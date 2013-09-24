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
<script src="./js/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
tinymce.init({
    selector: "textarea",
    width : "640px",
 });
</script>
<?php

if ($this->error) {
    echo '<div class="error">' . $this->error . '</div><br>';
    exit();
}
if ($this->success) {
    echo '<span class="success">' . $this->success . '</span><br>';
    exit();
}
?>
<form class="form" method="POST" action="?article/create">
Название:<br><input type="text" name="title" size="70"><br>
<input type="checkbox" name="published">Опубликовать<br>
<textarea name="text" rows="20"></textarea><br>
<input type="submit" value="Создать статью">
</form>
