<?php
class Home extends Controller{
    function index(){
        // Model
        $data =  $this->model("HomeModel");

        // View
        $this->view("Masterpage",[
            "page"=>"sp",
            "tinh"=>$data->GetData()
        ]);
    } 
}
