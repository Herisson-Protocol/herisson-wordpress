<?php
/**
 * Folder tool
 * 
 * PHP Version 5.3
 *
 * @category Tools
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      None
 */

namespace Herisson;

/**
 * Class: Herisson\Folder
 *
 * This is a tools class with static methods
 *
 * @category Tools
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      None
 */
class Folder
{

    /**
     * Format a file/directory size
     *
     * @param integer $size the size to format
     *
     * @return the formatted size
     */
    public static function formatSize($size)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB');
        for ($i = 0; $size >= 1000 && $i < 4; $i++) {
            $size /= 1000;
        }
        return round($size, 2).' '.$units[$i];
    }


    /**
     * Get the recursive size of a folder
     *
     * @param string $dir the directory path
     *
     * @return the size of the directory
     */
    public static function getFolderSize($dir)
    {
        $size = Shell::shellExec('du', ' -sm '.$dir);
        $size = substr($size, 0, strpos($size, '	'));
        return $size;
    }

}



