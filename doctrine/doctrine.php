<?php
/**
 * Plugin Name: Doctrine ORM Integration
 * Plugin URI: http://www.flynsarmy.com/2010/02/integrating-doctrine-into-wordpress/
 * Description: This plugin enables Doctrine ORM 1.2.3 support in WordPress to easy development. You can get more information in <a href="http://www.doctrine-project.org/projects/orm/1.2/docs/en">Doctrine documentation</a>.
 * Author: Flynsarmy</a>, <a href="http://www.netinho.info/" title="Visitar a página de autores">Francisco Ernesto Teixeira
 * Author URI: http://www.flynsarmy.com/
 * Version: 0.3
 */

// constants of plugin
define( 'DOCTRINE_DSN', 'mysql://' . DB_USER . ':' . DB_PASSWORD . '@' . DB_HOST . '/' . DB_NAME );
define( 'DOCTRINE_MODELS_DIR', dirname( __FILE__ ) . '/models' );
define( 'DOCTRINE_SHORTCODES_DIR', dirname( __FILE__ ) . '/shortcodes' );
$GLOBALS['doctrine_models_folder_reset_processed'] = false;


/*
 * Doctrine Options Page
 */

/*
 * Load Doctrine library
 */
function herisson_doctrine_loadlibrary() {
    // load Doctrine library
    require_once dirname( __FILE__ ) . '/lib/Doctrine.php';
    require_once dirname( __FILE__ ) . '/count_files_in_dir.func.php';

    // this will allow Doctrine to load Model classes automatically
    spl_autoload_register( array( 'Doctrine', 'autoload' ) );

    Doctrine_Manager::connection( DOCTRINE_DSN, 'default' );

    herisson_doctrine_loadmodels();

    // (OPTIONAL) CONFIGURATION BELOW

    // load our shortcodes
    if ( is_dir( DOCTRINE_SHORTCODES_DIR ) ) {
        foreach ( glob( DOCTRINE_SHORTCODES_DIR . '/*.php' ) as $shortcode_file ) {
            require_once( $shortcode_file );
        }
    } else {
        mkdir( DOCTRINE_SHORTCODES_DIR, 0775 );
    }

    // this will allow us to use "mutators"
    Doctrine_Manager::getInstance()->setAttribute( Doctrine::ATTR_AUTO_ACCESSOR_OVERRIDE, true );

    // this sets all table columns to notnull and unsigned (for ints) by default
    Doctrine_Manager::getInstance()->setAttribute( Doctrine::ATTR_DEFAULT_COLUMN_OPTIONS,
        array( 'notnull' => true, 'unsigned' => true ) );

    // set the default primary key to be named 'id', integer, 20 bytes as default MySQL bigint
    Doctrine_Manager::getInstance()->setAttribute( Doctrine::ATTR_DEFAULT_IDENTIFIER_OPTIONS,
        array( 'name' => 'id', 'type' => 'integer', 'length' => 20 ) );

    // use of Alternative PHP Cache
    if ( function_exists( 'apc_cache_info' ) && ( get_option('doctrine_use_apc') != 'false' ) ) {
        $cacheDriver = new Doctrine_Cache_Apc();
        Doctrine_Manager::getInstance()->setAttribute( Doctrine::ATTR_QUERY_CACHE,
            $cacheDriver );
        Doctrine_Manager::getInstance()->setAttribute( Doctrine_Core::ATTR_RESULT_CACHE,
            $cacheDriver );
        Doctrine_Manager::getInstance()->setAttribute( Doctrine_Core::ATTR_RESULT_CACHE_LIFESPAN,
            (int) get_option( 'doctrine_apc_result_cache_lifespan' ) );
    }
}

/*
 * Generate and load all database models
 */
function herisson_doctrine_loadmodels() {
    // detect if model's folder exists and make if not
    if ( !is_dir( DOCTRINE_MODELS_DIR ) ) {
        mkdir( DOCTRINE_MODELS_DIR, 0775 );
    }

    // detect if models exists and generate if not
#    if ( count_files_in_dir( DOCTRINE_MODELS_DIR ) . '/*.php' ) {
#        Doctrine_Core::generateModelsFromDb( DOCTRINE_MODELS_DIR, array( 'default' ),
#            array( 'generateTableClasses' => true ) );
#    }

    // telling Doctrine where our models are located
    Doctrine::loadModels( DOCTRINE_MODELS_DIR . '/generated' );
    Doctrine::loadModels( DOCTRINE_MODELS_DIR );
}

herisson_doctrine_loadlibrary();
?>
