<?php

?><!DOCTYPE html>
<html>
    <head>
        <title><?= $this->title; ?></title>
        <script src="http://localhost/js/jquery-2.0.3.js"></script>
        <script src="http://localhost/js/login-menu.js"></script>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<style type="text/css" media="all">
    .invisible {
        display: none;
    }
    .error{
        color: red;
        font-size: 150%;
    }
    .success{
        color: green;
        font-size: 150%;
    }
</style>
    </head>
    <body>
        <div class="div_menu" style="display: inline-block;">
            <a href="?article/titles" class="a_menu">Статьи</a>
            <a href="?user/authors" class="a_menu">Авторы</a>
            <div class="div_menu" style="display: inline-block;">
            <div id="div_auth"<?php
            if (!$this->auth) { echo ' class="invisible"';}
            ?>
>
                    <a href="?article/create" class="a_menu">Создать статью</a>
                    <a href="?user/change" class="a_menu">Личное</a>
                    <a href="?user/logout" class="a_menu" id="logout">Выход</a>
            </div>
            <div id="div_notauth"<?php
            if ($this->auth) { echo ' class="invisible"';}
            ?>
>
                    <a href="?user/login" class="a_menu" id="login">Логин</a>
                    <a href="?user/register" class="a_menu">Регистрация</a>
            </div>
            </div>
        </div>
<div class="invisible" id="div_login">
    <form id="lg_form" class="form" method="POST" action="?user/login">
    Логин:  <input type="text" name="login"><br>
    Пароль: <input type="password" name="password"><br>
    <input type="submit" value="Login"><br>
    </form>
    <div class="retrieve">
    <a href="?user/retrieve/">Забыли пароль?</a>
    </div>
</div>
<div id="msg" class="invisible" ></div>
