<?php

class HomeModel extends Database{

    public function GetData(){
        $sql = "SELECT * FROM tinhthanh ";
        return $this->connect()->query($sql);

    }
}