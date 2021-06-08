<?php
/**
 * Functions and definitions
 *
 * @package WordPress
 * @subpackage Tower
 */

/**
 * Custom Post Types
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
 * 4. placeholder - As a replacement for the form `title` field
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
                'type' => 'text',
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
                'id' => 'description',
                'rules' => 'nullable',
            ],
        ],
        'meta' => [
            'handle' => 'tower-policies-group',
            'title' => 'Policy',
            'callback' => 'tower_policies_callback',
        ],
        'placeholder' => 'Policy Name',
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
        'placeholder' => 'Policy Holder\'s Name',
    ]
]);

// Register custom post types (cpt)
function tower_custom_post_types() {
    foreach (TOWER_CUSTOM_POSTS as $key => $custom_type) {
        register_post_type($key, $custom_type['config']);
    }
}
add_action('init', 'tower_custom_post_types');

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

// Callback function for "Claims" CPT
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
    // include nonce
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
                    maxlength='1500'
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
            <td style="vertical-align: top; background: #FEFEFE; padding: 5px 10px;">
                <label for="{$field['name']}">{$field['title']}</label>
            </td>
            <td>{$html}</td>
        </tr>
        EOL;
    }
    echo '</table/>';
}

// Custom validation rules for custom filds
function tower_custom_input_validation($message, $field, $request_data, $form_location) {

}
add_action('wppb_check_form_field_input', 'tower_custom_input_validation');

// Change default entry title for our custom post types
function change_default_title($title) {
    $screen = get_current_screen();
    $post_type = $screen->post_type;
    if (array_key_exists($post_type, TOWER_CUSTOM_POSTS))
        return @TOWER_CUSTOM_POSTS[$post_type]['placeholder'];
    return $title;
}
add_filter( 'enter_title_here', 'change_default_title' );

// persist form data along with custom meta fields
function tower_save_form_data($data) {
    // logic for data validation and saving.
    $post_type = 'tower_' . @$data->get_param('type');
    if (! array_key_exists($post_type, TOWER_CUSTOM_POSTS)) return null;

    // backend validation
    $fields = array_merge([
        'name' => 'title',
        'rules' => 'required',
    ], TOWER_CUSTOM_POSTS[$post_type]['fields']);
    
    // check if the form has any validation errors
    $validation_errors = tower_form_errors($data, $fields);

    $post_id = 0; // id for the new post
    
    if (count($validation_errors) == 0) {
        $post_id = wp_insert_post([
            'post_type' => $post_type,
            'post_title' => $data->get_param('title')
        ]);
        if ($post_id > 0) {
            foreach ($fields as $field) {
                update_post_meta(
                    $post_id, 
                    $field['name'], 
                    sanitize_text_field($data->get_param($field['name']))
                );
            }
        }
    }
    
    echo json_encode([
        'success' => $post_id > 0,
        'errors' => $validation_errors,
    ]);
}

// validate the post input data and return errors []
function tower_form_errors($data, $fields=[]) {
    $form_errors = [];
    foreach ($fields as $field) {
        $rules = explode('|', $field['rules']);
        $field_is_valid = true;
        $field_name = $field['name'];
        $value = trim(sanitize_text_field($data->get_param($field_name)));

        foreach ($rules as $rule) {
            if (! $field_is_valid) break;

            switch ($rule) {
                case 'email':
                    if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $field_is_valid = false;
                        $form_errors[$field_name] = 'Enter a valid email address';
                    }
                    break;

                case 'number':
                    if (! is_numeric($value)) {
                        $field_is_valid = false;
                        $form_errors[$field_name] = 'This field needs to be numeric';
                    }
                    break;

                case 'required':
                    if ($value == '') {
                        $field_is_valid = false;
                        $form_errors[$field_name] = 'This field is required';
                    }
                    break;

                case 'unique':
                    // check DB for meta-value duplicate entry
                    global $wpdb;
                    $row_count = $wpdb->get_var("
                        SELECT COUNT(`meta_id`)
                            FROM {$wpdb->postmeta} 
                                WHERE `meta_key` = '{$field_name}' 
                                AND `meta_value` = '{$value}'
                    ");
                    if ($row_count > 0) {
                        $field_is_valid = false;
                        $form_errors[$field_name] = 'This value already exists. Please select another';
                    }
                    break;
            }
        }
    }
    return $form_errors;
}

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
}
add_action('wp_enqueue_scripts', 'tower_register_scripts');

// this returns HTML for the Claims form
function tower_claims_form() {
    ob_start();
    include('template-parts/form-claim.php');
    $html = ob_get_contents();
    ob_end_clean();
    return $html;
}

// Register custom REST API endpoints
add_action('rest_api_init', function() {
    // Load claims form 
    register_rest_route('tower-forms/v1', 'claims', [
        'methods' => 'GET',
        'callback' => 'tower_claims_form',
    ]);
    // Persist form data
    register_rest_route('tower-forms/v1', 'form', [
        'methods' => 'POST',
        'callback' => 'tower_save_form_data',
    ]);
});
