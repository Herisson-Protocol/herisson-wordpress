<?

require_once("View.php");


class HerissonController {

    public $action;
    public $name;
    public $view;
    public $options;
    public $app;
    public $layout;

    function __construct() {
        $this->options = get_option('HerissonOptions');
        $path =explode("/", $_SERVER['REQUEST_URI']);
        if (array_key_exists(2, $path) && strlen($path[2])) {
            $this->action = $path[2];
        } else {
            $this->action = "index";
        }
        $this->layout = true;
        $this->setView();
    }

    protected function setView($action=null, $controller=null) {
        if ($action) {
            $this->action = $action;
        }
        if ($controller) {
            $this->name = $controller;
        }
        if (!is_a($this->view,"HerissonView")) {
            $this->view = new HerissonView($this->app, $this->name, $this->action);
        } else {
            $this->view->setAction($this->action);
            $this->view->setController($this->name);
        }
    }

    private function getActionName($actionName) {
        return $actionName."Action";
    }

    public function route() {
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

        if (! $this->layout) {
            exit;
        }

    }

    public function debug() {
        foreach (get_object_vars($this) as $attr=>$value) {
            if (is_string($value)) {
                print "$attr = $value<br/>";
            }
        }

    }

}


