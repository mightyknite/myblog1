<?php

defined('MYBLOG') or die("Restricted access");

?>
<script type="text/javascript" src="/js/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
tinymce.init({
    selector: "textarea",
    width : "640px"
});
</script>
<style type="text/css" media="all">
    .comment_author{
        font-size: 150%;
    }
</style>
<h1><?=$this->article_title;?></h1>
<?if ($this->user_id) {?>
<h3>Добавить коментарий</h3>
<form class="comment_form" method="POST" action="?comment/add/<?=$this->article_id;?>">
<textarea name="text" rows="10"></textarea><br>
<input type="submit" value="Добавить коментарий">
</form>
<?}?>
<h2>Коментарии:</h2>
<?if (isset($this->rows)) {?>
<?foreach($this->rows as $value){?>
<div class="comments">
<div class="comment_author">
<a href="?article/user/<?=$value['user_id'];?>"><?=$value['user_name'];?></a>
<?=date('d-m-Y H:i:s', $value['comments_time']);?>
</div>
<?if ($this->user_id == $value['user_id']) {?>
<div>
<a href="?comment/edit/<?=$value['comments_id'];?>">Изменить коментарий</a>
<a href="?comment/delete/<?=$value['comments_id'];?>" onclick="return confirm('Удалить коментарий?');">Удалить коментарий</a>
</div>
<?}?>
<div class="comment"><?=$value['comments_text'];?></div>
</div>
<?}?>
<?} else {?>
<h5>Нет коментариев</h5>
<?}?>
