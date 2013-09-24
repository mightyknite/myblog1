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
<h1>Аватар</h1>
<?php

if ($this->error) {
    echo '<div class="error">' . $this->error . '</div>';
    exit();
}
if ($this->success) {
    echo '<div class="success">' . $this->success . '</div>';
}
$avatar = 'images/avatars/' . $this->auth . '.jpg';
if (file_exists($avatar)) {
    echo '<img src="' . $avatar . '">';
} else {
    echo '<div class="error">Нет загруженного аватара</div>';
}
?>

<form class="form" enctype="multipart/form-data" method="POST">
Выбрать файл:  <input type="file" name="avatar"><br>
<input type="submit" value="Загрузить"><br>
</form>
