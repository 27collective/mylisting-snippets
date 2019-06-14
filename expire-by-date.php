<?php
add_action( 'mylisting/schedule:hourly', function() {
    global $wpdb;

    // Change status to expired.
    $listing_ids = $wpdb->get_col(
        $wpdb->prepare( "
            SELECT postmeta.post_id FROM {$wpdb->postmeta} as postmeta
            LEFT JOIN {$wpdb->posts} as posts ON postmeta.post_id = posts.ID
            WHERE postmeta.meta_key = '_job_date'
            AND postmeta.meta_value > 0
            AND postmeta.meta_value < %s
            AND posts.post_status = 'publish'
            AND posts.post_type = 'job_listing'",
            date( 'Y-m-d', current_time( 'timestamp' ) )
        )
    );

    if ( $listing_ids ) {
        foreach ( $listing_ids as $listing_id ) {
            $data                = [];
            $data['ID']          = $listing_id;
            $data['post_status'] = 'expired';
            wp_update_post( $data );
        }
    }
} );
