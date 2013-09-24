<style type="text/css" media="all">
    .error{
        color: red;
        font-size: 150%;
    }
    .success{
        color: green;
        font-size: 150%;
    }
</style>
<?php

if ($this->auth) {
    if ($this->success) {
        echo '<span class="success">' . $this->success . '</span><br>';
    } else {
        echo '<span class="error">Сначала разлогинтесь</span><br>';
    }
    exit();
}
if ($this->error) {
    echo '<span class="error">' . $this->error . '</span><br>';
}
?>
<form class="register_form" method="POST">
    Логин:  <input type="text" name="login" value="<?=isset($_POST['login']) ? htmlspecialchars($_POST['login']) : '';?>"><br>
Пароль: <input type="password" name="password" value=""><br>
Ваш емайл: <input type="text" name="email" value="<?=isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';?>"><br>
Ваше имя: <input type="text" name="name" value="<?=isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';?>"><br>
<input type="submit" value="Регистрация"><br>
</form>