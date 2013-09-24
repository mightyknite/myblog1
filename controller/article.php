<?php

class controller_article extends controller_base
{
    protected $pages; //Вывод количества страниц и ссылки на страницы
    protected $article_comments = 0; //Коментов к статье
    protected $article = array(); //Статья
    protected $list = array(); //Список статей

    public function __construct() {
        if (isset($_SESSION['uid'])) {
            $this->auth = $_SESSION['uid'];
        }
        $this->model = new model_article();
    }
    
    //Показ определённой статьи
    public function display ()
    {
        $this->model->setFilter('article_id', '=', $this->id);
        $this->model->addJoin('model_user',
                array('article_user_id' => 'user_id'),
                'user',
                array('user_id', 'user_name'));
        $article = $this->model->getAll();
        //Статья должна быть опубликована или принадлежать юзеру
        if (isset($article[0]['article_id']) &&
                ($article[0]['article_published'] || $article[0]['article_user_id'] == $this->auth)) {
            $this->assign('title', $article[0]['article_title']);
            $this->assign('article', $article[0]);
            $this->model->resetJoins();
            $this->model->resetFilters();
            $this->model->addJoin('model_comment',
                    array('article_id' => 'comments_article_id'),
                    'comm',
                    array('user_id', 'user_name'));
            $this->model->setFilter('comments_article_id', '=', $this->id);
            $count = $this->model->getCount();
            $this->assign('article_comments', $count['count']);
        } else {
            //Нет статьи
            $this->error404();
            exit();
        }
        $this->view('display');
    }
    
    public function delete ()
    {
        //Только зарегистрированные пользователи могут удалять статьи
        if (!$this->user_id) {
            header('Location: /');
        }
        $args = func_get_args(); 
        $article_id = intval(array_shift($args));
        if (!$article_id) {
            header('Location: /');
        }
        $this->delete_error = false;
        
        $query = 'SELECT article_user_id
            FROM `myblog_article`
            WHERE `article_id` = ?';
        $sth = $this->dbh->db->prepare($query);
        $sth->execute(array($article_id));
        if ($this->row = $sth->fetchAll(PDO::FETCH_ASSOC)) {
            //Только автор может удалять свою статью
            if ($this->user_id == $this->row[0]['article_user_id']) {
                $query = 'DELETE FROM `myblog_article`
                    WHERE `article_id` = ?';
                $sth = $this->dbh->db->prepare($query);
                if ($sth->execute(array($article_id))) {
                    $this->show('delete', true);
                } else {
                    $this->delete_error = true;
                    $this->show('delete', true);
                }
            } else {
                header('Location: /');
            }
        } else {
            $this->delete_error = true;
            $this->show('delete', true);
        }
    }
    
    public function edit ()
    {
        //Только зарегистрированные пользователи могут редактировать статьи
        if (!$this->auth) {
            $this->assign('error', 'Только зарегистрированные пользователи могут редактировать статьи');
            $this->view('edit');
            exit();
        }
        $this->model->setFilter('article_id', '=', $this->id);
        $article = $this->model->getAll();
        //Только автор может редактировать свою статью
        if (isset($article[0]['article_id']) && $article[0]['article_user_id'] == $this->auth) {
            //Если пришли данные сохраняем изменения иначе открываем для редактирования
            if (isset($_POST['text']) && isset($_POST['title'])) {
                if ($this->model->saveArticle(false)) {
                    $this->assign('success', 'Статья успешно сохранена');
                    $this->display();
                    exit();
                } else {
                    $this->assign('error', 'Не удалось сохранить статью');
                }
            } else {
                $this->assign('title', $article[0]['article_title']);
                $this->assign('article', $article[0]);
            }
        } else {
            //Ошибка чтения статьи
            $this->assign('error', 'Неверная статья');
        }
        $this->view('edit');
    }
    
    //Создать статью
    public function create ()
    {
        $this->assign('title', 'Создать статью');
        
        //Только зарегистрированные пользователи могут создавать статьи
        if ($this->auth) {
            if (isset($_POST['text']) && isset($_POST['title'])) {
                if ($this->model->saveArticle (true)) {
                    $this->assign('success', 'Статья создана');
                } else {
                    $this->assign('error', 'Ошибка создания статьи');
                }
            }
        } else {
            $this->assign('error', 'Только зарегистрированные пользователи могут создавать статьи');
        }
        $this->view('create');
    }
    
    //Список всех статей
    public function titles ()
    {
        $this->model->assign('currentPage', $this->page);
        $this->model->setOrder('article_time', model_base::ORDER_DESC);
        $this->model->setFilter('article_published', '=', true);
        $this->model->addJoin('model_user',
                array('article_user_id' => 'user_id'),
                'user',
                array('user_id', 'user_name'));
        $list = $this->model->getAll();
        
        $this->assign('list', $list);
        $this->assign('title', 'Статьи');
        
        //Вычисляем количество страниц
        $this->assign('pages', $this->getCountPages ());

        $this->view('titles');
    }
    
    //Статьи определённого пользователя
    public function user ()
    {
        $this->model->assign('currentPage', $this->page);
        $this->model->setOrder('article_time', model_base::ORDER_DESC);
        $this->model->setFilter('article_user_id', '=', $this->id);
        if ($this->id != $this->auth) {
            $this->model->setFilter('article_published', '=', true);
        }
        $this->model->addJoin('model_user',
                array('article_user_id' => 'user_id'),
                'user',
                array('user_id', 'user_name'));
        $list = $this->model->getAll();
        
        $this->assign('list', $list);
        $this->assign('title', 'Статьи автора');
        
        //Вычисляем количество страниц
        $this->assign('pages', $this->getCountPages ());
        
        $this->view('titles');
    }
    
    public function getCountPages ()
    {
        $pageSize = $this->model->getPageSize();
        if ($pageSize) {
            $count = $this->model->getCount();
            return ceil($count['count'] / $pageSize);
        } else {
            return 0;
        }
    }
}
