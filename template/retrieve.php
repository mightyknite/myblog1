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

echo '<h1>' . $this->title . '</h1>';
if ($this->success) {
    echo '<span class="success">' . $this->success . '</span><br>';
    exit();
}
if ($this->error) {
    echo '<span class="error">' . $this->error . '</span><br>';
}
?>
<form class="login_form" method="POST">
Введите емайл с которым зарегистрирован пользователь:<br><input type="text" name="email"><br>
<input type="submit" value="Получить новый пароль"><br>
</form>
