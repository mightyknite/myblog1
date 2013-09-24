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
    exit();
}
?>
<form class="form" method="POST">
Новый пароль: <input type="password" id="pass1" name="pass1"><br>
Повтор пароля: <input type="password" id="pass2" name="pass2"><br>
<input type="hidden" name="">
<div id="compare"></div>
<input id="submit" type="submit" disabled="disabled" value="Сохранить новый пароль"><br>
</form>
<script>
    $('input').keyup(function(){
        if($('#pass1').val()==$('#pass2').val()){
            $('#compare').removeClass('error').addClass('success');
            $('#compare').html('Пароли совпадают');
            $('#submit').prop('disabled', false);
        }else{
            $('#compare').removeClass('success').addClass('error');
            $('#compare').html('Пароли не совпадают');
            $('#submit').prop('disabled', true);
        }
    })
</script>
