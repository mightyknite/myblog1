<?php

class ctrl
{
            
    public function __construct() {
        $this->dbh = new db();
        if (isset($_SESSION['uid'])) {
            $this->user_id = $_SESSION['uid'];
        } else {
            $this->user_id = false;
        }
    }
    
    public function show($template, $header = false) {
        if ($header) {
            $this->tpl = $template;
            include 'template/' . 'index_old' . '.php';
        } else {
            include 'template/' . $template . '.php';
        }
    }
    
}

class app
{
    public function __construct() {
        $this->routes = explode('/', substr($_SERVER['REQUEST_URI'],2));
        $this->run();
    }
    
    public function run() {
        $class = array_shift($this->routes);
        if (preg_match('#^[a-z0-9]*$#', $class)) {
            switch ($class) {
                case 'article':
                    $class = 'controller_' . $class;
                    break;
                default:
                    $file = 'classes/' . $class . '.php';
                    if (!file_exists($file)) {
                        //404
                        $class = 'article';
                        $file = 'controller/' . $class . '.php';
                        array_unshift($this->routes, 'titles');
                    }
                    require $file;
                    break;
            }
            $control = new $class();
            $method = array_shift($this->routes);
            if (method_exists($control, $method)) {
                if (!empty($this->routes)) {
                    call_user_func_array(array($control, $method), $this->routes);
                } else {
                    $control->$method();
                }
            } else {
                throw new Exception('Invalid method');
            }
        } else {
            throw new Exception('Invalid class');
        }
    }
}
