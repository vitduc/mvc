<?php

class Application
{
    protected $controller = "Home";
    protected $action = "index";
    protected $params = array();

    function __construct()
    {
        $arr = $this->UrlProcess();

        //Xử lý controller
        if(isset($_GET['url'])){
            if(file_exists("./app/controllers/".$arr[0].".php")) {
                $this->controller = $arr[0];
                unset($arr[0]);
            }
        }
        require_once "./app/controllers/".$this->controller.".php";
        
        // Khỏi tạo biến ở controller
        // Mặc định là: = new Home();
        $this->controller = new $this->controller;

        //Xử lý action
        if (isset($arr[1])) {
            if (method_exists($this->controller, $arr[1])) {
                $this->action = $arr[1];
            }
            unset($arr[1]);
        }

        //Xử lý Params
        $this->params = $arr?array_values($arr):array();
        call_user_func_array(array($this->controller, $this->action), $this->params);
        
    }

    function UrlProcess()
    {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(trim($_GET['url'])));
        }
    }
}
