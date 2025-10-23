<?php
/**
 * Plugin Name: Politeia Course Group
 * Description: Crea el nivel superior de “Programas Filosóficos” que agrupa grupos de cursos LearnDash.
 * Author: Nico / Politeia
 * Version: 1.0.0
 * Text Domain: politeia-course-group
 * Codex Enabled: true
 * Codex Enabled: true
 * Codex Enabled: true
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'PCG_PATH', plugin_dir_path( __FILE__ ) );
define( 'PCG_URL', plugin_dir_url( __FILE__ ) );

/**
 * Autoload classes
 */
spl_autoload_register( function ( $class ) {
    if ( strpos( $class, 'PCG_' ) === 0 ) {
        $file = PCG_PATH . 'includes/class-' . strtolower( str_replace( '_', '-', $class ) ) . '.php';
        if ( file_exists( $file ) ) require_once $file;
    }
});

/**
 * Initialize
 */
add_action( 'plugins_loaded', function() {
    // Register CPT
    new PCG_CPT();

    // Register ACF fields (if ACF active)
    if ( class_exists('ACF') ) {
        new PCG_ACF();
    }

    // Relations and templates
    new PCG_Relations();
    new PCG_Templates();

    // REST endpoints
    new PCG_REST();

    // Admin menu
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-pcg-admin.php';
    if ( class_exists( 'PCG_Admin_Menu' ) ) {
        new PCG_Admin_Menu();
    }
});

/**
 * Enqueue assets
 */
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style( 'pcg-style', PCG_URL . 'assets/css/pcg-style.css', [], '1.0' );
    wp_enqueue_script( 'pcg-script', PCG_URL . 'assets/js/pcg-script.js', ['jquery'], '1.0', true );
});
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/codex/init.php';
