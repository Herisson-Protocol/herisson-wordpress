<?


class HerissonView {

    public $app;
    public $controller;
    public $action;
    public $viewFile;
    public $options;

    function __construct($app, $controller, $action) {
        $this->app = $app;
        $this->controller = $controller;
        $this->action = $action;
        $this->viewFile = HERISSON_BASE_DIR."/views/".$this->app."/".$this->controller."/".$this->action.".php";
        $this->options = get_option('HerissonOptions');
        
    }

    function display() {
        foreach (get_object_vars($this) as $attr=>$value) {
            $$attr = $value;
        }
        echo "viewFile : $this->viewFile<br>";
        if (file_exists($this->viewFile)) {
            require $viewFile;
        }
        

    }

}

