<style type="text/css" media="all">
    .author{
        color: green;
        font-size: 70%;
    }
    .text{
        padding-left: 20px;
    }
    .total{
        padding-left: 0px;
        color: green;
        font-size: 80%;
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
<script src="./js/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
tinymce.init({
    selector: "textarea",
    width : "640px"
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
<?if ($this->auth == $this->article['user']['user_id']) {?>
    <a href="?article/edit/<?=$this->article['article_id'];?>" class="a_menu">Изменить</a>
    <a href="?article/delete/<?=$this->article['article_id'];?>" class="a_menu"
        onclick="return confirm('Удалить статью?');">Удалить</a>
<?}?>

<h1><?=$this->article['article_title'];?></h1>
<div class="author"><?=$this->article['user']['user_name'];?></div>
<div class="text"><?=$this->article['article_text'];?></div>
<div class="total"><a href="?comment/article/<?=$this->article['article_id'];?>">
Всего коментариев: <?=$this->article_comments;?></a></div>
<?if ($this->auth) {?>
<h3>Добавить коментарий</h3>
<form class="comment_form" method="POST" action="?comment/add/<?=$this->article['article_id'];?>">
<textarea name="text" rows="1"></textarea><br>
<input type="submit" value="Добавить коментарий">
</form>
<?}?>