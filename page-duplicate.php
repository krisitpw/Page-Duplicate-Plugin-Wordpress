<?php
/*
Plugin Name: Page Duplicate
Description: Add the ability to duplicate pages in WordPress.
Version: 1.0
Author: Krishna Singh
*/

// Your plugin code will go here.
function page_duplicate_add_duplicate_link($actions, $post) {
    if ('page' == $post->post_type) {
        $actions['duplicate_page'] = '<a href="' . admin_url("post-new.php?post_type=page&clone_id={$post->ID}") . '">Duplicate Page</a>';
    }
    return $actions;
}

add_filter('page_row_actions', 'page_duplicate_add_duplicate_link', 10, 2);

function page_duplicate_create_duplicate($post_id) {
    if (isset($_GET['clone_id']) && is_numeric($_GET['clone_id'])) {
        $clone_id = (int)$_GET['clone_id'];
        $original_post = get_post($clone_id);
        if (!empty($original_post)) {
            $post = array(
                'post_title' => $original_post->post_title . ' (Copy)',
                'post_content' => $original_post->post_content,
                'post_status' => $original_post->post_status,
                'post_type' => $original_post->post_type,
                'post_author' => get_current_user_id(),
            );
            $new_post_id = wp_insert_post($post);
            if ($new_post_id) {
                // Optionally, you can copy any custom fields here if needed.
                // Example: $custom_fields = get_post_custom($clone_id);
                // foreach ($custom_fields as $key => $value) {
                //     update_post_meta($new_post_id, $key, $value[0]);
                // }
                // Redirect to the new page's edit screen.
                wp_redirect(admin_url("post.php?action=edit&post=$new_post_id"));
                exit;
            }
        }
    }
}

add_action('admin_init', 'page_duplicate_create_duplicate');

