<?php

defined('MYBLOG') or die("Restricted access");

class comment extends ctrl
{
    
    //Удалить коментарий
    public function delete ()
    {
        //Только зарегистрированные пользователи могут удалять свои коменты
        if (!$this->user_id) {
            header('Location: /');
        }
        $args = func_get_args(); 
        $comments_id = intval(array_shift($args));
        if (!$comments_id) {
            header('Location: /');
        }
        $this->delete_error = false;
        
        $query = 'SELECT `comments_user_id`, `comments_article_id`
            FROM `myblog_comments`
            WHERE `comments_id` = ?';
        $sth = $this->dbh->db->prepare($query);
        $sth->execute(array($comments_id));
        if ($this->row = $sth->fetchAll(PDO::FETCH_ASSOC)) {
            //Только автор может удалять свой комментарий
            if ($this->user_id == $this->row[0]['comments_user_id']) {
                $query = 'DELETE FROM `myblog_comments`
                    WHERE `comments_id` = ?';
                $sth = $this->dbh->db->prepare($query);
                if ($sth->execute(array($comments_id))) {
                    call_user_func_array(array($this, 'article'), array($this->row[0]['comments_article_id']));
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
    
    //Изменить коментарий
    public function edit ()
    {
        //Только зарегистрированные пользователи могут удалять свои коменты
        if (!$this->user_id) {
            header('Location: /');
        }
        $args = func_get_args(); 
        $comments_id = intval(array_shift($args));
        if (!$comments_id) {
            header('Location: /');
        }
        $this->delete_error = false;
        
        $query = 'SELECT `comments_user_id`, `comments_article_id`, `comments_text`
            FROM `myblog_comments`
            WHERE `comments_id` = ?';
        $sth = $this->dbh->db->prepare($query);
        $sth->execute(array($comments_id));
        if ($this->row = $sth->fetchAll(PDO::FETCH_ASSOC)) {
            //Только автор может удалять свой комментарий
            if ($this->user_id == $this->row[0]['comments_user_id']) {
                //Если пришли данные сохраняем изменения иначе открываем для редактирования
                if (isset($_POST['text'])) {
                    $query = 'UPDATE `myblog_comments` SET
                        `comments_text` = ?,
                        `comments_time` = ?
                        WHERE `comments_id` = ?';
                    $sth = $this->dbh->db->prepare($query);
                    $sth->bindParam(1, $_POST['text'], PDO::PARAM_STR);
                    $this->time = time();
                    $sth->bindParam(2, $this->time, PDO::PARAM_INT);
                    $sth->bindParam(3, $comments_id, PDO::PARAM_INT);
                    if ($sth->execute()) {
                        call_user_func_array(array($this, 'article'), array($this->row[0]['comments_article_id']));
                    } else {
                        //Обработать ошибку
                        throw new Exception('Error UPDATE comment');
                    }
                } else {
                    $this->text = $this->row[0]['comments_text'];
                    $this->show('editcomment', true);
                }
            } else {
                header('Location: /');
            }
        } else {
            //Ошибка чтения коментария
            header('Location: /');
        }
    }
    
    //Добавить коментарий
    public function add ()
    {
        //Только зарегистрированные пользователи могут добавлять коментарии
        if (!$this->user_id) {
            header('Location: /');
        }
        //Получаем айди статьи
        $args = func_get_args(); 
        $this->article_id = intval(array_shift($args));
        if (!$this->article_id) {
            header('Location: /');
        }
        if (isset($_POST['text'])) {
            $this->error_add = false;
            $query = 'INSERT INTO `myblog_comments` SET
                `comments_user_id` = ?,
                `comments_article_id` = ?,
                `comments_text` = ?,
                `comments_time` = ?';
            $sth = $this->dbh->db->prepare($query);
            $sth->bindParam(1, $this->user_id, PDO::PARAM_INT);
            $sth->bindParam(2, $this->article_id, PDO::PARAM_INT);
            $sth->bindParam(3, $_POST['text'], PDO::PARAM_STR);
            $this->time = time();
            $sth->bindParam(4, $this->time, PDO::PARAM_INT);
            if ($sth->execute()) {
                call_user_func_array(array($this, 'article'), array($this->article_id));
            } else {
                //Обработать ошибку
                $this->error_add = true;
            }
        } else {
            call_user_func_array(array($this, 'article'), array($this->article_id));
        }
    }
    
    //Список всех коментариев к статье
    public function article ()
    {
        //Получаем айди статьи
        $args = func_get_args(); 
        $this->article_id = intval(array_shift($args));
        if (!$this->article_id) {
            header('Location: /');
        }
        $this->title = 'Коментарии';
        $query = 'SELECT `article_title`
            FROM `myblog_article`
            WHERE `article_id` = ?';
        $sth = $this->dbh->db->prepare($query);
        $sth->execute(array($this->article_id));
        if ($this->row = $sth->fetchAll(PDO::FETCH_ASSOC)) {
            $this->article_title = $this->row[0]['article_title'];
        }
        $query = 'SELECT `comments_text`, `user_name`, `user_id`, `comments_time`, `comments_id`
            FROM `myblog_comments`
            JOIN `myblog_user` ON `comments_user_id` = `user_id`
            WHERE `comments_article_id` = ?
            ORDER BY `comments_time` DESC';
        $sth = $this->dbh->db->prepare($query);
        $sth->execute(array($this->article_id));
        $this->rows = $sth->fetchAll(PDO::FETCH_ASSOC);
        $this->show('comments', true);
    }
    
}
