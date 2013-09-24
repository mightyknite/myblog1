<?php

defined('MYBLOG') or die("Restricted access");

?>
<script src="./js/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
tinymce.init({
    selector: "textarea",
    width : "640px",
 });
</script>
<h1>Изменение коментария</h1>
<form class="login_form" method="POST">
<textarea name="text" rows="10"><?=$this->text?></textarea><br>
<input type="submit" value="Изменить коментарий">
</form>
