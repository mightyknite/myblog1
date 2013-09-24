<?php

class controller_base
{

    public $model;
    public $view;
    
    protected $routes = array();
    protected $id = 0;
    protected $page = 1;
    
    protected $auth = false; //пользователь прошел аутентификацию?
    protected $title = ''; //title страницы
    protected $content = ''; //Текст страницы
    protected $error = ''; //Текст ошибки
    protected $success = '';
    protected $avatar = '';
    
    public function __construct() {
        $this->routes = explode('/', substr(strtolower($_SERVER['REQUEST_URI']), 2));
        $this->run();
    }
    
    public function run() {
        $class = array_shift($this->routes);
        if (!$class) {
            array_unshift($this->routes, 'titles');
            $class = 'article';
        }
        if (preg_match('#^[a-z0-9-]*$#', $class)) {
            $class = 'controller_' . $class;
            $control = new $class();
            $method = array_shift($this->routes);
            if (method_exists($control, $method)) {
                if (!empty($this->routes)) {
                    $id = array_shift($this->routes);
                    switch ($method) {
                        case 'titles':
                            $control->assign('page', $id);
                            break;
                        case 'user':
                            $control->assign('id', $id);
                            if (!empty($this->routes)) {
                                $id = array_shift($this->routes);
                                $control->assign('page', $id);
                            }
                            break;
                        default:
                            $control->assign('id', $id);
                            break;
                    }
                    if (!empty($this->routes)) {
                        $this->error404();
                    }
                }
                $control->$method();
            } else {
                $this->error404();
            }
        } else {
            $this->error404();
        }
    }

    public function error404()
    {
        header($_SERVER['SERVER_PROTOCOL'] . '404 Not Found');
        echo file_get_contents('template/404.html');
        exit();
    }

    public function assign ($var, $value)
    {
        $this->$var = $value;
    }
    
    public function view($template)
    {
        include 'template/header.php';
        include 'template/' . $template . '.php';
        include 'template/footer.php';
    }
}