<?php
/**
 * Herisson\Shell
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
 * Herisson\Shell
 * 
 * Library for shell tools using static methods
 * 
 * @category Tools
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      None
 */
class Shell
{

    /**
     * Run a shell exec call
     *
     * @param string $binary  the binary to call
     * @param string $options the options to pass to binary
     *
     * @return the shell output
     */
    public static function shellExec($binary, $options)
    {

        if (preg_match("#/#", $binary)) {
            $fullBinary = $binary;
        } else {
            $fullBinary = self::getPath($binary);
        }
        // echo "$binary -> $fullBinary<br>";
        if (file_exists($fullBinary) && is_executable($fullBinary)) {
            $herissonOptions = get_option('HerissonOptions');
            if ($herissonOptions['debugMode']) {
                Herisson\Message::i()->addSucces($fullBinary." ".$options);
            }
            exec($fullBinary." ".$options, $output);
            return implode("\n", $output);
        }
        return false;
    }

    /**
     * Get the path of the given binary
     *
     * This methods uses `which` to get the full path of the binary
     *
     * @param string $binary the binary to get to path to
     *
     * @return the full path of the binary
     */
    public static function getPath($binary)
    {
        exec("which $binary", $output);
        return implode("\n", $output);
    }

}

