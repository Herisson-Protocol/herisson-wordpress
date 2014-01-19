<?php
/**
 * HerissonModelTest
 *
 * PHP Version 5.3
 *
 * @category Test
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 */

namespace Herisson\Models;

require_once __DIR__."/../../Env.php";

/**
 * Class: HerissonModelTest
 *
 * @category Test
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      PHPUnit_Framework_TestCase
 */
class BaseTest extends \PHPUnit_Extensions_Database_TestCase
{

    /**
     * Table name
     */
    public $table;


    /**
     * Configuration
     *
     * Create sample data, and herisson demo website
     * Clean up sample url bookmarks
     *
     * @return void
     */
    protected function setUp()
    {
        $this->sampleName        = "Webpage example name";
        $this->sampleUrl         = "http://www.example.org";
        $this->sampleDescription = "Description example Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.";
        $this->sampleTag        = "loremipsum";

        $this->loadFixtures();

    }

    /**
     * Put back initial configuration
     *
     * Set options as they were 
     *
     * @return void
     */
    public function tearDown()
    {
    }

    /**
     * Load the SQL dump to initialize table state
     *
     * @return string the SQL fixtures file
     */
    protected function loadFixtures()
    {
        global $wpdb;
        $sql = file_get_contents($this->getFixtures());
        $sql = preg_replace("/#PREFIX#/", $wpdb->prefix, $sql);

        $this->getConnection()->execute($sql);
    }
 
    /**
     * Get the fixtures file name
     *
     * @return string the SQL fixtures file
     */
    protected function getFixtures()
    {
        return __DIR__."/../fixtures/".$this->table.".sql";
    }
 
    /**
     * Get the Doctrine connection
     *
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    public function getConnection()
    {
        $manager = Doctrine_Manager::getInstance();
        return $manager->getConnection('default');
    }
 
    /**
     * Dummy method to create the dataset, but this method is not used
     *
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    public function getDataSet()
    {
        $dataSet = new PHPUnit_Extensions_Database_DataSet_MySQLDataSet();
        return $this->createMySQLXMLDataSet('fixtures/bookmarks.xml');
    }



}


