<?php

class model_article extends model_base
{
    private $limit = 5;
    
    public $pages;
    
    protected $tableName = 'myblog_article';
    protected $primaryKey = 'article_id';
    protected $fields = array('article_id',
        'article_user_id',
        'article_title',
        'article_text',
        'article_published',
        'article_time');
//    protected $pageSize = 5;
       
    /*
     * (boolean)$new
     * true - создать новую статью
     * false - сохранить существующую
     */
    public function saveArticle ($new)
    {
        if ($new) {
            $query = 'INSERT INTO `myblog_article` SET';
        } else {
            $query = 'UPDATE `myblog_article` SET';
        }
        $query .=' `article_title` = ?,
            `article_text` = ?,
            `article_time` = ?,
            `article_published` = ?,
            `article_user_id` = ?';
        if (!$new) {
            $query .=' WHERE `article_id` = ?';
        }
        $sth = $this->db->db->prepare($query);
        $title = htmlspecialchars($_POST['title']);
        $sth->bindParam(1, $title, PDO::PARAM_STR);
        $sth->bindParam(2, $_POST['text'], PDO::PARAM_STR);
        $time = time();
        $sth->bindParam(3, $time, PDO::PARAM_INT);
        if (isset($_POST['published'])) {
            $published = 1;
        } else {
            $published = 0;
        }
        $sth->bindParam(4, $published, PDO::PARAM_INT);
        $sth->bindParam(5, $this->auth, PDO::PARAM_INT);
        if (!$new) {
            $sth->bindParam(6, $this->id, PDO::PARAM_INT);
        }
        return $sth->execute();
    }
}