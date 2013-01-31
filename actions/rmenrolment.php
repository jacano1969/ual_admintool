<?php


    include_once('../dbconfig.php');
    include_once('../lib.php');

    $id=0;

    if(!empty($_GET['id'])) {
        $id = $_GET['id'];
    } else {
        return false;
    }
    
    //
    $sql = 'DELETE FROM STAFF_ENROLMENTS WHERE RECORD_ID=' . $id;
    $action_desc = 'Delete Course Enrolment';
    
    if(log_user_action($_SESSION['USERNAME'],$_SESSION['USERID'],"Delete Record",$action_desc,$sql)) {            
        // add records
        if(sql_delete($sql_full)) {
            echo $sql; //"ok";  // send back some data to show everyting went as planned
        }
    } else {
        return false;                
    }
    