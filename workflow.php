<?php

session_start();

require_once('lib.php');

if(is_logged_in()){
    // get params
    if(!empty($_GET['step'])){
    
        $step = false;
        
        if(!empty($_GET['step'])){
            $step = $_GET['step'];
        }       
        
        echo get_workflows($step);
    }
} else {
    header('Location: login.php');
    exit;
}