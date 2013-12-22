<?

require __DIR__."/../Controller.php";

class HerissonControllerFront extends HerissonController {

    function __construct() {
        parent::__construct();
        $this->app = "front";
    }



}
