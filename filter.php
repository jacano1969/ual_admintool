<?php

session_start();

require_once('lib.php');

if(is_logged_in()){
    // get params
    if(!empty($_GET['type'])){
    
        $type = false;
        $data = false;
        
        if(!empty($_GET['data'])){
            $type = $_GET['type'];
            $data = $_GET['data'];
        }       
        
        echo get_filter_data($type, $data);
    } else {
        echo get_filter_data(false, $data);
    }
} else {
    header('Location: login.php');
    exit;
}