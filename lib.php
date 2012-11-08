<?php 


function do_login($username, $password)
{
    require_once('dbconfig.php');
    
    global $CFG, $ADMINUSER;
    
    $is_user=false;    
    
    // connect to db
    $mysqli = new mysqli($CFG->db_host, $CFG->db_user, $CFG->db_pass, $CFG->db_name);
    
    if (mysqli_connect_error()) {
        header('Location: login.php?error=4');
    }
    
    // check if user can log in
    if ($result = $mysqli->query("SELECT record_id, username, FROM staff_login where username='$username' AND password='$password'")) {
        
        session_regenerate_id(true);
        
        while ($row = mysql_fetch_assoc($result)) {
            $ADMINUSER->id=$row->record_id;
            $ADMINUSER->username=$row->username;
        }
        
        /* free result set */
        $result->close();
        $is_user=true;
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
    }
    
    // incorrect username or password
    header('Location: login.php?error=1');
}


function do_logout() {
    global $ADMINUSER;
     
    $ADMINUSER=null;
    header('Location: index.php');    
}


function is_logged_in(){
    
    global $ADMINUSER;
    
    if(!empty($ADMINUSER)) {
        if(!empty($ADMINUSER->id)) {
            return true;
        } else {
            // user data/session error
            header('Location: login.php?error=3');
        }
    }else {
        // user not logged in
        header('Location: login.php');
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
