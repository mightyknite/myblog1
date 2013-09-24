<?php

class model_comment extends model_base
{
    protected $tableName = 'myblog_comments';
    protected $primaryKey = 'comments_id';
    protected $fields = array('comments_id', 'comments_user_id', 'comments_article_id', 'comments_text');

}