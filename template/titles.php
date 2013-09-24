<style type="text/css" media="all">
    .title0{
        background-color: #C0C0C0;
    }
    .title1{
        background-color: #FFFFFF;
    }
    .author{
        font-size: 75%;
        overflow: auto;
    }
   .published{
        font-size: 75%;
        color: red;
    }
    .leftimg {
       float:left;
       margin-right: 10px;
    }</style>
<?php
//Rem
echo '<h1>' . $this->title . '</h1>';
if (isset($this->list)) {
    foreach($this->list as $key=>$value){
        $user = $value['user'];
        echo '<div class="title' . ($key % 2) . '">';
        echo '<h3>Название: <a href="?article/display/' . $value['article_id'] . '">'
               . $value['article_title'] . '</a></h3>';
        if (!$value['article_published']) {
            echo '<div class="published">Статья не опубликована';
            echo '</div>';
        }
        echo '<div class="author">';
        $avatar = 'images/avatars/' . $user['user_id'] . '.jpg';
        if (file_exists($avatar)) {
            echo '<img src="' . $avatar . '" class="leftimg">';
        }
        echo'Автор: <a href="?article/user/' . $user['user_id'] . '">'
               . $user['user_name'] . '</a>';
        echo ' ' . date('d-m-Y H:i:s', $value['article_time']);
        echo '</div>';
        echo '</div>';
        echo '<div style="clear:both;"></div>';
    }
} else {
    echo '<h2>Статей нет</h2>';
}
echo '<div class="pages">Страницы: ';
for ($i = 1; $i <= $this->pages; $i++) {
    if ($this->id) {
        echo '<a href="?article/user/' . $this->id . '/' . $i . '">' . $i . ' </a>';
    } else {
        echo '<a href="?article/titles/' . $i . '">' . $i . ' </a>';
    }
}
echo '</div>';
