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

namespace Herisson;

/**
 * Class: Herisson\Format
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
class Format
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
     * @throws Herisson\Format\Exception
     * @return void
     */
    public function check()
    {
        // Check that getForm method are not defined in $type=file Format classes
        $c = new ReflectionMethod($this, 'getForm');
        //echo $c->getDeclaringClass()->getName()." == ".get_class($this)."<br>";
        if ($c->getDeclaringClass()->getName() == get_class($this) && $this->type == 'file') {
            throw new Format\Exception(
                __('Format « '.$this->name.' » in '.get_class($this).' has « type = file ».'
               .' It should not redefine a <code>getForm()</code> method', HERISSON_TD));
        }
    }


    /**
     * Check method to verify if the format class has a given method
     *
     * @param string $methodName the name of the method to check
     *
     * @return true if the format has the method, false otherwise
     */
    protected function checkMethod($methodName)
    {
        //echo get_class($this)."::".$methodName."<br>";
        if (! method_exists($this, $methodName)) {
            return false;
        }
        // Check that import method is not in a parent class
        $c = new ReflectionMethod($this, $methodName);
        //echo $c->getDeclaringClass()->getName()." == ".get_class($this)."<br>";
        if ($c->getDeclaringClass()->getName() == get_class($this)) {
            return true;
        }
        return false;
    }


    /**
     * Check method to verify if the format class has an import method
     *
     * @throws Herisson\Format\Exception
     * @return true if the format can import, false otherwise
     */
    public function doImport()
    {
        return $this->checkMethod('import');
    }


    /**
     * Check method to verify if the format class has an export method
     *
     * @throws Herisson\Format\Exception
     * @return true if the format can export, false otherwise
     */
    public function doExport()
    {
        return $this->checkMethod('export');
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
     * @throws Herisson\Format\Exception
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
        throw new Format\Exception("The « $keyword » format is not referenced");
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
                if (!preg_match('/\.php$/', $entry) || $entry == "Exception") {
                    continue;
                }
                include_once $dir."/".$entry;
                $classname    = 'Herisson\Format\\'.$entry;
                #get_class().basename($entry, ".php");
                $formatList[] = $classname;
            }
            uasort($formatList, array('self', '_sortFormat'));
            closedir($handle);
        }

        return $formatList;
    }


    /**
     * Check of correct configuration and parameters of imports
     *
     * @throws Herisson\Format\Exception
     *
     * @return void
     */
    protected function preImport()
    {
        if ($this->type == "file" && !isset($_FILES['import_file'])) { 
            throw new Format\Exception(__("Bookmarks file not found.", HERISSON_TD));
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


