<style type="text/css" media="all">
    .error{
        color: red;
        font-size: 150%;
    }
    .success{
        color: green;
        font-size: 150%;
    }
    .retrieve{
        color: red;
        font-size: 75%;
    }
</style>
<div id="message">
<?php

if ($this->error) {
    echo '<span class="error">' . $this->error . '</span><br>';
    exit();
}
if ($this->success) {
    echo '<span class="success">' . $this->success . '</span><br>';
    exit();
}
?>
</div>
<form class="login_form" method="POST">
Логин:  <input type="text" name="login"><br>
Пароль: <input type="password" name="password"><br>
<input type="submit" value="Login"><br>
</form>
<div class="retrieve">
<a href="?user/retrieve/">Забыли пароль?</a>
</div>
