<?php

use Roots\Acorn\Application;

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our theme. We will simply require it into the script here so that we
| don't have to worry about manually loading any of our classes later on.
|
*/

if (! file_exists($composer = __DIR__.'/vendor/autoload.php')) {
    wp_die(__('Error locating autoloader. Please run <code>composer install</code>.', 'sage'));
}

require $composer;

/*
|--------------------------------------------------------------------------
| Register The Bootloader
|--------------------------------------------------------------------------
|
| The first thing we will do is schedule a new Acorn application container
| to boot when WordPress is finished loading the theme. The application
| serves as the "glue" for all the components of Laravel and is
| the IoC container for the system binding all of the various parts.
|
*/

Application::configure()
    ->withProviders([
        App\Providers\ThemeServiceProvider::class,
    ])
    ->boot();

/*
|--------------------------------------------------------------------------
| Register Sage Theme Files
|--------------------------------------------------------------------------
|
| Out of the box, Sage ships with categorically named theme files
| containing common functionality and setup to be bootstrapped with your
| theme. Simply add (or remove) files from the array below to change what
| is registered alongside Sage.
|
*/

collect(['setup', 'filters'])
    ->each(function ($file) {
        if (! locate_template($file = "app/{$file}.php", true, true)) {
            wp_die(
                /* translators: %s is replaced with the relative file path */
                sprintf(__('Error locating <code>%s</code> for inclusion.', 'sage'), $file)
            );
        }
    });

    
    /*
    |--------------------------------------------------------------------------
    | Custom Post types
    |--------------------------------------------------------------------------
    |
    | Custom post types so we can differentiate content
    |
    */
        
    add_action( 'init', 'register_team' );
    function register_team() {
        register_post_type( 'team',
            array(
                'labels' => array(
                    'name' => __( 'Team' ),
                    'singular_name' => __( 'Person' )
                ),
                'hierarchical' => true,
                'show_in_nav_menus' => false,
                'publicly_queryable' => false,
                //'rewrite' => array( 'slug' => 'team' ),
                'public' => true,
                'has_archive' => false,
                'supports' => array( 'title','thumbnail','editor','page-attributes' ),
            )
        );
    }
        
    /*
    |--------------------------------------------------------------------------
    | Custom Fields
    |--------------------------------------------------------------------------
    |
    | Custom fields for the theme
    |
    */
    
    use Carbon_Fields\Container;
    use Carbon_Fields\Field;
    
    #add_filter( 'carbon_fields_theme_options_container_admin_only_access', '__return_false' );

    
    add_action( 'carbon_fields_register_fields', __NAMESPACE__ . '\\crb_attach_theme_options' );    
    function crb_attach_theme_options(){
        $basic_options_container = Container::make( 'theme_options', __( 'Sites Options' ) )
        ->add_fields( array(
            Field::make( 'text', 'crb_gtm_id', __( 'Google Tag Manager ID' ) ),
            Field::make( 'text', 'crb_google_site_verification', __( 'Google Site Verification ID' ) ),
            Field::make( 'text', 'crb_recaptcha_client_key', __( 'Google Recaptcha - Client Key' ) ),
            Field::make( 'text', 'crb_recaptcha_secret_key', __( 'Google Recaptcha - Secret Key' ) )
        ) );

        Container::make( 'theme_options', __( 'Site Options' ) )
        ->set_page_parent( $basic_options_container ) 
        ->set_page_menu_title( 'API settings' )
        ->add_fields( array(
    
        ) );
    }

    add_action( 'carbon_fields_register_fields', __NAMESPACE__ . '\\crb_attach_custom_fields' );
    function crb_attach_custom_fields() {
        Container::make( 'post_meta', 'Custom Data' )
        ->where( 'post_type', '=', 'page' )
        ->add_fields( array(
            Field::make( 'color', 'crb_page_theme', 'Page Theme Color' )
                ->set_palette( array( '#FF0000', '#00FF00', '#0000FF' ) )
        ));
    }    

    add_action( 'after_setup_theme', 'crb_load' );
    function crb_load() {
        require_once( 'vendor/autoload.php' );
        \Carbon_Fields\Carbon_Fields::boot();
    }