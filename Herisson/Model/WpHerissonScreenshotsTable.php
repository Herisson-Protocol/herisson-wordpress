<?php
/**
 * WpHerissonScreenshotsTable
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
 * WpHerissonScreenshotsTable
 * 
 * @category Models
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 */
class WpHerissonScreenshotsTable extends Doctrine_Table
{
    /**
     * Returns an instance of this class.
     *
     * @return object WpHerissonScreenshotsTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('WpHerissonScreenshots');
    }

    /**
     * Get all screenshots tools
     *
     * @return array the list of screenshots tools
     */
    public static function getAll()
    {
        $screenshots = Doctrine_Query::create()
            ->from('Herisson\Model\WpHerissonScreenshots')
            ->orderby("id")
            ->execute();
        return $screenshots;
    }

    /**
     * Get one screenshots tool from id
     *
     * @param integer $id the id of the screenshot tool
     *
     * @return mixed the screenshot tool object
     */
    public static function get($id)
    {
        if (!is_numeric($id)) {
            return new WpHerissonScreenshots();
        }
        $screenshots = Doctrine_Query::create()
            ->from('Herisson\Model\WpHerissonScreenshots')
            ->where("id=?")
            ->execute(array($id));
        foreach ($screenshots as $screenshot) {
            return $screenshot;
        }
        return new WpHerissonScreenshots();
    }

}

