<?php

require_once __DIR__."/../Admin.php";

class HerissonControllerAdminBackup extends HerissonControllerAdmin
{


    function __construct()
    {
        $this->name = "backup";
        parent::__construct();
    }


    /**
     * Creates the options admin page and manages the updating of options.
     */
    function indexAction()
    {
        $this->view->backups = Doctrine_Query::create()
            ->from('WpHerissonBackups')
            ->execute();

    }

}


