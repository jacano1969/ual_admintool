<?php

    require_once('lib.php');
    
    global $CFG;
    
    $group_id = 0;
    
    if(!empty($_GET['groupId'])) {
        $group_id = $_GET['groupId'];     
    }
    
    // check if we are adding/removing
    if(!empty($_GET['action'])) {
        
        $action=$_GET['action'];
        $username='';
        
        $mysqli = new mysqli($CFG->db_host, $CFG->db_user, $CFG->db_pass, $CFG->db_name);
        
        if (mysqli_connect_error()) {
            header('Location: login.php?error=4');
            exit;
        }
    
        $mysqli->set_charset("utf8");
    
        if(!empty($_GET['username'])) {
            $username = $_GET['username'];     
        }
    
        if($action=='add') {
            
            $sql="INSERT INTO group_membership (group_id,username) VALUES ($group_id,'$username')";
    
            if($result = $mysqli->query($sql)){
                $mysqli->close();
            }
            //echo $sql;
        } else if($action=='remove') {
            $sql="DELETE FROM group_membership WHERE group_id=$group_id AND username='$username'";
    
            if($result = $mysqli->query($sql)){
                $mysqli->close();
            }
            //echo $sql;
        }
    } else {
    
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
    }