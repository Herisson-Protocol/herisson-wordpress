<?

include ("View.php");


class HerissonController {

    public $action;
    public $name;
    public $view;
    public $options;
    public $app;

    function __construct() {
        $this->options = get_option('HerissonOptions');
        $path =explode("/", $_SERVER['REQUEST_URI']);
        if (array_key_exists(2, $path) && strlen($path[2])) {
            $this->action = $path[2];
        } else {
            $this->action = "index";
        }
        $this->setView();
    }

    protected function setView($action=null, $controller=null) {
        if ($action) {
            $this->action = $action;
        }
        if ($controller) {
            $this->controller = $controller;
        }
        $this->view = new HerissonView($this->app, $this->name, $this->action);
    }

    private function getActionName($actionName) {
        return $actionName."Action";
    }

    function route() {
        if ($this->action) {
            $method = $this->getActionName($this->action);
            if (method_exists($this, $method)) {
                call_user_func(array($this, $method));
            } else {
                $this->indexAction();
            }

        } else {
            $this->indexAction();
        }

        $this->view->display();

    }

    function showView() {


    }


}


