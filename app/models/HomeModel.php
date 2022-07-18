<?php

class HomeModel extends database{
    public function GetData(){
        $sql = "SELECT * FROM tinhthanh";
        return mysqli_query($this->conn, $sql);
    }
}
