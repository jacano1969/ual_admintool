<?php

    session_start();
   
    require_once('lib.php');
    
    global $CFG;
    
    $selected_group=0;
    
    $page ='';
 
    $page .= '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
    $page .= '<html lang="en" dir="ltr">';
    $page .= '<head>';
    $page .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
    $page .= '<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">';
    $page .= '<title>UAL Admn Tool</title>';
    $page .= '<link rel="stylesheet" href="css/h5bp/normalize.css">';
    $page .= '<link href="css/style.css" type="text/css" rel="stylesheet">';
    $page .= '<script src="script/libs/jquery-1.7.1.min.js" type="text/javascript"></script>';
    $page .= '<link href="css/multi_select_list.css" type="text/css" rel="stylesheet">';
    $page .= '<script src="script/jquery.twosidedmultiselect.js"></script>';
    $page .= '<script src="script/ual_groups.js" type="text/javascript"></script>';
    $page .= '</head>';
    
    $page .= '<body id="home-page">';
    $page .= '<div class="container">';
    $page .= '<fieldset>';
    $page .= '<legend>';
    $page .= 'Add members to groups';
    $page .= '</legend>';
    
    // get all users
    $mysqli = new mysqli($CFG->db_host, $CFG->db_user, $CFG->db_pass, $CFG->db_name);
        
    $result ='';
    $sql='';
        
    if (mysqli_connect_error()) {
        header('Location: login.php?error=4');
        exit;
    }
    
    // get groups
    $mysqli->set_charset("utf8");
    $sql="select group_id as id, name as name from groups order by name desc";
    
    if ($result = $mysqli->query($sql)) {
        if($result->num_rows==0) {
            // no groups - need to add a group first
            $page .='No groups have been created yet.';
            $result->close();
        } else {
            
            $page .='<label for="group">Select group </label><select class="required" id="group" name="group">';
            
            if($selected_group==0) {
                $page .= '<option id="0" name="0"></option>';
            } else {
                $page .= '<option id="0" selected="selected" name="0"></option>';
            }
            
            while ($row = $result->fetch_object()) {
                if($selected_group==$row->id) {
                    $page .='<option id="'.$row->id.'" name="'.$row->id.'" selected="selected">'.$row->name.'</option>';
                } else {
                    $page .='<option id="'.$row->id.'" name="'.$row->id.'">'.$row->name.'</option>';
                }
            }
            
            $page .='</select><br><br>';
            
            $result->close();
        }
    }
    
    $page .='<div id="loading"> </div>';
    $page .='<div id="groupmembers">';
    
    if($selected_group==0) {
        
    } else {
        // get users in and out of selected group
        $mysqli->set_charset("utf8");
        
        $sql="select USERNAME as id, concat(USERNAME,' - ',COALESCE(LASTNAME,''), ', ',COALESCE(FIRSTNAME,''), ' (', COALESCE(ROLE,'NO ROLE'),')') as value from USERS union select USERNAME as id, concat(USERNAME,' - ',COALESCE(LASTNAME,''), ', ', COALESCE(FIRSTNAME,''), ' (', COALESCE(ROLE,'NO ROLE'),')') as value from new_users order by value ASC";
    
        if ($result = $mysqli->query($sql)) {
            if($result->num_rows==0) {
                $result->close();
                header('Location: login.php?error=4');
                exit;
            }
        }
         
        $page .= multi_select_list("users", $result, 20);
        
        $result->close();
    }
    
    $page .='</div>';
    
    $page .= '<input type="submit" class="submit" name="cancel" id="cancel" value="Back">';
    $page .= '</fieldset>';
    $page .= '</div>';
    
    $mysqli->close();
    
    echo $page;
    
    