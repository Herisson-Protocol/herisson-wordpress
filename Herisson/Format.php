<?php
/**
 * Format tool
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


/**
 * Class: HerissonFormat
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
class HerissonFormat
{

    /**
     * Format name
     */
    public $name;

    /**
     * Format keyword
     */
    public $keyword;

    /**
     * Format type
     */
    public $type;

    /**
     * Check method to verify if everything in the Format class is correctly defined
     *
     * @throws HerissonFormatException
     * @return void
     */
    public function check()
    {
        // Check that getForm method are not defined in $type=file Format classes
        $c = new ReflectionMethod($this, 'getForm');
        //echo $c->getDeclaringClass()->getName()." == ".get_class($this)."<br>";
        if ($c->getDeclaringClass()->getName() == get_class($this) && $this->type == 'file') {
            throw new HerissonFormatException(
                __('Format « '.$this->name.' » in '.get_class($this).' has « type = file ».'
               .' It should not redefine a <code>getForm()</code> method', HERISSON_TD));
        }
    }


    /**
     * Default getForm method
     *
     * @return void
     */
    public function getForm()
    {
        echo "This format has no form, and is not a file format. It is probably an error. Ask the Format creator";
    }


    /**
     * Retrieve the correct Format class specified by the key
     *
     * @param string $keyword the keyword of the file format
     *
     * @throws HerissonFormatException
     * @return an instance of the correct Format class
     */
    public static function getFormatByKey($keyword)
    {
        $formatList = self::getList();
        foreach ($formatList as $format) {
            if ($format->keyword == $keyword) {
                return $format;
            }
        }
        throw new HerissonFormatException("The « $keyword » format is not referenced");
    }


    /**
     * Browse Format directory to list available import/export format.
     *
     * @return an array of available formats
     */
    public static function getList()
    {
        $dir        = __DIR__.'/Format';
        $formatList = array();

        if ($handle = opendir($dir)) {
            /* Ceci est la façon correcte de traverser un dossier. */
            while (false !== ($entry = readdir($handle))) {
                if (!preg_match('/\.php$/', $entry)) {
                    continue;
                }
                include_once $dir."/".$entry;
                $classname    = get_class().basename($entry, ".php");
                $formatList[] = new $classname;
            }
            uasort($formatList, array('self', '_sortFormat'));
            closedir($handle);
        }

        return $formatList;
    }


    /**
     * Check of correct configuration and parameters of imports
     *
     * @throws HerissonFormatException
     *
     * @return void
     */
    protected function preImport()
    {
        if ($this->type == "file" && !isset($_FILES['import_file'])) { 
            throw new HerissonFormatException(__("Bookmarks file not found.", HERISSON_TD));
        }
    }

    /**
     * Sorting format names
     *
     * @param string $a first element
     * @param string $b second element
     *
     * @return the strcmp() result of format names
     */
    private function _sortFormat($a, $b)
    {
        return strcmp($a->name, $b->name);
    }

}


/**
 * Class: HerissonFormatException
 *
 * @category Tools
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      None
 */
class HerissonFormatException extends Exception
{


}
