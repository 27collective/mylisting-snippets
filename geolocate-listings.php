add_action( 'init', function() {
    if ( empty( $_GET['geolocate_listings'] ) || ! current_user_can( 'administrator' ) ) {
        return;
    }

    $next_data = 50;
    $offset = 0;

    do {
        $listings = (array) get_posts( [
            'post_type' => 'job_listing',
            'offset'   => $offset,
            'posts_per_page' => $next_data,
            'post_status' => ['publish', 'private', 'expired'],
            'meta_query' => [
                'relation' => 'OR',
                [ 'key' => 'geolocation_lat', 'value' => '' ],
                [ 'key' => 'geolocation_long', 'value' => '' ],
                [ 'key' => 'geolocation_lat', 'compare' => 'NOT EXISTS' ],
                [ 'key' => 'geolocation_long', 'compare' => 'NOT EXISTS' ],
            ],
        ] );

        printf(
            "Fetching geolocation data from listing %d to %d <br />",
            $offset + 1,
            $offset + $next_data
        );

        flush();
        ob_flush();

        foreach ( $listings as $listing ) {
            if ( ! ( $location = get_post_meta( $listing->ID, '_job_location', true ) ) ) {
                printf( '<p style="color: #8e8e8e;">Skipping geolocation for listing #%d (missing address)</p>', $listing->ID );
                continue;
            }

            $geocoded = mylisting()->geocoder()->save_location( $listing->ID, $location );
            if ( $geocoded !== false ) {
                printf( '<p style="color: green;">Geolocation successful for listing #%d (%s)</p>', $listing->ID, $location );
                continue;
            }

            printf( '<p style="color: red;">Failed to geolocate listing #%d (%s)</p>', $listing->ID, $location );
        }

        $offset = ( ! $offset ) ? $next_data : $offset + $next_data;
    } while( ! empty( $listings ) );

    exit('All listings are updated, you can close this window.');
}, 250 );
