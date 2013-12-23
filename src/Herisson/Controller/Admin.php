<?

require_once __DIR__."/../Controller.php";

class HerissonControllerAdmin extends HerissonController {

    function __construct() {
        $this->app = "admin";
        parent::__construct();

        $action = param('action');
        if ($action) {
            $this->action = $action;
        } else {
            $this->action = "index";
        }
        $this->setView();

    }



}
