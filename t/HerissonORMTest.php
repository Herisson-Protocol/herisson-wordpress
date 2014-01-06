<?php
/**
 * HerissonORMTest
 *
 * PHP Version 5.3
 *
 * @category Test
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 */

require_once __DIR__."/Env.php";

/**
 * Class: HerissonORMTest
 *
 * @category Test
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      PHPUnit_Framework_TestCase
 */
class HerissonORMTest extends PHPUnit_Extensions_Database_TestCase
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
        $this->sampleDescription = "Description example";
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
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    public function getConnection()
    {
        $manager = Doctrine_Manager::getInstance();
        return $manager->getConnection('default');
    }
 
    /**
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    public function getDataSet()
    {
        $dataSet = new PHPUnit_Extensions_Database_DataSet_MySQLDataSet();
        return $this->createMySQLXMLDataSet('fixtures/bookmarks.xml');
    }



}


