<?php

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


class HerissonControllerAdminOption extends HerissonControllerAdmin
{


    function __construct()
    {
        $this->name = "option";
        parent::__construct();
    }


    /**
     * Creates the options admin page and manages the updating of options.
     * 
     * @return void
     */
    function indexAction()
    {

        if (post('action') == 'index') {
            $options = get_option('HerissonOptions');
            $new_options = array();
            $allowedoptions = array('basePath', 'bookmarksPerPage', 'sitename', 'debugMode', 'adminEmail', 'search', 'screenshotTool', 'convertPath', 'checkHttpImport', 'acceptFriends', 'spiderOptionTextOnly', 'spiderOptionFullPage', 'spiderOptionScreenshot');
            foreach ($allowedoptions as $option) {
                $new_options[$option] = post($option);
            }
            $complete_options = array_merge($options, $new_options);
            if (!array_key_exists('privateKey', $complete_options)) {
                list($publicKey, $privateKey) = herisson_generate_keys_pair();
                $complete_options['publicKey'] = $publicKey;
                $complete_options['privateKey'] = $privateKey;
                echo "<b>Warning</b> : public/private keys have been regenerated<br>";
            }
            update_option('HerissonOptions', $complete_options);
        }
        $this->view->screenshots = WpHerissonScreenshotsTable::getAll();
        $this->view->options = get_option('HerissonOptions');


    }

}


