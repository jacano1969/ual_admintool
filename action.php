<?php

session_start();

require_once('lib.php');

if(is_logged_in()){
    // get params
    if(!empty($_GET['action_id'])){
    
        $action_id = false;
        $step_id = false;
        $sub_step_id = false;
        
        $action_id = $_GET['action_id'];
       
        if(!empty($_GET['step_id'])){
            $step_id = $_GET['step_id'];
        }
        
        if(!empty($_GET['sub_step_id'])){
            $sub_step_id = $_GET['sub_step_id'];
        }
        
        echo get_workflow_action($step_id, $sub_step_id, $action_id);
    }
} else {
    header('Location: login.php');
    exit;
}