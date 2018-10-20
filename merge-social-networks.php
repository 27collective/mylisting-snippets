<?php

add_action( 'init', function() {
    if ( empty( $_GET['import-social-networks'] ) || ! current_user_can( 'administrator' ) ) {
        return false;
    }

    $listings = (array) get_posts([
        'post_type' => 'job_listing',
        'posts_per_page' => -1,
        'post_status' => 'any',
    ]);

	// Add other social networks to this list.
    $fields = [
        'Facebook' => '_links_facebook',
        'Instagram' => '_links_instagram',
        'Twitter' => '_links_twitter',
    ];

    foreach ($listings as $listing) {
        $social_networks = [];
        foreach ($fields as $network => $field) {
            if ( $value = $listing->$field ) {
                $social_networks[] = [
                    'network' => $network,
                    'url' => $value,
                ];
            }
        }

        update_post_meta( $listing->ID, '_links', $social_networks );
    }
} );
