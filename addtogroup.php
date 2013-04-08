<?php

    session_start();
   
    require_once('lib.php');
    
    global $CFG;
    
    $page ='';
    
    $page .= show_header();
    
    // TODO:
    //$page .= show_group_page();
    
    // get all users
    $mysqli = new mysqli($CFG->db_host, $CFG->db_user, $CFG->db_pass, $CFG->db_name);
        
    $result ='';
    $sql='';
        
    if (mysqli_connect_error()) {
        header('Location: login.php?error=4');
        exit;
    }
    
    $mysqli->set_charset("utf8");
    
    //$sql='select username as id, concat(username," - ",lastname, ", ",firstname, " (", COALESCE(role,"NO ROLE"),")") as value from USERS order by lastname ASC';
    
    $sql="select USERNAME as id, concat(USERNAME,' - ',COALESCE(LASTNAME,''), ', ',COALESCE(FIRSTNAME,''), ' (', COALESCE(ROLE,'NO ROLE'),')') as value from USERS union select USERNAME as id, concat(USERNAME,' - ',COALESCE(LASTNAME,''), ', ', COALESCE(FIRSTNAME,''), ' (', COALESCE(ROLE,'NO ROLE'),')') as value from new_users order by value ASC";

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
    
    
    $page .= multi_select_list("users", $result, 20);
    
    $page .= '<input type="button" id="sort" name="sort" value="Sort">';
    //$page .= show_footer();
    
    $result->close();
    $mysqli->close();
    
    echo $page;
    
    