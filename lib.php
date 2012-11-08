<?php 

require_once('dbconfig.php');

function do_login($username, $password) {
    global $CFG;
    
    $is_user=false;    
    
    // connect to db
    $mysqli = new mysqli($CFG->db_host, $CFG->db_user, $CFG->db_pass, $CFG->db_name);
    
    if (mysqli_connect_error()) {
        header('Location: login.php?error=4');
        exit;
    }
    
    // check if user can log in
    if ($result = $mysqli->query("SELECT record_id, username FROM staff_login where username='$username' AND password='$password'")) {
        //session_start();
        
        if($result->num_rows==0) {
            $is_user=false;
        } else {
            
            while ($row = $result->fetch_object()) {
                $_SESSION['userid']=$row->record_id;
                $_SESSION['username']=$row->username;
            }
            
                $is_user=true;
        }
        
        /* free result set */
        $result->close();
    } else {
        // TODO:
        // check staff tale for username
            // add staff username to user_login table with default password
                // login new user
                    // session_regenerate_id(true);
                    // $is_user=true;
        $is_user=false;
    }

    // check if username password are correct
    if($is_user==true) {
        // redirect to index page
        header('Location: index.php');
        exit;
    } else {
        // incorrect username or password
        header('Location: login.php?error=1');
        exit;
    }
}


function do_logout() {
    session_start();
    session_destroy();
    header('Location: login.php');
    exit;
}


function is_logged_in() {
    //session_start(); 
    if(isset($_SESSION['userid'])) {
        return true;
    } else {
        return false;
    }
}


/**
 * Description: function to add a new record of type $record_type
 *
 * 
 */
function add($record_type) {
    
}


/**
 * Description: function to update a record of type $record_type
 *
 * 
 */
function update($record_type, $record_id) {
    
}


/**
 * Description: function to delete a record of type $record_type with id $record_id
 *
 * 
 */
function delete($record_type, $record_id) {
    
}


/**
 * Description: function to search for a record of type $record_type with name like $search_text
 *
 * 
 */
function search($record_type, $search_text) {
    
}


/**
 * Description: function to show default page, when no action has been chosen
 *
 * 
 */
function show_home() {
    
    $home = '';
    $home .= 'You are logged in';
    $home .= '<form name="logout" action="index.php" method="post">';
    $home .= '<input type="hidden" name="action" value="logout">';
    $home .= '<input type="submit" value="Log out">';
    $home .= '</form>';
    
    return $home;    
}


function show_header() {
    
    $header = '';
    $header = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
    $header .= '<html lang="en" dir="ltr">';
    $header .= '<head>';
    $header .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
    $header .= '<title>UAL Admn Tool</title>';
    $header .= '<link href="css/style.css" type="text/css" rel="stylesheet">';
    $header .= '</head>';
    
    return $header;
}


function show_footer() {
    
    $footer = '';
    $footer .='</body>';
    $footer .='</html>';
    
    return $footer;
}
