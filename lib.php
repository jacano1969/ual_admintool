<?php 

require_once('dbconfig.php');

//
// Login functions
//


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


//
// Navigation functions
//


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


function do_course_request() {
    
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
    $navigation .= '<input type="submit" class="submit" value="Course Request" onmousedown="this.className=\'submit down\';" onmouseout="this.className=\'submit\';" onmouseup="this.className=\'submit\';" onclick="this.form.action.value=\'courserequest\';">';
    $navigation .= '<input type="submit" class="submit" value="Log out" onmousedown="this.className=\'submit down\';" onmouseout="this.className=\'submit\';" onmouseup="this.className=\'submit\';" onclick="this.form.action.value=\'logout\';">';
    $navigation .= '</div>';
    $navigation .= '</form>';
    
    
    //$filters = get_filter_data();
    
    //print $filters;
    
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


/**
 * Description function to return data for dropdown filters
 *
 *
 * To be used with an ajax get/data/json request
 */
function get_filter_data() {
    
    global $CFG;
    
    // TODO:
    // get all programmes, course years, courses, units for the currently logged in user
    $mysqli =  new mysqli($CFG->db_host, $CFG->db_user, $CFG->db_pass, $CFG->db_name);
    
    $filter = new stdClass();
    
    if (mysqli_connect_error()) {
        header('Location: login.php?error=4');
        exit;
    }
    
    // multidimensional arrays for data
    //$filter->colleges_list = array();       // courses.college => college name
    //$filter->schools_list = array();        // courses.school => school name
    $filter->programmes_list = array();     // courses.aos_code (where 1st character = p) => programme name
    $filter->course_years_list = array();   // courses.aos_period (4th character) => course year number
    $filter->courses_list = array();        // courses.courseid => course name
    $filter->units_list = array();          // courses.aos_code (where 1st character is a-z) => unit name
    //$filter->users_list = array();          // enrolments.recordid + staff_enrolments_ulcc.recordid => firstname . ' ' . lastname
    
    
    //
    // todo: check that logged in user has access
    //
    // programmes
    $programmes_sql = "select distinct aos_code as id, concat(aos_code, aos_period, acad_period) as name from course_structure where aos_code like('L%') order by name";
    
    // course years
    $course_years_sql = "select distinct acad_period as name from course_structure order by name";
    
    // courses
    $courses_sql = "select distinct aos_code as id, aos_description as name from courses order by name";
    
    // units
    $units_sql = "SELECT DISTINCT CONCAT(AOSCD_LINK,LNK_AOS_PERIOD,LNK_PERIOD) AS name from course_structure order by name";
    
    
    // get programmes list
    if ($result = $mysqli->query($programmes_sql)) {
        if($result->num_rows==0) {
            return $filter;
        } else {
            
            // construct json data
            while ($row = $result->fetch_object()) {
                $filter->programmes_list['id'] = $row->id;
                $filter->programmes_list['name'] = $row->name;
            }
        }
        
        /* free result set */
        $result->close();
    }
    
    // get course years list
    if ($result = $mysqli->query($course_years_sql)) {
        if($result->num_rows==0) {
            return $filter;
        } else {
            
            // construct json data
            while ($row = $result->fetch_object()) {
                $filter->course_years_list['id'] = $row->name;
                $filter->course_years_list['name'] = $row->name;
            }
        }
        
        /* free result set */
        $result->close();
    }
    
    // get courses list
    if ($result = $mysqli->query($courses_sql)) {
        if($result->num_rows==0) {
            return $filter;
        } else {
            
            // construct json data
            while ($row = $result->fetch_object()) {
                $filter->courses_list['id'] = $row->id;
                $filter->courses_list['name'] = $row->name;
            }
        }
        
        /* free result set */
        $result->close();
    }
    
    // get units list
    if ($result = $mysqli->query($units_sql)) {
        if($result->num_rows==0) {
            return $filter;
        } else {
            
            // construct json data
            while ($row = $result->fetch_object()) {
                $filter->units_list['id'] = $row->name;
                $filter->units_list['name'] = $row->name;
            }
        }
        
        /* free result set */
        $result->close();
    }
    
    return $filter;
}

