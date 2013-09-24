<?php

class controller_user extends controller_base
{
    protected $max_file_size = 102400; //Допустимый размер аватара
    protected $allowed_ext = array('jpg', 'jpeg', 'png', 'gif'); //Допустимые расширения загружаемого файла
    protected $file_path = '/../images/avatars/';
    protected $height = 64;
    protected $width = 64;
    
    public function __construct() {
        if (isset($_SESSION['uid'])) {
            $this->auth = $_SESSION['uid'];
        }
        $this->model = new model_user();
    }
    
    public function authors ()
    {
        $this->title = 'Авторы';
        $query = 'SELECT `user_name`, `user_id`, COUNT(`article_user_id`) AS arts
            FROM `myblog_user`
            LEFT JOIN `myblog_article` ON `user_id` = `article_user_id`
            GROUP BY user_id
            ORDER BY `user_name` ASC LIMIT 20';
        $sth = $this->dbh->db->prepare($query);
        $sth->execute(array());
        while ($row = $sth->fetchAll(PDO::FETCH_ASSOC)) {
            $this->rows = $row;
        }
        $this->show('authors', true);
    }

    //загрузка аватаров
    public function change()
    {
        //Только зарегистрированные пользователи могут редактировать
        if (!$this->auth) {
            $this->assign('error', 'Только зарегистрированные пользователи могут редактировать профиль');
            $this->view('change');
            exit();
        }
        //Был ли загружен файл
        if (isset($_FILES['avatar']['error'])) {
            if ($_FILES['avatar']['size']) {
                //Ограничиваем размер аватаров
                if ($_FILES['avatar']['size'] <= $this->max_file_size) {
                    //проверяем допустимое расширение файла
                    $ext = explode('.', strtolower($_FILES['avatar']['name']));
                    $ext = array_pop($ext);
                    $file_name = __DIR__ . $this->file_path . $this->auth . '_tmp.' . $ext;
                    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $file_name)) {
                        switch ($ext) {
                            case 'jpg':
                            case 'jpeg':
                                $source = imagecreatefromjpeg($file_name);
                                break;
                            case 'gif':
                                $source = imagecreatefromgif($file_name);
                                break;
                            case 'png':
                                $source = imagecreatefrompng($file_name);
                                break;
                            default:
                                $this->assign('error', 'Недопустимое расширение файла');
                                $this->view('change');
                                exit();
                        }
                        $new_file = __DIR__ . $this->file_path . $this->auth . '.jpg';
                        list($width, $height) = getimagesize($file_name);
                        $thumb = imagecreatetruecolor($this->width, $this->height);
                        imagecopyresampled($thumb, $source, 0, 0, 0, 0, $this->width, $this->height, $width, $height);
                        if (imagejpeg($thumb, $new_file)) {
                            $this->assign('success', 'Аватар загружен');
                        } else {
                            $this->assign('error', 'Ошибка создания аватара');
                        }
                        unlink($file_name);
                    } else {
                        $this->assign('error', 'Ошибка создания аватара');
                    }
                } else {
                    $this->assign('error', 'Слишком большой байл. Максимальный размер: '
                        . ($this->max_file_size / 1024) . ' кбайт');
                }
            } else {
                $this->assign('error', 'Ошибка загрузки файла');
            }
        }
        $this->view('change');
    }
    
    //ввод нового пароля
    public function newpass()
    {
        $this->assign('title', 'Ввод нового пароля');
        $this->model->setFilter('user_retrieve', '=', $this->id);
        $data = $this->model->getAll();
        if (isset($data[0]['user_id'])) {
            if ($data[0]['user_retr_live'] < time()) {
                $this->assign('error', 'Ссылка устарела, запросите новую');
            } else {
                if (!isset($_POST['pass1']) OR !isset($_POST['pass2'])) {
                    $this->view('newpass');
                    exit();
                }
                $pass1 = isset($_POST['pass1']) ? $_POST['pass1'] : '';
                $pass2 = isset($_POST['pass2']) ? $_POST['pass2'] : '';
                if ($pass1 AND $pass1) {
                    if (strcmp($pass1, $pass2) == 0) {
                        if ($this->model->saveNewPasw($data[0]['user_id'], $pass1)) {
                            $this->assign('success', 'Новый пароль принят, пройдите регистрацию');
                        } else {
                            $this->assign('error', 'Ошибка восстановления пароля');
                        }
                    } else {
                        $this->assign('error', 'Пароли не совпадают');
                    }
                } else {
                    $this->assign('error', 'Пустой пароль не позволяется');
                }
            }
        } else {
            $this->error404();
            exit();
        }
        $this->view('newpass');
    }
    
    //Запрос нового пароля
    public function retrieve()
    {
        $this->assign('title', 'Восстановление пароля');
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        if ($email) {
            $this->model->setFilter('user_email', '=', $email);
            $data = $this->model->getAll();
            if (isset($data[0]['user_id'])) {
                $retrieve = md5(uniqid(mt_rand(), true));
                $timetolive = time() + (24 * 60 * 60);
                        
                $this->model->assign('retrieve', $retrieve);
                $this->model->assign('timetolive', $timetolive);
                if (!$this->model->saveRetrieve($data[0]['user_id'])) {
                    $this->assign('error', 'Ошибка восстановления пароля');
                    $this->view('retrieve');
                    exit();
                }
                
                if (isset($_SERVER['SERVER_HOST'])) {
                    $host = $_SERVER['SERVER_HOST'];
                } else {
                    $host = $_SERVER['SERVER_NAME'];
                }
                $link = 'http://' . $host . '/?user/newpass/'. $retrieve;
                $subject = 'Восстановление пароля';
                $message = '<p>Чтобы восстановить пароль перейдите по ссылке:</p>' . "\r\n";
                $message .= '<a href="' . $link . '">' . $link . '</a>' . "\r\n";
                $message .= '<p>Ссылка будет жива сутки</p>' . "\r\n";
                $headers  = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

                $headers .= 'To: ' . $data[0]['user_name'] . ' <' . $email . '>' . "\r\n";
                $headers .= 'From: MyBlog <no-reply@example.com>' . "\r\n";
                
                $text = $headers . "\r\n" . $email . "\r\n" . $subject . "\r\n" . $message;
                $file_name = __DIR__ . $this->file_path . 'email_tmp.txt';
                file_put_contents($file_name, $text);

                if (mail($email, $subject, $message, $headers)) {
                    $this->assign('success', 'Инструкция по восстановлению пароля отправлена на емайл');
                } else {
                    $this->assign('error', 'Ошибка отправки емайла');
                }
            } else {
                $this->assign('error', 'Неверный емайл');
            }
        }
        $this->view('retrieve');
    }
    
    public function login()
    {
        $this->assign('title', 'Вход');
        $login = isset($_POST['login']) ? $_POST['login'] : '';
        $password = $this->model->mcryptPass(isset($_POST['password']) ? $_POST['password'] : '');
        if ($login AND $password) {
            $this->model->setFilter('user_login', '=', $login);
            $this->model->setFilter('user_password', '=', $password);

            $data = $this->model->getAll();
            if (isset($data[0]['user_id'])) {
                $_SESSION['uid'] = $data[0]['user_id'];
                $this->assign('auth', $data[0]['user_id']);
                $this->assign('success', 'Вход произведён');
            } else {
                $this->assign('error', 'Неверное имя пользователя или пароль');
            }
        }
        if (isset($_POST['json'])) {
            if ($this->success) {
                $res = array('msg' => $this->success, 'success' => 1);
            } else {
                $res = array('msg' => $this->error, 'success' => 0);
            }
            echo json_encode($res);
            exit();
        } else {
            $this->view('login');
        }
    }

    public function logout()
    {
        if (isset($_SESSION['uid'])) {
            unset($_SESSION['uid']);
            $this->assign('auth', false);
            $this->assign('success', 'Вы вышли из системы');
        } else {
            $this->assign('error', 'Чтобы выйти надо сначала войти');
        }
        if (isset($_POST['json'])) {
            if ($this->success) {
                $res = array('msg' => $this->success, 'success' => 1);
            } else {
                $res = array('msg' => $this->error, 'success' => 0);
            }
            echo json_encode($res);
            exit();
        } else {
            $this->view('login');
        }
    }

    public function register()
    {
        $this->title = 'Регистрация';
        $this->error_register = false;
        $login = isset($_POST['login']) ? $_POST['login'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        
        if ($login && $password && $email && $name) {
            //Проверяем есть ли такой логин в базе
            $this->model->assign('tableName', 'myblog_user');
            $this->model->assign('primaryKey', 'user_login');
            $user = $this->model->getOne($login);
            if ($user['user_id']) {
                $this->assign('error', 'Такой пользователь уже существует');
            } else {
                //Такого логина не существует, регистрируем пользователя
                if ($this->model->createUser()) {
                    $user = $this->model->getOne($login);
                    if ($user['user_id']) {
                        $this->assign('auth', $user['user_id']);
                        $_SESSION['uid'] = $user['user_id'];
                        $this->assign('success', 'Регистрация прошла успешно');
                    } else {
                        $this->assign('error', 'Ошибка регистрации');
                    }
                    //header('Location: /');
                    //exit();
                } else {
                    $this->assign('error', 'Ошибка регистрации');
                }
            }
        } else {
            if ($login || $password || $email || $name) {
                $this->assign('error', 'Для регистрации нобходимо заполнить все поля');
            }
        }
        $this->view('register');
    }
    
}
