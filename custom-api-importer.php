<?php
/**
 * Plugin Name: Custom API Importer
 * Description: A plugin that fetches data from various API sources and creates custom post types in WordPress.
 * Version: 1.0.0
 * Author: [Your Name]
 */

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

// Define plugin paths
define('CUSTOM_API_IMPORTER_PATH', plugin_dir_path(__FILE__));
define('CUSTOM_API_IMPORTER_URL', plugin_dir_url(__FILE__));

// Custom Post Type settings
define('CUSTOM_API_CPT_SLUG', 'majoitus');
define('CUSTOM_API_CPT_NAME', 'Majoitukset');
define('CUSTOM_API_CPT_SINGULAR_NAME', 'Majoitus');

function custom_api_importer_activate() {
    // Register CPT upon activation
    custom_api_register_cpt();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'custom_api_importer_activate');

function custom_api_importer_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'custom_api_importer_deactivate');

// Register Custom Post Type
function custom_api_register_cpt() {
    $labels = array(
        'name' => __(CUSTOM_API_CPT_NAME, 'custom-api-importer'),
        'singular_name' => __(CUSTOM_API_CPT_SINGULAR_NAME, 'custom-api-importer')
    );

    $args = array(
        'label' => __(CUSTOM_API_CPT_NAME, 'custom-api-importer'),
        'public' => true,
        'show_ui' => true,
        'supports' => array('title', 'editor'),
        'rewrite' => array('slug' => CUSTOM_API_CPT_SLUG)
    );

    register_post_type(CUSTOM_API_CPT_SLUG, $args);
}
add_action('init', 'custom_api_register_cpt');

// Add settings page to admin menu
function custom_api_importer_add_admin_menu() {
    add_menu_page(
        'API Importer Settings', 
        'API Importer', 
        'manage_options', 
        'custom_api_importer', 
        'custom_api_importer_settings_page'
    );
}
add_action('admin_menu', 'custom_api_importer_add_admin_menu');

// Render settings page
function custom_api_importer_settings_page() {
    ?>
    <div class="wrap">
        <h1>API Importer Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('custom_api_importer_options');
            do_settings_sections('custom_api_importer');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Initialize settings
function custom_api_importer_settings_init() {
    register_setting('custom_api_importer_options', 'custom_api_importer_api_key');

    add_settings_section(
        'custom_api_importer_section',
        __('API Settings', 'custom-api-importer'),
        null,
        'custom_api_importer'
    );

    add_settings_field(
        'custom_api_importer_api_key',
        __('API Key', 'custom-api-importer'),
        'custom_api_importer_api_key_render',
        'custom_api_importer',
        'custom_api_importer_section'
    );
}
add_action('admin_init', 'custom_api_importer_settings_init');

// Render API Key field
function custom_api_importer_api_key_render() {
    $value = get_option('custom_api_importer_api_key', '');
    echo "<input type='text' name='custom_api_importer_api_key' value='" . esc_attr($value) . "' size='50' />";
}
