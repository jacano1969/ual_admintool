<?php

    session_start();
   
    require_once('lib.php');
    
    // we're using a multi select list
    global $MULTI_SELECT_LIST;
    
    global $CFG;
    
    $page ='';
    
    $MULTI_SELECT_LIST=true;
    
    $page .= show_header();
    
    // get all users
    $mysqli = new mysqli($CFG->db_host, $CFG->db_user, $CFG->db_pass, $CFG->db_name);
        
    $result ='';
    $sql='';
        
    if (mysqli_connect_error()) {
        header('Location: login.php?error=4');
        exit;
    }
    
    $sql='select record_id as id, username as value from USERS';
    
    if ($result = $mysqli->query($sql)) {
        if($result->num_rows==0) {
            $result->close();
            header('Location: login.php?error=4');
            exit;
        } else {
            //while ($row = $result->fetch_object()) {
            //    $results->id[]=$row->record_id;
            //    $results->value[]=$row->username;
            //}
            
            //$result->close();
        }
    }
    $mysqli->close();
    
    
    $page .= multi_select_list("users", $result);
    
    $page .= show_footer();
    
    echo $page;
    
    