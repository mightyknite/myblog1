<?php

defined('MYBLOG') or die("Restricted access");

echo '<h1>Авторы:</h1>';

if (isset($this->rows)) {
    foreach($this->rows as $key=>$value){
        echo '<span class="blog_author"><a href="?article/user/' . $value['user_id'] . '">'
                . $value['user_name'] . '</a></span>';
        echo '<span class="blog_arts"> (Статей: ' . $value['arts'] . ')</span><br>';
    }
} else {
    echo '<h2>Авторов нет</h2>';
}
