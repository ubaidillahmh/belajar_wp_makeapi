<?php

    add_action('init', 'create_api');

    function create_api()
    {
        register_post_type( 'wt9-testimonial', 
            array(
                'labels' => array(
                    'name'          => 'Testimonial',
                    'singular_name' => 'Testimonial',
                    'add_new'       => 'Add New',
                    'add_new_item'  => 'Add New Testimonial',
                    'edit'          => 'Edit',
                    'edit_item'     => 'Edit Testimonial',
                    'new_item'      => 'New Testimonial',
                    'view'          => 'View',
                    'view_item'     => 'View Testimonial',
                    'search_item'   => 'Search Testimonial',
                    'not_found'     => 'No Testimonial Found',
                    'not_found_in_trash' => 'No Testimonial Found in Trash',
                    'parent'        => 'Parent Testimonial',   
                ),
                'public'    => true,
                'menu_position' => 5,
                'supports'  => array( 'title', 'editor', 'comments', 'thumbnail', 'custom-fields' ),
                'taxonomies'=> array( '' ),
                'menu_icon' => 'dashicons-format-aside',
                'has_archive' => true
            ) 
        );
    }

?>