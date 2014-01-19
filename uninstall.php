<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit();

require_once __DIR__."/Herisson.php";


Herisson::uninstall();


