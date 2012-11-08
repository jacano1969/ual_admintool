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
        if($result->num_rows==0) {
            $is_user=false;
        } else {
            
            while ($row = $result->fetch_object()) {
                $_SESSION['USERID']=$row->record_id;
                $_SESSION['USERNAME']=$row->username;
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
    if(isset($_SESSION['USERID'])) {
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
    $home .= '<body id="home-page">';
    $home .= '<div class="container">';
    $home .= '<fieldset>';
    $home .= '<legend>';
    $home .= 'Welcome ' . get_logged_in_user($_SESSION['USERID']);
    $home .= '</legend>';
    
    $home .= show_navigation();
    $home .= '</fieldset>';
    
    return $home;    
}


function show_header() {
    
    $header = '';
    $header .= '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
    $header .= '<html lang="en" dir="ltr">';
    $header .= '<head>';
    $header .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
    $header .= '<title>UAL Admn Tool</title>';
    $header .= '<link href="css/style.css" type="text/css" rel="stylesheet">';
    $header .= '</head>';
    
    return $header;
}

function show_navigation() {
    
    $navigation = '';
    $navigation .= '<form name="navigation" action="index.php" method="post">';
    $navigation .= '<input type="hidden" name="action">';
    $navigation .= '<input type="submit" class="submit" value="Add" onmousedown="this.className=\'submit down\';" onmouseout="this.className=\'submit\';" onmouseup="this.className=\'submit\';" onclick="this.form.action.value=\'add\';">';
    $navigation .= '<input type="submit" class="submit" value="Update" onmousedown="this.className=\'submit down\';" onmouseout="this.className=\'submit\';" onmouseup="this.className=\'submit\';" onclick="this.form.action.value=\'update\';">';
    $navigation .= '<input type="submit" class="submit" value="Delete" onmousedown="this.className=\'submit down\';" onmouseout="this.className=\'submit\';" onmouseup="this.className=\'submit\';" onclick="this.form.action.value=\'delete\';">';
    $navigation .= '<input type="submit" class="submit" value="Search" onmousedown="this.className=\'submit down\';" onmouseout="this.className=\'submit\';" onmouseup="this.className=\'submit\';" onclick="this.form.action.value=\'search\';">';
    $navigation .= '<input type="submit" class="submit" value="Log out" onmousedown="this.className=\'submit down\';" onmouseout="this.className=\'submit\';" onmouseup="this.className=\'submit\';" onclick="this.form.action.value=\'logout\';">';
    $navigation .= '</div>';
    $navigation .= '</form>';
    
    return $navigation;
}


function show_footer() {
    
    $footer = '';
    $footer .='</body>';
    $footer .='</html>';
    
    return $footer;
}


//
// helper functions
//

function get_logged_in_user($userid) {
    global $CFG;
    
    // connect to db
    $mysqli = new mysqli($CFG->db_host, $CFG->db_user, $CFG->db_pass, $CFG->db_name);
    
    $logged_in_user = '';
    
    if (mysqli_connect_error()) {
        header('Location: login.php?error=4');
        exit;
    }
    
    // check if user can log in
    if ($result = $mysqli->query("SELECT firstname, lastname FROM users WHERE record_id=$userid")) {
        if($result->num_rows==0) {
            return $logged_in_user;
        } else {
            
            while ($row = $result->fetch_object()) {
                $logged_in_user = $row->firstname . ' '. $row->lastname;
            }
        }
        
        /* free result set */
        $result->close();
    }
    
    return $logged_in_user;
}


