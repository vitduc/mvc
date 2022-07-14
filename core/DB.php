<?php
class database{
    private $host_name = 'localhost';
    private $db_name = 'exam';
    private $user_name = 'root';
    private $password = '';
    public $conn = '';

    function __construct(){
        $this->conn = mysqli_connect($this->host_name, $this->user_name, $this->password);
        mysqli_select_db($this->conn, $this->db_name);
        mysqli_query($this->conn, "SET NAMES 'utf8'");
    }


}



?>