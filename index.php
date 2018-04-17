<?php

spl_autoload_register(function ($file) {
    if(file_exists('controller/'.$file.'.php')){
        require_once 'controller/'.$file.'.php';
    }else{
        require_once 'model/'.$file.'.php';
    }
});

$init = new Index();

if($_POST['url']){
   // print_r()
    //die(json_encode($init->rrr()));
}

echo $init->get_body();







