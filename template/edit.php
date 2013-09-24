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
}
if ($this->success) {
    echo '<div class="success">' . $this->success . '</div><br>';
}
if (!isset($this->article['article_id'])) {
    exit();
}
?>

<form class="form" method="POST">
Название:<br><input type="text" name="title" size="70" value="<?=$this->article['article_title'];?>"><br>
<input type="checkbox" name="published" <?=($this->article['article_published'] ? 'checked="checked"' : '');?>>Опубликовано<br>
<textarea name="text" rows="20"><?=$this->article['article_text'];?></textarea><br>
<input type="submit" value="Изменить статью">
</form>
