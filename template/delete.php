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

defined('MYBLOG') or die("Restricted access");

        if ($this->delete_error) {
            echo '<div class="error">Ошибка удаления</div>';
        } else {
            echo '<div class="success">Удалено</div>';
        }
