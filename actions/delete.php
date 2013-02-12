<?php

    session_start();

    // get id of record to be hidden
    $id=0;
    if(!empty($_GET['id'])) {
        $id=$_GET['id'];
    } else {
        echo "id false";
    }

    // get action_id
    $workflow_action_id=0;
    if(!empty($_GET['action_id'])) {
        $workflow_action_id=$_GET['action_id'];
    } else {
        echo "action_id false";
    }
    
    include_once('../dbconfig.php');
    include_once('../lib.php');
    
    
    // get workflow mapping data origin and destination by workflow action id
    $sql="select wfdm.data_origin as origin_table,wfdm.data_origin_criteria as origin_criteria, " .
         "wfdm.data_destination as destination_table from workflow_data wfd " .
         "inner join workflow_action wfa on wfa.workflow_action_id=$workflow_action_id " .
         "and wfd.status=1 and wfa.status=1 " .
         "and wfa.workflow_data_id=wfd.workflow_data_id " .
         "left join workflow_data_mapping wfdm on " .
         "wfdm.workflow_data_item_id = wfd.workflow_data_item_id";
    
    $mysqli = new mysqli($CFG->db_host, $CFG->db_user, $CFG->db_pass, $CFG->db_name);
    
    $mysqli->set_charset("utf8");
    
    $origin_criteria = '';
    $destination_table = '';
    
    // hide record (copy from origin to destination)
    if ($result = $mysqli->query($sql)) {
        while($row = $result->fetch_object()) {
            $origin_criteria=$row->origin_criteria;
            $destination_table=$row->destination_table;
        }
        
        $result->close();
    } else{
        $mysqli->close();
        echo "error excecuting sql";
    }
    
    // get column names for destination table
    $sql2="select * from $destination_table LIMIT 1";
    
    $data_table_cols = '';
    $id_col = '';
    
    if ($data_result = $mysqli->query($sql2)) {
        $data_table_cols = $data_result->fetch_fields();
        
        foreach ($data_table_cols as $table_col) {
            
            // todo: possibly change this programatically !!!!
            if($table_col->name=='id') {
                $id_col =$table_col->name;                            
            }
        }
        
        $data_result->close();
    } else{
        $mysqli->close();
        echo "error excecuting sql";
    }
    
    $delete_sql = '';
    
    // create sql for delete
    if($origin_criteria!='') {
        $delete_sql="delete from $destination_table WHERE $origin_criteria AND $id_col=$id";
    } else {
        $delete_sql="delete from $destination_table WHERE $id_col=$id";
    }
    
    // delete record 
    if($mysqli->query($delete_sql)==true){
        $mysqli->close();
        
        if(log_user_action($_SESSION['USERNAME'],$_SESSION['USERID'],"Delete Record","Delete Record",$delete_sql)) {   
            echo "The record $id has been deleted.";
        }
    } else {
        $mysqli->close();
        echo "An Error occurred.";
    }
    
    // TODO:
    // get linked work flow
    
    
    