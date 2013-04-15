<?php

    require_once('lib.php');
    
    global $CFG;
    
    $group_id = 0;
    
    if(!empty($_GET['groupId'])) {
        $group_id = $_GET['groupId'];     
    }
    
    $group_members = array();
        
    $mysqli = new mysqli($CFG->db_host, $CFG->db_user, $CFG->db_pass, $CFG->db_name);
    
    if (mysqli_connect_error()) {
        header('Location: login.php?error=4');
        exit;
    }
    
    // get users in/out of group
    $mysqli->set_charset("utf8");
    
    $sql="SELECT username as 'username' FROM group_membership where group_id=$group_id";
    
    if ($result = $mysqli->query($sql)) {
        if($result->num_rows==0) {
            $result->close();
            echo '';
            exit;
        } else {
            while ($row = $result->fetch_object()) {
                $group_members[]=$row->username;
            }
        }
    }
    
    $result->close();
    
    $mysqli->close();
    
    echo json_encode($group_members);