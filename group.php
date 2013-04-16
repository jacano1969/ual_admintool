<?php

    session_start();
   
    require_once('lib.php');
    
    global $CFG;
    
    $group_id = 0;
    
    if(!empty($_GET['groupId'])) {
        $group_id = $_GET['groupId'];     
    }
    
    $group_members='';
        
    $mysqli = new mysqli($CFG->db_host, $CFG->db_user, $CFG->db_pass, $CFG->db_name);
    
    if (mysqli_connect_error()) {
        header('Location: login.php?error=4');
        exit;
    }
    
    // get users in/out of group
    $mysqli->set_charset("utf8");
    
    $sql="SELECT USERNAME AS id,".
	     "concat(USERNAME,' - ',COALESCE(LASTNAME,''), ', ',COALESCE(FIRSTNAME,''), ' (', COALESCE(ROLE,'NO ROLE'),')') AS value ". 
         "FROM USERS ".
         "WHERE USERNAME NOT IN(SELECT username from group_membership where group_id='.$group_id.') ".
         "UNION SELECT USERNAME AS id, ".
         "concat(USERNAME,' - ',COALESCE(LASTNAME,''), ', ', COALESCE(FIRSTNAME,''), ' (', COALESCE(ROLE,'NO ROLE'),')') AS value ".
         "FROM new_users ".
         "WHERE USERNAME NOT IN(SELECT username from group_membership where group_id='.$group_id.') ".
         "ORDER BY value ASC";
    
    if ($result = $mysqli->query($sql)) {
        if($result->num_rows==0) {
            $result->close();
            header('Location: login.php?error=4');
            exit;
        }
    }
     
    $group_members .= multi_select_list("users", $result, 20);
    
    $result->close();
    
    $mysqli->close();
    
    echo $group_members;