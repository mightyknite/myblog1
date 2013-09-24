<?php

class model_user extends model_base
{
    protected $tableName = 'myblog_user';
    protected $primaryKey = 'user_id';
    protected $retrieve = '';
    protected $timetolive = '';
    protected $fields = array('user_id', 'user_name', 'user_login', 'user_password', 'user_retr_live', 'user_retrieve');

    public function createUser()
    {
        $query = 'INSERT INTO `myblog_user` SET
            `user_login` = ?,
            `user_password` = ?,
            `user_email` = ?,
            `user_name` = ?,
            `user_registered` = ?';
        $sth = $this->db->db->prepare($query);
        $sth->bindParam(1, $_POST['login'], PDO::PARAM_STR);
        //$psw_md5 = md5($_POST['password']);
        $psw = $this->mcryptPass($_POST['password']);
        $sth->bindParam(2, $psw, PDO::PARAM_STR);
        $email = htmlspecialchars($_POST['email']);
        $sth->bindParam(3, $email, PDO::PARAM_STR);
        $name = htmlspecialchars($_POST['name']);
        $sth->bindParam(4, $name, PDO::PARAM_STR);
        $time = time();
        $sth->bindParam(5, $time, PDO::PARAM_INT);
        return $sth->execute();
    }
    
    public function mcryptPass ($pass)
    {
        $key = $this->getSecretKey();

        $encrypted_data = mcrypt_encrypt ('rijndael-256', $key, $pass, 'cbc');
        
        return base64_encode($encrypted_data);
    }

    private function getSecretKey ()
    {
        //Загружаем из файла
        return 'asdfsaSDFSDfsf';
    }
    
    public function saveRetrieve($user_id)
    {
        if (!$user_id) {
            return false;
        }
        
        $sth = $this->db->db->prepare(
            "UPDATE `{$this->tableName}` AS `{$this->alias}` SET
                `user_retrieve` = :retrieve,
                `user_retr_live` = :timetolive
                WHERE `{$this->alias}`.`{$this->primaryKey}`=:key"
        );

        if ($sth->execute(
            array(
                ':key' => $user_id,
                ':retrieve' => $this->retrieve,
                ':timetolive' => $this->timetolive
            )))
        {
            return true;
        } else {
            return false;
        }
    }

    public function saveNewPasw($user_id, $new_pasw)
    {
        if (!$user_id) {
            return false;
        }
        
        $new_pasw = $this->mcryptPass($new_pasw);
        $sth = $this->db->db->prepare(
            "UPDATE `{$this->tableName}` AS `{$this->alias}` SET
                `user_password` = :psw,
                `user_retrieve` = '',
                `user_retr_live` = ''
                WHERE `{$this->alias}`.`{$this->primaryKey}`=:key"
        );

        if ($sth->execute(
            array(
                ':key' => $user_id,
                ':psw' => $new_pasw
            )))
        {
            return true;
        } else {
            return false;
        }
    }
}