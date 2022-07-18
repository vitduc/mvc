<?php



class Db
{

    private $host_name = 'localhost';
    private $db_name = 'exam';
    private $user_name = 'root';
    private $password = '';

    protected function connect()
    {
        $connect = new PDO("mysql:host=$this->host_name;dbname=$this->db_name; charset=utf8" , $this->user_name , $this->password);
        $connect->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $connect;
    }

}
