// Update meta key name, preserving it's value.
add_action( 'init', function() {
    $old_key = 'year';
    $new_key = 'listing-year';

    if ( empty( $_GET['update-key'] ) || ! current_user_can( 'administrator' ) ) {
        return false;
    }

    $listings = (array) get_posts( [
        'post_type' => 'job_listing',
        'posts_per_page' => -1,
        'post_status' => 'any',
    ] );

    foreach ( $listings as $listing ) {
        if ( $meta_value = get_post_meta( $listing->ID, '_'.$old_key, true ) ) {
            update_post_meta( $listing->ID, '_'.$new_key, $meta_value );
        }
    }
} );
