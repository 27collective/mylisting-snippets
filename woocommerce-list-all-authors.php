// List all users in the Author dropdown when editing a WooCommerce product in admin backend.
add_filter( 'wp_dropdown_users', function( $output ) {
    global $post, $user_ID;

    if ( empty( $post ) ) return $output;

    // Return if this isn't a product.
    if ( $post->post_type !== 'product' ) return $output;

    // return if this isn't the theme author override dropdown
    if (!preg_match( '/post_author_override/', $output )) return $output;

    // return if we've already replaced the list (end recursion)
    if (preg_match( '/post_author_override_replaced/', $output )) return $output;

    // replacement call to wp_dropdown_users
    $output = wp_dropdown_users( [
        'echo' => 0,
        'name' => 'post_author_override_replaced',
        'selected' => empty($post->ID) ? $user_ID : $post->post_author,
        'include_selected' => true
    ] );

    // put the original name back
    $output = preg_replace('/post_author_override_replaced/', 'post_author_override', $output);

    return $output;
} );
