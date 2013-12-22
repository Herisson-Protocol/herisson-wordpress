<?

require __DIR__."/../Controller.php";

class HerissonControllerAdmin extends HerissonController {

    function __construct() {
        parent::__construct();
        $this->app = "admin";

        $action = param('action');
        if ($action) {
            $this->action = $action;
        } else {
            $this->action = "index";
        }
        $this->setView();

    }



}
