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
                continue;
            }
            printf( '<p>Geocoding location: "%s" for listing: "%d"</p>', $location, $listing->ID );

            mylisting()->geocoder()->save_location( $listing->ID, $location );
        }

        $offset = ( ! $offset ) ? $next_data : $offset + $next_data;
    } while( ! empty( $listings ) );

    exit('All listings are updated, you can close this window.');
}, 250 );
