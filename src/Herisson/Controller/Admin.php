<?

require __DIR__."/../Controller.php";

class HerissonControllerAdmin extends HerissonController {

    function __construct() {
        parent::__construct();
        $this->app = "admin";

        $this->action = "index";

    }



}
