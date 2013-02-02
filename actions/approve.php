<?php

    // get action_id
    $workflow_action_id=0;
    if(!empty($_GET['action_id'])) {
        $workflow_action_id=$_GET['action_id'];
    } else {
        echo "action_id false";
    }
    
    
    // get id of record to be approved
    $id=0;
    if(!empty($_GET['id'])) {
        $id=$_GET['id'];
    } else {
        echo "id false";
    }
    
    include_once('../dbconfig.php');
    include_once('../lib.php');
    
    // get workflow data id
    $sql="select workflow_data_id from workflow_action where workflow_action_id=$workflow_action_id";
    
    $mysqli = new mysqli($CFG->db_host, $CFG->db_user, $CFG->db_pass, $CFG->db_name);
    
    $workflow_data_id = '';
        
    if (mysqli_connect_error()) {
        header('Location: login.php?error=4');
        exit;
    }
    
    if ($result = $mysqli->query($sql)) {
        
        while($row = $result->fetch_object()) {
            $workflow_data_id=$row->workflow_data_id;
        }
        
        $result->close();
    } else{
        $mysqli->close();
        echo "error excecuting sql 1";
    }
        
    // get workflow data
    $sql="select data as 'table_name' from workflow_data where workflow_data_id=$workflow_data_id";
    
    // approve record
    if ($result2 = $mysqli->query($sql)) {
        while($row2 = $result2->fetch_object()) {
            $table_name=$row2->table_name;
        }
        
        $result2->close();
    } else{
        $mysqli->close();
        echo "error excecuting sql 2";
    }
    
    if(sql_update("update $table_name set approved=1 where id=$id")==true){
        $mysqli->close();
        echo "This record has been approved.";
    } else {
        $mysqli->close();
        echo "An Error occurred.";
    }
    
    // TODO:
    // log to workflow_log
    
    // TODO:
    // get linked work flow
    
    // 1: create new course based on approved data
    
    // 2: enrol staff member on approved course
    
    // 3: send approval email
    
    
    
    