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
</style>
<script src="./js/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
tinymce.init({
    selector: "textarea",
    width : "640px"
 });
</script>

<?if ($this->user_id == $this->article['user_id']) {?>
    <a href="?article/edit/<?=$this->article['article_id'];?>" class="a_menu">Изменить</a>
    <a href="?article/delete/<?=$this->article['article_id'];?>" class="a_menu"
        onclick="return confirm('Удалить статью?');">Удалить</a>
<?}?>

<?if (isset($this->delete_error) && $this->delete_error) {?>
    <div class="error">Ошибка удаления статьи</div>
<?}?>
<h1><?=$this->article['article_title'];?></h1>
<div class="author"><?=$this->article['user_name'];?></div>
<div class="text"><?=$this->article['article_text'];?></div>
<div class="total"><a href="?comment/article/<?=$this->article['article_id'];?>">
Всего коментариев: <?=$this->article_comments;?></a></div>
<?if ($this->user_id) {?>
<h3>Добавить коментарий</h3>
<form class="comment_form" method="POST" action="?comment/add/<?=$this->article['article_id'];?>">
<textarea name="text" rows="10"></textarea><br>
<input type="submit" value="Добавить коментарий">
</form>
<?}?>