<?php
/**
 * Functions and definitions
 *
 * @package WordPress
 * @subpackage Tower
 */

/**
 * Custom Post Type Schema
 * 
 * This constant represents the 2 custom post types:
 * 1. Insurance Policies
 * 2. Insurance Claims
 * 
 * Each post type has 3 items as follows:
 * 1. config - as required to register custom posts [`register_post_type`]
 * 2. fields - this lists all the required fields for the custom post type.
 *              *Using the default `title` field in each post types.
 * 3. meta - as required to register custom meta boxes [`add_meta_box`]
 */
define('TOWER_CUSTOM_POSTS', [
    'tower_policies' => [
        'config' => [
            'labels' => [
                'name' => 'Insurance Policies',
                'menu_name' => 'Policies',
                'singular_name' => 'Policy',
            ],
            'description' => 'All insurance policies submissions.',
            'public' => true,
            'has_archive' => true,
            'rewrite' => [
                'slug' => 'policies',
            ],
            'show_in_rest' => true,
            'supports' => ['title'],
        ],
        'fields' => [
            // Page title : Policy Name
            [
                'name' => 'policy-id',
                'title' => 'Policy ID',
                'description' => 'Policy ID number',
                'type' => 'number',
                'scope' => ['tower_claims', 'tower_policies'],
                'capability' => 'edit_posts',
                'rules' => 'required|number|unique',
            ],
            [
                'name' => 'policy-date',
                'title' => 'Date',
                'description' => 'Policy Live Date',
                'type' => 'date',
                'scope' => ['tower_policies'],
                'capability' => 'edit_posts',
                'rules' => 'required|date',
            ],
            [
                'name' => 'policy-description',
                'title' => 'Description',
                'description' => 'Description about the policy',
                'type' => 'textarea',
                'scope' => ['tower_policies'],
                'capability' => 'edit_posts',
                'rules' => 'nullable',
            ],
        ],
        'meta' => [
            'handle' => 'tower-policies-group',
            'title' => 'Policy',
            'callback' => 'tower_policies_callback',
        ],
    ],
    'tower_claims' => [
        'config' => [
            'labels' => [
                'name' => 'Insurance Claims',
                'menu_name' => 'Claims',
                'singular_name' => 'Claim',
            ],
            'description' => 'All insurance claim submissions.',
            'public' => true,
            'has_archive' => true,
            'rewrite' => [
                'slug' => 'claims',
            ],
            'show_in_rest' => true,
            'supports' => ['title'],
        ],
        'fields' => [
            // Page title : Holder's Name
            [
                'name' => 'policy-id',
                'title' => 'Policy ID',
                'description' => 'Policy ID number',
                'type' => 'number',
                'scope' => ['tower_claims', 'tower_policies'],
                'capability' => 'edit_posts',
                'rules' => 'required|number',
            ],
            [
                'name' => 'policy-email',
                'title' => 'Email',
                'description' => 'Email of the policy holder',
                'type' => 'email',
                'scope' => ['tower_claims'],
                'capability' => 'edit_posts',
                'rules' => 'required|email',
            ]
        ],
        'meta' => [
            'handle' => 'tower-claims-group',
            'title' => 'Claims',
            'callback' => 'tower_claims_callback',
        ],
    ]
]);

// Register custom post types (cpt)
function tower_custom_post_types() {
    foreach (TOWER_CUSTOM_POSTS as $key => $custom_type)
        register_post_type($key, $custom_type['config']);
}
add_action('init', 'tower_custom_post_types');

/**
 * 1. Create meta boxes
 * 2. Handle data validation
 * 3. Persisting custom fields to database
 * 4. Retrieving the custom fields
 */

// Register meta boxes for each custom post types
function tower_register_meta_boxes() {
    foreach (TOWER_CUSTOM_POSTS as $key => $custom_type) {
        $meta = $custom_type['meta'];
        add_meta_box(
            $meta['handle'],
            $meta['title'],
            $meta['callback'],
            $key
        );
    }
}
add_action('add_meta_boxes', 'tower_register_meta_boxes');

// Callback function for "Policies" CPT
function tower_policies_callback($post) {
    tower_build_meta_box(
        $post, 
        TOWER_CUSTOM_POSTS['tower_policies']['fields']
    );
}

// Callback function for "Claims" CPT type
function tower_claims_callback($post) {
    tower_build_meta_box(
        $post, 
        TOWER_CUSTOM_POSTS['tower_claims']['fields']
    );
}

