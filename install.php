<?php
//Some rem
define ('MYBLOG', 1);

require './classes/db.php';

echo '<!DOCTYPE html><html><head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head><body>';
echo '<h1>Инсталляция таблиц "Мой Блог"</h1>';
$dbh = new db();
if (!$dbh->db) {
    echo '<h2>Перед началом работы измените имя пользователя и пароль к базе данных в файле classes\\db.php</h2>';
    echo '</body></html>';
    exit;
}
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'create':
            $query = "CREATE TABLE IF NOT EXISTS `myblog_article` (
                `article_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `article_user_id` int(10) unsigned NOT NULL DEFAULT '0',
                `article_title` char(255) NOT NULL DEFAULT '',
                `article_text` text NOT NULL,
                `article_time` int(10) NOT NULL DEFAULT '0',
                PRIMARY KEY (`article_id`),
                UNIQUE KEY `article_id` (`article_id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;
                ";
            $sth = $dbh->db->prepare($query);
            if ($sth->execute()) {
                echo '<h3>Таблица myblog_article создана</h3>';
            } else {
                echo '<h3>Таблица myblog_article - ошибка</h3>';
            }
            $query = "CREATE TABLE IF NOT EXISTS `myblog_user` (
                `user_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
                `user_login` char(32) NOT NULL DEFAULT '',
                `user_password` char(32) NOT NULL DEFAULT '',
                `user_email` char(128) NOT NULL DEFAULT '',
                `user_name` char(128) NOT NULL DEFAULT '',
                `user_registered` int(10) unsigned NOT NULL DEFAULT '0',
                PRIMARY KEY (`user_id`),
                UNIQUE KEY `user_id` (`user_id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;
                ";
            $sth = $dbh->db->prepare($query);
            if ($sth->execute()) {
                echo '<h3>Таблица myblog_user создана</h3>';
            } else {
                echo '<h3>Таблица myblog_user - ошибка</h3>';
            }
            $query = "CREATE TABLE IF NOT EXISTS `myblog_comments` (
                `comments_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `comments_user_id` int(10) unsigned NOT NULL DEFAULT '0',
                `comments_article_id` int(10) unsigned NOT NULL DEFAULT '0',
                `comments_text` char(255) NOT NULL DEFAULT '',
                `comments_time` int(10) unsigned NOT NULL DEFAULT '0',
                PRIMARY KEY (`comments_id`),
                UNIQUE KEY `comments_id` (`comments_id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;
                ";
            $sth = $dbh->db->prepare($query);
            if ($sth->execute()) {
                echo '<h3>Таблица myblog_comments создана</h3>';
            } else {
                echo '<h3>Таблица myblog_comments - ошибка</h3>';
            }
            break;
        case 'delete':
            $query = "DROP TABLE IF EXISTS `myblog_comments`, `myblog_user`, `myblog_article`";
            $sth = $dbh->db->prepare($query);
            if ($sth->execute()) {
                echo '<h3>Таблицы удалены</h3>';
            } else {
                echo '<h3>Ошибка удаления таблиц</h3>';
            }
            break;
    }
}
echo 'Будут установлены следующие таблицы:<br>';
echo 'myblog_user<br>';
echo 'myblog_article<br>';
echo 'myblog_comments<br>';
echo '<form class="form" method="POST">';
echo '<input type="hidden" name="action" value="create">';
echo '<input type="submit" value="Создать таблицы">';
echo '</form>';
echo '<form class="form" method="POST" style="margin-top: 20px;">';
echo '<input type="hidden" name="action" value="delete">';
echo '<input type="submit" value="Удалить таблицы">';
echo '</form>';
echo '</body></html>';
