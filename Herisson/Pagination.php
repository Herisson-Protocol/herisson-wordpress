<?php
/**
 * Pagination 
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
 * Class: Herisson\Pagination
 *
 * @category Tools
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      None
 */
class Pagination
{

    /**
     * Singleton object
     *
     * @var Herisson\Pagination
     */
    public static $i;

    /**
     * Creating singleton
     *
     * @return Herisson\Pagination instance
     */
    public static function i()
    {
        if (is_null(self::$i)) {
            self::$i = new Pagination();
        }
        return self::$i;
    }

    /**
     * retrieve pagination parameters
     *
     * @return array with 2 parameters : offset (current pagination offset),
     *         and limit (maximum items per pages)
     */
    public static function getVars()
    {
        $options = get_option('HerissonOptions');
        return array(
            'offset'    => param('offset'),
            'page'      => param('page'),
            'limit'     => $options['bookmarksPerPage'],
        );
    }





}


