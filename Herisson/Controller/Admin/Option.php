<?php
/**
 * Option controller 
 *
 * @category Controller
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      HerissonControllerAdmin
 */

require_once __DIR__."/../Admin.php";

/*
if (!isset($_SERVER['REQUEST_URI'])) {
    $arr = explode("/", $_SERVER['PHP_SELF']);
    $_SERVER['REQUEST_URI'] = "/" . $arr[count($arr) - 1];
    if ( !empty($_SERVER['argv'][0]) ) {
        $_SERVER['REQUEST_URI'] .= "?{$_SERVER['argv'][0]}";
    }
}
*/

/**
 * Class: HerissonControllerAdminOption
 *
 * @category Controller
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      HerissonControllerAdmin
 */
class HerissonControllerAdminOption extends HerissonControllerAdmin
{

    /**
     * Constructor
     *
     * Sets controller's name
     */
    function __construct()
    {
        $this->name = "option";
        parent::__construct();
    }

    /**
     * Creates the options admin page and manages the update of options.
     * 
     * This is the default Action
     *
     * @return void
     */
    function indexAction()
    {

        if (post('action') == 'index') {
            $options = get_option('HerissonOptions');
            $new_options = array();
            $allowedoptions = array(
                'acceptFriends',
                'adminEmail',
                'basePath',
                'bookmarksPerPage',
                'checkHttpImport',
                'convertPath',
                'debugMode',
                'screenshotTool',
                'search',
                'sitename',
                'spiderOptionFavicon',
                'spiderOptionFullPage',
                'spiderOptionScreenshot',
                'spiderOptionTextOnly',
            );
            foreach ($allowedoptions as $option) {
                $new_options[$option] = post($option);
            }
            $complete_options = array_merge($options, $new_options);
            if (!array_key_exists('privateKey', $complete_options)) {
                $encryption = Herisson\Encryption::i()->generateKeyPairs();
                $complete_options['publicKey'] = $encryption->public;
                $complete_options['privateKey'] = $encryption->private;
                echo "<b>Warning</b> : public/private keys have been regenerated<br>";
            }
            update_option('HerissonOptions', $complete_options);
        }

        // Check binaries paths
        $binaryTools = array(
            'convert',
            'wget',
            'du',
            'mv',
            'uname',
        );
        sort($binaryTools);
        $this->view->binaries = array();
        foreach ($binaryTools as $binary) {
            $this->view->binaries[$binary] = Herisson\Shell::getPath($binary);
        }

        $this->view->platform = Herisson\Shell::shellExec('uname', '-a');

        $this->view->screenshots = WpHerissonScreenshotsTable::getAll();
        $this->view->options = get_option('HerissonOptions');


    }

}


