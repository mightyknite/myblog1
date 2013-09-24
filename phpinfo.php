<?php

//var_dump($_SERVER);
$result = mail('mw2@gala.net', 'subject', 'message');

if($result)
{
    echo 'Ok';
}
else
{
    echo 'Wrong';
}
phpinfo();