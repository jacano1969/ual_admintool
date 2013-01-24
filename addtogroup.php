<?php

    session_start();
   
    require_once('lib.php');
    
    // we're using a multi select list
    global $MULTI_SELECT_LIST;
    
    global $CFG;
    
    $page ='';
    
    $page .= show_header();
    
    // get all users
    $mysqli = new mysqli($CFG->db_host, $CFG->db_user, $CFG->db_pass, $CFG->db_name);
        
    $result ='';
    $results = new stdClass();
    $sql='';
        
    if (mysqli_connect_error()) {
        header('Location: login.php?error=4');
        exit;
    }
    
    $sql='select record_id, username from USERS';
    
    if ($result = $mysqli->query($sql)) {
        if($result->num_rows==0) {
            $result->close();
            header('Location: login.php?error=4');
            exit;
        } else {
            while ($row = $result->fetch_object()) {
                $results->id[]=$row->id;
                $results->value[]=$row->username;
            }
            
            $result->close();
        }
    }
    $mysqli->close();
    
    $MULTI_SELECT_LIST=true;
    $page .= multi_select_list("users", $results);
    
    $page .= show_footer();
    
    echo $page;
    
    