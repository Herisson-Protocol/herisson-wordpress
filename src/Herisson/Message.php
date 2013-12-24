<?php
/**
 * HerissonMessage 
 *
 * @category Tools
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      None
 */


/**
 * Class: HerissonMessage
 *
 * @category Tools
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      None
 */
class HerissonMessage
{

    /**
     * singleton
     * @var HerissonMessage
     */
    public static $i;

    /**
     * Array of the currently known error in the page.
     *
     * Errors are strings
     */
    protected $errors;

    /**
     * Array of the currently known successful items in the page.
     *
     * Successes are strings
     */
    protected $success;

    /**
     * Creating singleton
     *
     * @return HerissonMessage instance
     */
    public static function i()
    {
        if (is_null(self::$i)) {
            self::$i = new HerissonMessage();
            self::$i->errors = array();
            self::$i->success = array();
        }
        return self::$i;
    }

    /**
     * Get the errors array
     *
     * @return the array of errors
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Add an error to the errors array
     *
     * @param string $message the error message
     *
     * @return void
     */
    public function addError($message)
    {
        array_push($this->errors, $message);
    }

    /**
     * Check if there is any error message
     *
     * @return the number of errors in the errors array
     */
    public function hasErrors()
    {
        return sizeof($this->errors);
    }

    /**
     * Get the success array
     *
     * @return the array of success
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * Add an succes to the success array
     *
     * @param string $message the success message
     *
     * @return void
     */
    public function addSucces($message)
    {
        array_push($this->success, $message);
    }

    /**
     * Check if there is any succes message
     *
     * @return the number of success in the success array
     */
    public function hasSuccess()
    {
        return sizeof($this->success);
    }



}


