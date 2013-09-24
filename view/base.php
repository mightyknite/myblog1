<?php

class view_base
{
    protected $auth = false; //пользователь прошел аутентификацию?
    protected $title = ''; //title страницы
    protected $content = ''; //Текст страницы
    protected $error = ''; //Текст ошибки
    protected $success = '';
    protected $avatar = '';
    
    public function __construct()
    {
        if (isset($_SESSION['uid'])) {
            $this->auth = $_SESSION['uid'];
        }
    }
    
    public function assign ($var, $value)
    {
        $this->$var = $value;
    }
    
    public function getValue ($var)
    {
        return $this->$var;
    }
    
    public function view($template)
    {
        include 'template/header.php';
        include 'template/' . $template . '.php';
        include 'template/footer.php';
    }
    
}