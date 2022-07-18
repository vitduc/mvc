<?php

class HomeModel extends Db{

    public function GetData(){
        $sql = "SELECT * FROM tinhthanh ";
        return $this->connect()->query($sql);

    }

}
