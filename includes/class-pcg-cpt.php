<?php
class PCG_CPT {
    public function __construct() {
        add_action( 'init', [ $this, 'register_cpt' ] );
    }

    public function register_cpt() {
        register_post_type( 'politeia_course_group', [
            'label' => 'Politeia Course Groups',
            'labels' => [
                'name' => 'Politeia Course Groups',
                'singular_name' => 'Politeia Course Group',
                'add_new' => 'Add Course Group',
                'add_new_item' => 'Add New Course Group',
                'edit_item' => 'Edit Course Group',
                'new_item' => 'New Course Group',
                'view_item' => 'View Course Group',
                'search_items' => 'Search Course Groups',
                'not_found' => 'No Course Groups found',
                'menu_name' => 'Politeia Course Groups'
            ],
            'public' => true,
            'has_archive' => true,
            'supports' => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
            'show_in_rest' => true,
            'menu_icon' => 'dashicons-welcome-learn-more',
            'rewrite' => [ 'slug' => 'course-groups' ],
        ]);
    }
}
