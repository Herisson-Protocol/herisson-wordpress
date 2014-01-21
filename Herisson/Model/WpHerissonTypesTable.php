<?php
/**
 * WpHerissonTypesTable
 * 
 * PHP Version 5.3
 * 
 * @category Models
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 */

namespace Herisson\Model;

use Doctrine_Query;
use Doctrine_Table;
use Doctrine_Core;

/**
 * WpHerissonTypesTable
 *
 * ORM class to handle Bookmarks Table
 * 
 * @category Models
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 */
class WpHerissonTypesTable extends Doctrine_Table
{
    /**
     * Returns an instance of this class.
     *
     * @return object WpHerissonTypesTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('WpHerissonTypes');
    }
}

