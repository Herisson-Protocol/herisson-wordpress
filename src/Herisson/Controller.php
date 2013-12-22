<?

include ("View.php");


class HerissonController {

    public $action;
    public $name;
    public $view;
    public $options;

    function __construct() {
        $this->view = new HerissonView;
        $this->options = get_option('HerissonOptions');
        $path =explode("/",$_SERVER['REQUEST_URI']);
        if (array_key_exists(2,$path) && strlen($path[2])) {
            $this->action = $path[2];
        } else {
            $this->action = "index";
        }
    }

    private function getActionName($name) {
        return $name."Action";
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

        $this->showView();

    }

    function showView() {
        foreach (get_object_vars($this->view) as $attr=>$value) {
            $$attr = $value;
        }
        $viewfile = HERISSON_BASE_DIR."/views/".$this->name."/".$this->action.".php";
        if (file_exists($viewfile)) {
            require $viewfile;
        }
        


    }


}


