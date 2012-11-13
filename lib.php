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
    if ($result = $mysqli->query("SELECT id, username FROM staff_login where username='$username' AND password='$password'")) {
        if($result->num_rows==0) {
            $is_user=false;
        } else {
            
            while ($row = $result->fetch_object()) {
                $_SESSION['USERID']=$row->id;
                $_SESSION['USERNAME']=$row->username;
            }
            
            $is_user=true;
        }
        
        /* free result set */
        $result->close();
        $mysqli->close();
    } else {
        // TODO:
        // check staff tale for username
            // add staff username to user_login table with default password
                // login new user
                    // $is_user=true;
        $is_user=false;
        $mysqli->close();
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
    
    // get home filters
    $home .= '<div id="mainfilters">';
    
    $home .= get_filter_data(false, false);
    
    $home .= '</div>';

    $home .= '</div>';
    
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
    $header .= '<script src="script/jquery-1.8.1.min.js" type="text/javascript"></script>';
    $header .= '<script src="script/ual_admintool.js" type="text/javascript"></script>';
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
    $navigation .= '<img src="images/logo.png" alt="UAL Logo" title="UAL Logo">';
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
        $mysqli->close();
    }
    
    return $logged_in_user;
}


/**
 * Description function to return data for dropdown filters
 *
 *
 * To be used with an ajax get/data/json request
 */
