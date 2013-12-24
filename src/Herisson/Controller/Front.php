<?php

require_once __DIR__."/../Controller.php";

class HerissonControllerFront extends HerissonController
{

    function __construct()
    {
        $this->app = "front";
        parent::__construct();
        $this->setView();
    }



}