/**
 * Save meta box content
 * 
 * @param int $post_id POST ID
 */
function tower_save_meta_box($post_id) {
    // Check if the post_type is correct
    if (! array_key_exists(get_post_type($post_id), TOWER_CUSTOM_POSTS)) return $post_id;

    // Check if nonce is set
    if (! isset($_POST['tower_nonce'])) return $post_id;

    // Verify that nonce is valid
    if (! wp_verify_nonce($_POST['tower_nonce'], basename(__FILE__))) return $post_id;

    // For Autosave, dont proceed
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;

    // Check data validation
    foreach (TOWER_CUSTOM_POSTS['tower_policies']['fields'] as $field) {
        update_post_meta(
            $post_id, 
            $field['name'], 
            sanitize_text_field($_POST[$field['name']])
        );
    }
}
add_action('save_post', 'tower_save_meta_box');

// This will build the meta box for the custom posts in WP admin
function tower_build_meta_box($post, $fields) {
    // include nonce!
    wp_nonce_field(basename(__FILE__), 'tower_nonce');

    if (! is_array($fields)) return;

    echo '<table style="width: 100%">';
    foreach ($fields as $field) {
        // find value
        $field_value = esc_html(get_post_meta($post->ID, $field['name'], true));
        $html = '';
        switch ($field['type']) {
            case 'textarea':
                $html = "<textarea name='{$field['name']}' 
                    id='{$field['name']}' 
                    rows='5' 
                    placeholder='{$field['description']}'
                    style='width:100%;' 
                    maxlength='1200'
                    required
                >{$field_value}</textarea>";
                break;

            default:
                $html = "<input 
                    type='{$field['type']}' 
                    name='{$field['name']}' 
                    id='{$field['name']}' 
                    placeholder='{$field['description']}'
                    maxlength='250'
                    value='{$field_value}'
                    required />";
        }
        echo <<<EOL
        <tr>
            <td style="vertical-align: top; background: #FEFEFE; padding: 5px 10px;"><label for="{$field['name']}">{$field['title']}</label></td>
            <td>{$html}</td>
        </tr>
        EOL;
    }
    echo '</table/>';
}

// Change default entry title for our custom post types
function change_default_title($title) {
    $screen = get_current_screen();
    switch ($screen->post_type) {
        case 'tower_claims':
            return 'Policy Holder Name';
            break;
        case 'tower_policies':
            return 'Policy Name';
            break;
    }
    return $title;
}
add_filter( 'enter_title_here', 'change_default_title' );

 // Enqueue styles
function tower_register_styles() {

    $version = wp_get_theme()->get('Version');

    // (handle, path, dependencies[id], version, media: 'all')
    wp_enqueue_style(
        'tower-style', 
        get_template_directory_uri() . '/style.css', 
        ['tower-bootstrap'], 
        $version
    );
    wp_enqueue_style(
        'tower-bootstrap', 
        'https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css', 
        [], 
        '4.4.1'
    );
    wp_enqueue_style(
        'tower-fontawesome', 
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css', 
        [], 
        '5.13.0' 
    );
}
add_action('wp_enqueue_scripts', 'tower_register_styles');

// Enqueue scripts
function tower_register_scripts() {
    
    // (handle, path, dependencies[handle], version, inFooter: false)
    wp_enqueue_script(
        'tower-main', 
        get_template_directory_uri() . '/assets/js/main.js', 
        ['tower-bootstrap'], 
        '1.0', 
        true
    );
    wp_enqueue_script(
        'tower-jquery',
        'https://code.jquery.com/jquery-3.4.1.min.js',
        [],
        '3.4.1',
        true
    );
    wp_enqueue_script(
        'tower-popper',
        'https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js',
        ['tower-jquery'],
        '1.16.0',
        true
    );
    wp_enqueue_script(
        'tower-bootstrap',
        'https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js',
        ['tower-jquery'],
        '4.4.1',
        true
    );

    // // 
    // wp_localize_script('tower-main', 'nonce', [
    //     'mood' => 'happy',
    // ]);
}
add_action('wp_enqueue_scripts', 'tower_register_scripts');

// Create custom API endpoint to load claims form
function tower_claims_form() {
    ob_start();
    include('template-parts/form-claim.php');
    $html = ob_get_contents();
    ob_end_clean();
    return $html;
}
add_action('rest_api_init', function() {
    register_rest_route('tower-forms/v1', 'claims', [
        'methods' => 'GET',
        'callback' => 'tower_claims_form',
    ]);
});