function get_filter_data($type=false, $data=false) {
    
    global $CFG;
    
    // TODO:
    // get all programmes, course years, courses, units for the currently logged in user
    $mysqli =  new mysqli($CFG->db_host, $CFG->db_user, $CFG->db_pass, $CFG->db_name);
    
    //$filter = new stdClass();
    $filters ='';
    
    if (mysqli_connect_error()) {
        header('Location: login.php?error=4');
        exit;
    }
    
    // multidimensional arrays for data
    //$filter->colleges_list = array();       // courses.college => college name
    //$filter->schools_list = array();        // courses.school => school name
    //$filter->programmes_list = array();     // courses.aos_code (where 1st character = p) => programme name
    //$filter->course_years_list = array();   // courses.aos_period (4th character) => course year number
    //$filter->courses_list = array();        // courses.courseid => course name
    //$filter->units_list = array();          // courses.aos_code (where 1st character is a-z) => unit name
    //$filter->users_list = array();          // enrolments.recordid + staff_enrolments_ulcc.recordid => firstname . ' ' . lastname
    
    
    // check that logged in user has access
    $loggedin_username = $_SESSION['USERNAME'];
    
    $filters .= '<fieldset>';
    $filters .= '<legend>';
    $filters .= 'Filters';
    $filters .= '</legend>';
    $filters .= '<form id="filters" name="filters">';
    
    // programmes
    //$programmes_sql = "select distinct aos_code as id, concat(aos_code, aos_period, acad_period) as name from course_structure where aos_code like('L%') order by name";
    //if($type==false) {
        //$programmes_sql = "select distinct cs.aos_code as id, full_description as name from course_structure cs inner join enrolments e on e.studentid='$loggedin_username' and e.courseid=concat(cs.aos_code, cs.aos_period, cs.acad_period) and cs.aos_code like('L%') order by name";
        $programmes_sql = "select distinct c.aos_code as id, c.full_description as name from courses c inner join enrolments e on e.studentid='$loggedin_username' and e.courseid=concat(c.aos_code, c.aos_period, c.acad_period) and c.aos_code like('L%') order by name";
    //}
    
    // selected items
    $selected_programme = '';
    
    // get programmes list
    if ($result = $mysqli->query($programmes_sql)) {
        if($result->num_rows==0) {
            return $filter;
        } else {  
            $filters .= '<label for="programmes">Programme:</label><select id="programmes" name="programmes">';
            $filters .='<option id="0">Select Programme ...</option>';
            
            // construct data
            while ($row = $result->fetch_object()) {
                if($type=='P') {
                    if($data==$row->id) {
                        $filters .='<option id="'.$row->id.'" selected="selected">'.$row->name.'</option>';
                        $selected_programme=$row->name;
                    } else {
                        $filters .='<option id="'.$row->id.'">'.$row->name.'</option>';
                    }
                } else {
                    $filters .='<option id="'.$row->id.'">'.$row->name.'</option>';
                }
            }
            
            $filters .= '</select>';
        }
        
        /* free result set */
        $result->close();
    }

    // course years
    if($type==false) {
        $course_years_sql = "select distinct cs.acad_period as name from course_structure cs inner join enrolments e on e.studentid='$loggedin_username' and e.courseid=concat(cs.aos_code, cs.aos_period, cs.acad_period) order by name";
    } else {
        // filter by programme 
        if($type=='P') {
            $course_years_sql = "select distinct cs.acad_period as name from course_structure cs inner join enrolments e on e.studentid='$loggedin_username' and cs.aos_code='$data' and e.courseid=concat(cs.aos_code, cs.aos_period, cs.acad_period) order by name";
        }
    }
    
    // get course years list
    if ($result = $mysqli->query($course_years_sql)) {
        if($result->num_rows==0) {
            return $filter;
        } else {
            $filters .= '<label for="courseyears">Course Year:</label><select id="courseyears" name="courseyears">';
            $filters .='<option id="0">Select Course Year ...</option>';
            
            // construct json data
            while ($row = $result->fetch_object()) {
                if($type=='P') {
                    // get course year from progeamme name
                    $course_year = substr($selected_programme,(strlen($selected_programme)-5));
                    if($course_year==$row->name) {
                        $filters .='<option id="'.$row->name.'" selected="selected">'.$row->name.'</option>';
                    } else {
                        $filters .='<option id="'.$row->name.'">'.$row->name.'</option>';
                    }
                } else {
                    $filters .='<option id="'.$row->name.'">'.$row->name.'</option>';    
                }
                
            }
            
            $filters .= '</select>';
        }
        
        /* free result set */
        $result->close();
    }
    
    $selected_courses = array();
    
    // courses
    if($type==false) {
        $courses_sql = "select distinct c.aos_code as id, c.full_description as name from courses c inner join course_structure cs on cs.aos_code=c.aos_code and cs.aos_code REGEXP '^[0-9]' inner join enrolments e on e.studentid='$loggedin_username' and c.courseid=e.courseid order by name";
    } else {
        // filter by programme 
        if($type=='P') {
            $courses_sql = "select distinct c.aos_code as id, c.full_description as name from courses c inner join course_structure cs on cs.aos_code=c.aos_code and cs.aos_code REGEXP '^[0-9]' inner join enrolments e on e.studentid='$loggedin_username' and c.courseid=e.courseid inner join course_structure cs1 on cs1.aoscd_link=c.aos_code and cs1.aos_code='$data' order by name;";
        }
    }
    
    // get courses list
    if ($result = $mysqli->query($courses_sql)) {
        if($result->num_rows==0) {
            return $filter;
        } else {
            
            $filters .= '<label for="courses">Course:</label><select id="courses" name="courses">';
            $filters .='<option id="0">Select Course ...</option>';
            
            // construct json data
            while ($row = $result->fetch_object()) {
                if($type=='P') {
                    if($data==$row->id) {
                        $filters .='<option id="'.$row->id.'" selected="selected">'.$row->name.'</option>';
                    } else {
                        $filters .='<option id="'.$row->id.'">'.$row->name.'</option>';
                    }
                    // record selected courses (used to get units)
                    $selected_courses[]=$row->id;  
                } else {
                    $filters .='<option id="'.$row->id.'">'.$row->name.'</option>';
                }
            }
            
            $filters .= '</select>';
        }
        
        /* free result set */
        $result->close();
    }
    
    // units
    if($type==false) {
        //$units_sql = "SELECT DISTINCT cs.aos_code as id, full_description AS name from course_structure cs inner join enrolments e on e.studentid='$loggedin_username' and e.courseid=concat(cs.aos_code, cs.aos_period, cs.acad_period) and order by name";
        $units_sql = "SELECT DISTINCT c.aos_code as id, c.full_description AS name from courses c inner join enrolments e on e.studentid='$loggedin_username' and e.courseid=concat(c.aos_code, c.aos_period, c.acad_period) and c.aos_code REGEXP '^[A-Z]' and c.aos_code not like('L%') order by name";
    } else {
        // filter by programme 
        if($type=='P') {
            if(!empty($selected_courses)) {
                // get units onlt for the selected courses
                $all_selected_courses = implode('\',\'',$selected_courses);
                $units_sql = "select distinct cs.aos_code as id, c.full_description as name from course_structure cs inner join enrolments e on e.studentid='$loggedin_username' and e.courseid=concat(cs.aos_code, cs.aos_period, cs.acad_period) inner join courses c on cs.aos_code=cs.aos_code and cs.aos_code REGEXP '^[A-Z]' and cs.aos_code not like('L%') inner join courses c1 on c1.courseid=c.courseid and c1.aos_code in('$all_selected_courses') order by name";
            } else {
                $units_sql = "SELECT DISTINCT c.aos_code as id, c.full_description AS name from courses c inner join enrolments e on e.studentid='$loggedin_username' and e.courseid=concat(c.aos_code, c.aos_period, c.acad_period) and c.aos_code REGEXP '^[A-Z]' and c.aos_code not like('L%') order by name";
            }
        }
    }
    
    // get units list
    if ($result = $mysqli->query($units_sql)) {
        if($result->num_rows==0) {
            return $filter;
        } else {
            
            $filters .= '<label for="units">Unit:</label><select id="units" name="units">';
            $filters .='<option id="0">Select Unit ...</option>';
            
            // construct json data
            while ($row = $result->fetch_object()) {
                if($type=='P') {
                    if($data==$row->id) {
                        $filters .='<option id="'.$row->id.'" selected="selected">'.$row->name.' ('.$row->id.')</option>';
                    } else {
                        $filters .='<option id="'.$row->id.'">'.$row->name.' ('.$row->id.')</option>';
                    }
                } else {
                    $filters .='<option id="'.$row->id.'">'.$row->name.' ('.$row->id.')</option>';
                }
            }
            
            $filters .= '</select>';
        }
        
        /* free result set */
        $result->close();
    }
    
    $filters .= '</form>';
    $filters .= '</fieldset>';
        
    $mysqli->close();
    
    return $filters;
}

