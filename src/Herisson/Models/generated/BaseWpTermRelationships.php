<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('WpTermRelationships', 'default');

/**
 * BaseWpTermRelationships
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $object_id
 * @property integer $term_taxonomy_id
 * @property integer $term_order
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseWpTermRelationships extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('wp_term_relationships');
        $this->hasColumn('object_id', 'integer', 8, array(
             'type' => 'integer',
             'length' => 8,
             'fixed' => false,
             'unsigned' => true,
             'primary' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('term_taxonomy_id', 'integer', 8, array(
             'type' => 'integer',
             'length' => 8,
             'fixed' => false,
             'unsigned' => true,
             'primary' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('term_order', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => '0',
             'notnull' => true,
             'autoincrement' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        
    }
}