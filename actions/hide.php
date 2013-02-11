<?php

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
    
    $origin_table = '';
    $origin_criteria = '';
    $destination_table = '';
    
    // hide record (copy from origin to destination)
    if ($result = $mysqli->query($sql)) {
        while($row = $result->fetch_object()) {
            $origin_table=$row->origin_table;
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
    $field_list = '(';
    
    if ($data_result = $mysqli->query($sql2)) {
        $data_table_cols = $data_result->fetch_fields();
        
        foreach ($data_table_cols as $table_col) {
            
            if($table_col->name!='id') {
                $field_list .="$table_col->name,";                            
            }
        }
        
        $data_result->close();
    } else{
        $mysqli->close();
        echo "error excecuting sql";
    }
    
    // remove last comma from field list
    $field_list = rtrim($field_list, ",");
    
    $field_list .= ') ';
    
    // create sql for copy
    if($origin_criteria!='') {
        $copy_sql="insert into $destination_table $field_list $origin_table WHERE $origin_criteria AND ";
    } else {
        $copy_sql="insert into $destination_table $field_list $origin_table WHERE ";
    }
    
    // get id column
    $id_cols = array();
    $id_cols = explode(" ",$origin_table);
    $id_col = $id_cols[1];
    
    $copy_sql .=" $id_col='$id'";
    
    // hide (copy) record 
    if($mysqli->query($copy_sql)==true){
        $mysqli->close();
        echo "This record $id has been hidden.";
    } else {
        $mysqli->close();
        //echo "An Error occurred.";
        echo $copy_sql; 
    }
    
    // TODO:
    // log to workflow_log
    
    // TODO:
    // get linked work flow
    
    
    