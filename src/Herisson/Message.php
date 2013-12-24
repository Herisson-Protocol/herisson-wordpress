<?php


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

    public function getErrors()
    {
        return $this->errors;
    }

    public function addError($e)
    {
        array_push($this->errors, $e);
    }

    public function hasErrors()
    {
        return sizeof($this->errors);
    }

    public function getSuccess()
    {
        return $this->success;
    }

    public function addSucces($e)
    {
        array_push($this->success, $e);
    }

    public function hasSuccess()
    {
        return sizeof($this->success);
    }



}


