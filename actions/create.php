<?php

session_start();
    
$action_id = '';
$record_data = '';
$action_desc='';
$grid_id = '';

require_once('../lib.php');

if(!empty($_GET['action_id'])) {
    $action_id = $_GET['action_id'];
}

if(!empty($_GET['grid_id'])) {
    $grid_id = $_GET['grid_id'];
}

if(!empty($_GET['action_desc'])) {
    $action_desc = $_GET['action_desc'];
}

if(!empty($_GET['record_data'])) {
    $record_data = $_GET['record_data'];
}

if(!empty($record_data)) {
            
    // extract json data
    $process_data = json_decode($record_data,true);
    
    $add_data = '';
    
    if(isset($process_data['add'])) {
        $add_data = $process_data['add'];
    }
    
    $create_data = new stdClass();
    $create_data->sqla = array();
    $create_data->sqlb = array();
    
    // check for record already existing
    $check_data = "select 1 as '1' ";
    $check_table = '';
    $check_data_criteria= '';
    
    // get grid data to be processed
    $grid_data=array();
    
    // get workflow_data_items_id for grid using the worklflow_action_id
    $grid_data['id']=get_workflow_data_item_for_grid($action_id);
    $grid_data['data']=$grid_id;
    
    // add grid data to items to be processed
    $add_data[]=$grid_data;
    
    // add new record
    if(isset($process_data['add'])) {
        foreach($add_data as $data) {
            
            $workflow_data_item_id = $data['id'];
            $new_data = str_replace("'","''",$data['data']);  // escape quotes
            
            // get workflow mapping for this item
            $workflow_data_mapping = get_workflow_data_mapping($workflow_data_item_id);

            $table_and_row = explode(".", $workflow_data_mapping, 3);
            
            $table_name = $table_and_row[0];
            $row_name = $table_and_row[1];
            $new_data_type = $table_and_row[2];
            
            // collect table names                
            if(array_key_exists($table_name, $create_data->sqla)) {
                
                // add to sql field list
                $create_data->sqla[$table_name] .=", $row_name";
                
                // add to sql data values
                if($new_data_type=="string") {
                    $create_data->sqlb[$table_name] .= ", '$new_data'";
                }
                
                if($new_data_type=="integer") {
                    $create_data->sqlb[$table_name] .= ", $new_data";
                }
                
                if($data['id']==$grid_data['id']){
                    $create_data->sqlb[$table_name] .= ", '$grid_id'";
                    $check_data_criteria .= "$row_name='$grid_id'";                    
                }
            } else {
                
                // just add new insert statement for table
                $create_data->sqla[$table_name]="INSERT INTO $table_name (";
                $create_data->sqlb[$table_name]='';
                
                // set check table name
                $check_table=" from $table_name where ";
                
                if($new_data_type=="string"  || $new_data_type=="data") {
                    // create field list
                    $create_data->sqla[$table_name] .= " $row_name";
                    
                    // create data values
                    $create_data->sqlb[$table_name] .= $create_data->sqlb[$table_name] . "('$new_data'";
                    
                    // check if data already exists
                    $check_data_criteria = "$row_name='$new_data' and ";
                }
                
                if($new_data_type=="integer") {
                    // create field list
                    $create_data->sqla[$table_name] .= " $row_name";
                    
                    // create data values
                    $create_data->sqlb[$table_name] .= $create_data->sqlb[$table_name] . "($new_data";
                    
                    // check if data already exists
                    $check_data_criteria = "$row_name='$new_data' and ";
                }
                
                if($data['id']==$grid_data['id']){
                    $create_data->sqlb[$table_name] .= $create_data->sqlb[$table_name] . "('$grid_id'";
                    
                    // check if data already exists
                    $check_data_criteria = "$row_name='$grid_id' and ";
                }
            }
        }
        
        // now check to see if record alredy exists
        $check_sql = $check_data . $check_table . $check_data_criteria;
        
        if(sql_record_exists($check_sql)==true) {
            echo "Error:Duplicate";
            exit;
        }
        
        
        // add sqla to sqlb
        foreach($create_data->sqla as $key => $value) {
            $sql_full = $create_data->sqla[$key] .") VALUES " . $create_data->sqlb[$key] .")";

            if(log_user_action($_SESSION['USERNAME'], $_SESSION['USERID'], "Insert Record", $action_desc, $sql_full)) {            
                // add records
                sql_insert($sql_full);
            } else {
                return false;                
            }
        }
        
        echo $sql_full;//"ok";  // if we get to here, send back some data to show everything went as planned
    }    
} else {
    return false;
}

