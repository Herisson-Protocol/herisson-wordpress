<?php
/**
 * Plugin Name: Doctrine ORM Integration
 * Plugin URI: http://www.flynsarmy.com/2010/02/integrating-doctrine-into-wordpress/
 * Description: This plugin enables Doctrine ORM 1.2.3 support in WordPress to easy development. You can get more information in <a href="http://www.doctrine-project.org/projects/orm/1.2/docs/en">Doctrine documentation</a>.
 * Author: Flynsarmy</a>, <a href="http://www.netinho.info/" title="Visitar a página de autores">Francisco Ernesto Teixeira
 * Author URI: http://www.flynsarmy.com/
 * Version: 0.3
 *
 * @category Model
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      None
 */

// constants of plugin
// define('HERISSON_DOCTRINE_DSN', 'mysql://' . DB_USER . ':' . DB_PASSWORD . '@' . DB_HOST . '/' . DB_NAME);
define('HERISSON_DOCTRINE_MODELS_DIR', dirname(__FILE__) . '/Models');
//define('HERISSON_DOCTRINE_SHORTCODES_DIR', dirname(__FILE__) . '/../vendor/doctrine/shortcodes');
$GLOBALS['doctrine_models_folder_reset_processed'] = false;


/**
 * Load Doctrine library
 *
 * @return void
 */
function herissonDoctrineLoadlibrary($dsn)
{
    // load Doctrine library
    include_once dirname(__FILE__) . '/../vendor/doctrine/lib/Doctrine.php';

    // this will allow Doctrine to load Model classes automatically
    spl_autoload_register(array('Doctrine', 'autoload'));

    Doctrine_Manager::connection($dsn, 'default');

    herissonDoctrineLoadmodels();

    // (OPTIONAL) CONFIGURATION BELOW

    /*
    // load our shortcodes
    if (is_dir(HERISSON_DOCTRINE_SHORTCODES_DIR)) {
        foreach (glob(HERISSON_DOCTRINE_SHORTCODES_DIR . '/*.php') as $shortcode_file) {
            require_once($shortcode_file);
        }
    } else {
        mkdir(HERISSON_DOCTRINE_SHORTCODES_DIR, 0775);
    }
    */

    // this will allow us to use "mutators"
    Doctrine_Manager::getInstance()->setAttribute(Doctrine::ATTR_AUTO_ACCESSOR_OVERRIDE, true);

    // this sets all table columns to notnull and unsigned (for ints) by default
    Doctrine_Manager::getInstance()->setAttribute(Doctrine::ATTR_DEFAULT_COLUMN_OPTIONS,
        array('notnull' => true, 'unsigned' => true));

    // set the default primary key to be named 'id', integer, 20 bytes as default MySQL bigint
    Doctrine_Manager::getInstance()->setAttribute(Doctrine::ATTR_DEFAULT_IDENTIFIER_OPTIONS,
        array('name' => 'id', 'type' => 'integer', 'length' => 20));

    // use of Alternative PHP Cache
    if (function_exists('apc_cache_info') && (get_option('doctrine_use_apc') != 'false')) {
        $cacheDriver = new Doctrine_Cache_Apc();
        Doctrine_Manager::getInstance()->setAttribute(Doctrine::ATTR_QUERY_CACHE,
            $cacheDriver);
        Doctrine_Manager::getInstance()->setAttribute(Doctrine_Core::ATTR_RESULT_CACHE,
            $cacheDriver);
        Doctrine_Manager::getInstance()->setAttribute(Doctrine_Core::ATTR_RESULT_CACHE_LIFESPAN,
            (int) get_option('doctrine_apc_result_cache_lifespan'));
    }
}

/**
 * Generate and load all database models
 *
 * @return void
 */
function herissonDoctrineLoadmodels()
{
    // detect if model's folder exists and make if not
    if (!is_dir(HERISSON_DOCTRINE_MODELS_DIR)) {
        mkdir(HERISSON_DOCTRINE_MODELS_DIR, 0775);
    }

    // detect if models exists and generate if not
    if (countFilesInDir(HERISSON_DOCTRINE_MODELS_DIR) . '/generated/*.php') {
        Doctrine_Core::generateModelsFromDb(HERISSON_DOCTRINE_MODELS_DIR, array('default'),
            array('generateTableClasses' => true));
    }

    // telling Doctrine where our models are located
    Doctrine::loadModels(HERISSON_DOCTRINE_MODELS_DIR . '/generated');
    Doctrine::loadModels(HERISSON_DOCTRINE_MODELS_DIR);
}

/**
 * Count the number of files in a directory
 *
 * @param mixed $dir the fullpath of the directory
 *
 * @return the number of files in the directory
 */
function countFilesInDir($dir)
{
    $gdir = glob($dir);
    return ($gdir != false) ? count($gdir) : 0;
}


/**
 * Delete a full directory, recursively
 *
 * @param mixed $dir the full path of the directory to delete
 *
 * @return true and false sometimes...
 */
function deltree($dir)
{
    if (!file_exists($dir)) {
        return true;
    }
    
    if (!is_dir($dir) || is_link($dir)) {
        return unlink($dir);
    }
    
    foreach (scandir($dir) as $item) {
        if (($item == '.') || ($item == '..')) {
            continue;
        }
        
        if (!deltree($dir . '/' . $item)) {
            chmod($dir . '/' . $item, 0777);
            if (!deltree($dir . '/' . $item)) {
                return false;
            }
        }
    }
    
    return rmdir($dir);
}



