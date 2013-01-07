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
 * Description: function to process a database record
 *
 * 
 */
function process_record($record_data, $action_desc) {
    
    global $CFG;
    
    if(!empty($record_data)) {
            
        // extract json data
        $process_data = json_decode($record_data,true);
           
        $add_data = $process_data['add'];
        $update_data = $process_data['update'];
        $delete_data = $process_data['delete'];
        
        $create_data = new stdClass();
        //$create_data->tables = array();
        $create_data->sqla = array();
        $create_data->sqlb = array();
        
        $mailto = '';
        $message='';
        $subject='';
        
        // add new record
        if(!empty($add_data)) {
            foreach($add_data as $data) {
                
                $workflow_data_item_id = $data['id'];
                $new_data = str_replace("'","''",$data['data']);  // escape quotes
                
                // check if we have a mailto
                if($data['mailto']) {
                    
                    $mailto = str_replace("'","''",$data['mailto']);  // escape quotes
            
                    // get message text
                    $sql = "SELECT message FROM course_request_email where email_type=1 and status=1";

                    // get records
                    $mysqli = new mysqli($CFG->db_host, $CFG->db_user, $CFG->db_pass, $CFG->db_name);
    
                    if (mysqli_connect_error()) {
                        header('Location: login.php?error=4');
                        exit;
                    }
                    
                    if ($message_result = $mysqli->query($sql)) {
                        if($message_result->num_rows!=0) {
                            while($message_row = $message_result->fetch_object()) {
                                $message = $message_row->message;
                            }
                        }
                        $message_result->close();
                    }                  
                        
                    // create sql
                    $sql = "SELECT subject FROM course_request_email where email_type=1 and status=1";

                    // get records
                    if ($subject_result = $mysqli->query($sql)) {
                        if($subject_result->num_rows!=0) {
                            while($subject_row = $subject_result->fetch_object()) {
                                $subject = $subject_row->subject;
                            }
                        }
                        
                        $subject_result->close();
                    }
                    
                    $mysqli->close();
                } else {
                //if($mailto=='') {
                    // get the table and column for this new data
                    $workflow_data = get_workflow_data($workflow_data_item_id);
                    
                    $table_and_row = explode(".", $workflow_data, 3);
                    $table_name = $table_and_row[0];
                    $row_name = $table_and_row[1];
                    $new_data_type = $table_and_row[2];
                    
                    // collect table names                
                    if(array_key_exists($table_name, $create_data->sqla)) {
            
                        // add to sql field list
                        $create_data->sqla[$table_name] .=", $row_name";
                        
                        // add to sql data values
                        if($new_data_type=="string") {
                            $create_data->sqlb[$table_name] .= ", '$new_data'";
                        }
                        
                        if($new_data_type=="integer") {
                            $create_data->sqlb[$table_name] .= ", $new_data";
                        }
                    } else {
                        
                        // just add new insert statement for table
                        $create_data->sqla[$table_name]="INSERT INTO $table_name (";
                        
                        if($new_data_type=="string") {
                            // create field list
                            $create_data->sqla[$table_name] .= " $row_name";
                            
                            // create data values
                            $create_data->sqlb[$table_name] .= $create_data->sqlb[$table_name] . "('$new_data'";
                        }
                        
                        if($new_data_type=="integer") {
                            // create field list
                            $create_data->sqla[$table_name] .= " $row_name";
                            
                            // create data values
                            $create_data->sqlb[$table_name] .= $create_data->sqlb[$table_name] . "($new_data";
                        }
                    }
                }
            }
            
            // add sqla to sqlb
            foreach($create_data->sqla as $key => $value) {
                $sql_full = $create_data->sqla[$key] .") VALUES " . $create_data->sqlb[$key] .")";

                if(log_user_action($_SESSION['USERNAME'], $_SESSION['USERID'], "Insert Record", $action_desc, $sql_full)) {            
                    // add records
                    sql_insert($sql_full);
                } else {
                    return false;                
                }
            }
            
            // check if an email is to be sent        
            if($mailto!='') {
                
                //$subject ='Test email';
                //$message = 'This is a test email.';
                
                $headers  = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $headers .= 'To: ' . $mailto . "\r\n";
                $headers .= 'From: UAL AdminTool' . "\r\n";
                
                if($message!='') {
                    mail($mailto, $subject, $message, $headers);
                }
            }
            
            echo "ok";  // if we get to here, send back some data to show everything went as planned
            
        } else {
            // TODO: handle error
            echo $process_data;
        }
        
        
        
// TODO: find UQ ID to update record
        // update existing record
        if(!empty($update_data)) {
            foreach($update_data as $data) {
                
                $workflow_data_item_id = $data['id'];
                $new_data = str_replace("'","''",$data['data']);  // escape quotes
                
                $unique_id = 0;
                if($data['data']=='id') {
                    $unique_id = $data['id'];
                } else {
                
                    // get the table and column for this new data
                    $workflow_data = get_workflow_data($workflow_data_item_id);
                    
                    $table_and_row = explode(".", $workflow_data, 3);
                    $table_name = $table_and_row[0];
                    $row_name = $table_and_row[1];
                    $new_data_type = $table_and_row[2];
                    
                    // collect table names                
                    if(array_key_exists($table_name, $create_data->sqla)) {
            
                        // add to sql field list
                        $create_data->sqla[$table_name] .=" $row_name=";
                        
                        // add to sql data values
                        if($new_data_type=="string" || $new_data_type=="data") {
                            $create_data->sqla[$table_name] .= "'$new_data',";
                        }
                        
                    } else {
                        
                        // just add new update statement for table
                        $create_data->sqla[$table_name]="UPDATE $table_name SET ";
                        
                        if($new_data_type=="string" || $new_data_type=="data") {
                            // create data values
                            $create_data->sqla[$table_name] .= " $row_name=";
                            
                            // create field list
                            $create_data->sqla[$table_name] .= "'$new_data',";
                        }
                    } 
                }
            }
            
            // add sqla to sql_full
            foreach($create_data->sqla as $key => $value) {

                $sql_full = $create_data->sqla[$key];
                        
                $sql_full = substr($sql_full,0,-1);
                
                $sql_full .= " WHERE id=" . $unique_id;
                
                if(log_user_action($_SESSION['USERNAME'],$_SESSION['USERID'],"Update Record",$action_desc,$sql_full)) {            
                    // add records
                    if(sql_update($sql_full)) {
                        echo $sql_full ; //"ok";  // send back some data to show everyting went as planned
                    }
                } else {
                    return false;                
                }
            }
        } else {
            // TODO: handle error
            echo $process_data;
        }
        
        
        
        // delete existing record
        if(!empty($delete_data)) {
            
        }
        
    } else {
        return false;
    }

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
    
    //$home .= show_navigation();
    $home .= '</fieldset>';
    
    // get home filters
    /*$home .= '<div id="mainfilters">';
    
    $home .= get_filter_data(false, false);
    
    $home .= '</div>';

    $home .= '</div>';*/
    
    // workflow popup
    $home .= '<div id="hiddenlightbox">';
    $home .= get_workflows(false);
    $home .= '</div>';
    
    $home .= '<div id="helpbox">';
    $home .= '<h2>Help</h2>';
    $home .= '<div id="helptext"></div>';
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
    $header .= '<script src="script/jquery.lightbox_me.js" type="text/javascript"></script>';
    $header .= '<script src="script/jquery.validate.min.js" type="text/javascript"></script>';
    $header .= '<script type="text/javascript" src="script/jquery.tablesorter.js"></script>';
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


/**
 * Description: function to do an sql insert
 * 
 */
function sql_insert($sql) {
    global $CFG;
    
    // connect to db
    $mysqli = new mysqli($CFG->db_host, $CFG->db_user, $CFG->db_pass, $CFG->db_name);
    
    if (mysqli_connect_error()) {
        return false;
    }

    // TODO:
    // do we need to check this syntax
    $sql_insert = $sql;
    
    if($result = $mysqli->query($sql_insert)){
        $mysqli->close();
        return true;
    } else {
        return false;
    }
}


/**
 * Description: function to do an sql update
 * 
 */
function sql_update($sql) {
    global $CFG;
    
    // connect to db
    $mysqli = new mysqli($CFG->db_host, $CFG->db_user, $CFG->db_pass, $CFG->db_name);
    
    if (mysqli_connect_error()) {
        return false;
    }

    // TODO:
    // do we need to check this syntax
    $sql_update = $sql;
    
    if($result = $mysqli->query($sql_update)){
        $mysqli->close();
        return true;
    } else {
        return false;
    }
}


/**
 * Description: function to log user activity
 * 
 */
function log_user_action($username, $userid, $action, $description, $data) {
    global $CFG;
    
    // connect to db
    $mysqli = new mysqli($CFG->db_host, $CFG->db_user, $CFG->db_pass, $CFG->db_name);
    
    if (mysqli_connect_error()) {
        return false;
    }

    $data = str_replace("'","''",$data);
    
    $log_sql= "INSERT INTO workflow_log (username, record_id, time, action, description, data) " .
              "VALUES ('$username',$userid,UNIX_TIMESTAMP(),'$action','$description','$data')";
    
    if($result = $mysqli->query($log_sql)){
        $mysqli->close();
        return true;
    }
    
    return false;    
}


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
    
    // get all programmes, course years, courses, units for the currently logged in user
    $mysqli =  new mysqli($CFG->db_host, $CFG->db_user, $CFG->db_pass, $CFG->db_name);
    
    //$filter = new stdClass();
    $filters ='';
    
    $filters .= '<div id="mainfilters">';
    
    if (mysqli_connect_error()) {
        header('Location: login.php?error=4');
        exit;
    }    
    
    // check that logged in user has access
    $loggedin_username = $_SESSION['USERNAME'];
    
    $filters .= '<fieldset>';
    $filters .= '<legend>';
    $filters .= 'Filters';
    $filters .= '</legend>';
    $filters .= '<form id="filters" name="filters">';
    
    // programmes
    if($type==false || $type=='P') {
        // get all programmes
        $programmes_sql = "select distinct c.aos_code as id, c.full_description as name, c.acad_period as year from COURSES c inner join STAFF_ENROLMENTS e on e.staffid='$loggedin_username' and e.courseid=concat(c.aos_code, c.aos_period, c.acad_period) and c.aos_code like('L%') order by name";
    } else if($type=='C'){
        // get programmes for selected course
        $programmes_sql = "select distinct c.aos_code as id, c.full_description as name, c.acad_period as year from COURSES c inner join STAFF_ENROLMENTS e on e.staffid='$loggedin_username' and e.courseid=concat(c.aos_code, c.aos_period, c.acad_period) and c.aos_code like('L%') inner join course_structure cs1 on cs1.aoscd_link='$data' and cs1.aos_code=c.aos_code order by name";
    }
    
    // selected items
    $selected_programme = '';
    $selected_programme_year = '';
    
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
                        $selected_programme_year=$row->year;
                    } else {
                        $filters .='<option id="'.$row->id.'">'.$row->name.'</option>';
                    }
                } else {
                    if($type=='C') {
                        $selected_programme=$row->name;
                        $selected_programme_year=$row->year;
                        $filters .='<option id="'.$row->id.'" selected="selected">'.$row->name.'</option>';
                    } else {
                        $filters .='<option id="'.$row->id.'">'.$row->name.'</option>';
                    }
                }
            }
            
            $filters .= '</select>';
        }
        
        $filters .='</div>';
        
        /* free result set */
        $result->close();
    }

    // course years
    if($type==false) {
        $course_years_sql = "select distinct cs.acad_period as name from COURSE_STRUCTURE cs inner join STAFF_ENROLMENTS e on e.staffid='$loggedin_username' and e.courseid=concat(cs.aos_code, cs.aos_period, cs.acad_period) order by name";
    } else {
        // filter by programme 
        if($type=='P') {
            $course_years_sql = "select distinct cs.acad_period as name from COURSE_STRUCTURE cs inner join STAFF_ENROLMENTS e on e.staffid='$loggedin_username' and cs.aos_code='$data' and e.courseid=concat(cs.aos_code, cs.aos_period, cs.acad_period) order by name";
        }
        
        // filter by course 
        if($type=='C') {
            $course_years_sql = "select distinct cs.acad_period as name from COURSE_STRUCTURE cs inner join STAFF_ENROLMENTS e on e.staffid='$loggedin_username' and cs.aos_code='$data' and e.courseid=concat(cs.aos_code, cs.aos_period, cs.acad_period) order by name";
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
                    $course_year = $selected_programme_year;
                    if($course_year==$row->name) {
                        $filters .='<option id="'.$row->name.'" selected="selected">'.$row->name.'</option>';
                    } else {
                        $filters .='<option id="'.$row->name.'">'.$row->name.'</option>';
                    }
                } else {
                    if($type=='C') {
                        // get course year from progeamme name
                        $course_year = $selected_programme_year;
                        if($course_year==$row->name) {
                            $filters .='<option id="'.$row->name.'" selected="selected">'.$row->name.'</option>';
                        } else {
                            $filters .='<option id="'.$row->name.'">'.$row->name.'</option>';
                        }
                    } else {
                        $filters .='<option id="'.$row->name.'">'.$row->name.'</option>';
                    }
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
        $courses_sql = "select distinct c.aos_code as id, c.full_description as name from COURSES c inner join COURSE_STRUCTURE cs on cs.aos_code=c.aos_code and cs.aos_code REGEXP '^[0-9]' inner join STAFF_ENROLMENTS e on e.staffid='$loggedin_username' and c.courseid=e.courseid order by name";
    } else {
        // filter by programme 
        if($type=='P') {
            $courses_sql = "select distinct c.aos_code as id, c.full_description as name from COURSES c inner join COURSE_STRUCTURE cs on cs.aos_code=c.aos_code and cs.aos_code REGEXP '^[0-9]' inner join STAFF_ENROLMENTS e on e.staffid='$loggedin_username' and c.courseid=e.courseid inner join COURSE_STRUCTURE cs1 on cs1.aoscd_link=c.aos_code and cs1.aos_code='$data' order by name;";
        }
        
        // filter by programme 
        if($type=='C') {
            // TODO:
            //$courses_sql = "select distinct c.aos_code as id, c.full_description as name from courses c inner join course_structure cs on cs.aos_code=c.aos_code and cs.aos_code REGEXP '^[0-9]' inner join enrolments e on e.studentid='$loggedin_username' and c.courseid=e.courseid inner join course_structure cs1 on cs1.aoscd_link=c.aos_code and cs1.aos_code='$data' order by name;";
            $courses_sql = "select c.aos_code as id, c.full_description as name from COURSES c where c.aos_code='$data'";
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
                    if($type=='C') {
                        $filters .='<option id="'.$row->id.'" selected="selected">'.$row->name.'</option>';
                    } else {
                        $filters .='<option id="'.$row->id.'">'.$row->name.'</option>';
                    }
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
        $units_sql = "SELECT DISTINCT c.aos_code as id, c.full_description AS name from COURSES c inner join STAFF_ENROLMENTS e on e.staffid='$loggedin_username' and e.courseid=concat(c.aos_code, c.aos_period, c.acad_period) and c.aos_code REGEXP '^[A-Z]' and c.aos_code not like('L%') order by name";
    } else {
        // filter by programme 
        if($type=='P') {
            if(!empty($selected_courses)) {
                // get units onlt for the selected courses
                $all_selected_courses = implode('\',\'',$selected_courses);
                $units_sql = "select distinct cs.aos_code as id, c.full_description as name from COURSE_STRUCTURE cs inner join enrolments e on e.staffid='$loggedin_username' and e.courseid=concat(cs.aos_code, cs.aos_period, cs.acad_period) inner join COURSES c on cs.aos_code=cs.aos_code and cs.aos_code REGEXP '^[A-Z]' and cs.aos_code not like('L%') inner join COURSES c1 on c1.courseid=c.courseid and c1.aos_code in('$all_selected_courses') order by name";
            } else {
                $units_sql = "SELECT DISTINCT c.aos_code as id, c.full_description AS name from COURSES c inner join STAFF_ENROLMENTS e on e.staffid='$loggedin_username' and e.courseid=concat(c.aos_code, c.aos_period, c.acad_period) and c.aos_code REGEXP '^[A-Z]' and c.aos_code not like('L%') order by name";   
            }
        }
        
        if($type=='C') {
            $units_sql = " SELECT DISTINCT c.aos_code as id, c.full_description AS name from COURSES c inner join STAFF_ENROLMENTS e on e.staffid='$loggedin_username' and e.courseid=concat(c.aos_code, c.aos_period, c.acad_period) and c.aos_code REGEXP '^[A-Z]' inner join COURSE_STRUCTURE cs on cs.aos_code='$data' and cs.aoscd_link = c.aos_code order by name";
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
                    if($type=='C') {
                        $filters .='<option id="'.$row->id.'">'.$row->name.' ('.$row->id.')</option>';
                    } else {
                        $filters .='<option id="'.$row->id.'">'.$row->name.' ('.$row->id.')</option>';   
                    }
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



//
// Workflow
//


/**
 * Description: function to retreive workflow data
 *
 */
function get_workflow_data($workflow_data_item_id) {
    global $CFG;
    
    $data = '';
    $data_type = '';
    
    if(!empty($workflow_data_item_id) && $workflow_data_item_id!='') {
        // get wokflow data
        $mysqli =  new mysqli($CFG->db_host, $CFG->db_user, $CFG->db_pass, $CFG->db_name);
        
        if (mysqli_connect_error()) {
            return false;
        }
        
        $workflow_data_sql="select wfd.data as data, wfdt.data_type as data_type from workflow_data wfd " .
                           "inner join workflow_data_type wfdt on wfd.workflow_data_type_id=wfdt.workflow_data_type_id " .
                           "and workflow_data_item_id=$workflow_data_item_id";
        
        if ($result = $mysqli->query($workflow_data_sql)) {
            if($result->num_rows==0) {
                return false;
            } else {  

                // construct data
                while ($row = $result->fetch_object()) {
                    $data = $row->data;
                    $data_type = $row->data_type;
                }
            }
        }
        
        $result->close();
        
        $mysqli->close();
    } else {
        return false;
    }
    
    return $data .'.'. $data_type;
}


/**
 * Description: function to retreive all workflows
 *
 *
 */
function get_workflows($step_id=false) {
    global $CFG;
    
    // get wokflows, steps and sub steps
    $mysqli =  new mysqli($CFG->db_host, $CFG->db_user, $CFG->db_pass, $CFG->db_name);
    
    $workflows ='';
    $workflow_action = 0;
    
    if (mysqli_connect_error()) {
        header('Location: login.php?error=4');
        exit;
    }
    
    $workflow .= '<fieldset>';
    $workflow .= '<legend>';
    $workflow .= 'Select Action';
    $workflow .= '</legend>';
    $workflow .= '<form id="workflow" name="workflow">';
    
    // get all active workflows
    $workflow_sql="select workflow_id as id, name as name, description as description from workflow where status=1";

    if($step_id==false) {
        $current_workflow_step_id='0';
    } else {
        $current_workflow_step_id=$step_id;
    }
    
     // get workflows
    if ($workflow_result = $mysqli->query($workflow_sql)) {
        if($workflow_result->num_rows==0) {
            return 'An error has occured.';
        } else {  
            $workflow .= '<select id="workflows" name="workflows">';
            $workflow .='<option id="0">Select Action ...</option>';
            
            // construct data
            while ($workflow_row = $workflow_result->fetch_object()) {
                $workflow .= '<optgroup help="'.$workflow_row->description.'" label="'.$workflow_row->name.'">';
                $workflow_id = $workflow_row->id;
                
                // get all active workflow steps for each workflow
                $workflow_step_sql="select workflow_step_id as id, name as name, description as description, workflow_action_id as action from workflow_step where status=1 and workflow_id=$workflow_id";
               
                if ($workflow_step_result = $mysqli->query($workflow_step_sql)) {
                    if($workflow_step_result->num_rows==0) {
                        //continue;
                    } else {
                        
                        // construct data
                        while($workflow_step_row = $workflow_step_result->fetch_object()) {
                            if($current_workflow_step_id==$workflow_step_row->id) {
                                
                                // set the currently selected action
                                $workflow_action = $workflow_step_row->action;
                                
                                $workflow .='<option id="'.$workflow_step_row->id.'" selected="selected">'.$workflow_step_row->name.'</option>';
                            } else {
                                $workflow .='<option help="'.$workflow_step_row->description.'" id="'.$workflow_step_row->id.'">'.$workflow_step_row->name.'</option>';
                            }
                        }
                    }
                    
                    $workflow_step_result->close();
                }
            }
            
            $workflow .= '</optgroup>';
            $workflow .= '<optgroup help="Create or edit workflows" label="Workflows">';
            $workflow .= '<option id="10000" help="Open the workflow designer">Workflow Designer</option>';
            $workflow .= '</optgroup>';
            $workflow .= '</select>';
        }
        
        $workflow_result->close();
    }
    
    // get all active workflow sub steps for the currently selected workflow step
    if($step_id!='0') {
        $workflow_sub_step_sql="select workflow_sub_step_id as id, name as name, workflow_action_id as action, description as description from workflow_sub_step where status=1 and workflow_step_id=$step_id";

        if ($workflow_sub_step_result = $mysqli->query($workflow_sub_step_sql)) {
            if($workflow_sub_step_result->num_rows==0) {
                //continue;
            } else {
                
                $workflow .= '<select id="workflow_sub_steps" name="workflow_sub_steps">';
                $workflow .='<option id="0">Select Action ...</option>';
                    
                while($workflow_sub_step_row = $workflow_sub_step_result->fetch_object()) {
                    $workflow .='<option help="'.$workflow_sub_step_row->description.'" data="'.$workflow_sub_step_row->action.'" id="'.$workflow_sub_step_row->id.'">'.$workflow_sub_step_row->name.'</option>';
                }
                
                $workflow .= '</select>';
            }
            
            $workflow_sub_step_result->close();
            
        }
    }
    
    // show disabled ok button
    $workflow .='<input type="button" class="submit" value="Reset" name="reset" id="reset">';
    
    // get action for workflow step
    if($workflow_action!='0') {
        $workflow .='<input type="hidden" id="step_action" name="step_action" value="'.$workflow_action.'">';
        $workflow .='<input type="hidden" id="step_id" name="step_id" value="'.$step_id.'">';
        $workflow .='<input type="submit" value="Ok" name="ok" class="close" id="ok">';
    } else {
        $workflow .='<input type="hidden" id="sub_step_action" name="sub_step_action" value="0">';
        $workflow .='<input type="hidden" id="sub_step_id" name="sub_step_id" value="0">';
        $workflow .='<input type="submit" value="Ok" name="ok" class="close" id="ok" disabled="disabled">';
    }
        
    $workflow .= '</form>';
    $workflow .= '</fieldset>';
        
    $mysqli->close();
    
    return $workflow;
}


/**
 * Description: Get data for workflow action
 *
 */
function get_workflow_action($step_id, $sub_step_id, $action_id) {
    global $CFG;
    
    // get wokflow action
    $mysqli =  new mysqli($CFG->db_host, $CFG->db_user, $CFG->db_pass, $CFG->db_name);
    
    $workflow_action = '';
    $action_name='';
    
    if (mysqli_connect_error()) {
        header('Location: login.php?error=4');
        exit;
    }
    
    // get workflow step or sub step details
    if($step_id!=false){
        
        $step_sql = "select name as name, description as description from workflow_step where status=1 and workflow_step_id=$step_id";
        
        if ($result = $mysqli->query($step_sql)) {
            while($row = $result->fetch_object()) {
                $action_name = $row->name;    
            }
            
            $result->close();
        } else{
            $mysqli->close();
            return 'An error has occured.';
        }
    } else if($sub_step_id!=false) {
        
        $sub_step_sql = "select name as name, description as description from workflow_sub_step where status=1 and workflow_sub_step_id=$sub_step_id";
        
        if ($result = $mysqli->query($sub_step_sql)) {
            while($row = $result->fetch_object()) {
                $action_name = $row->name;    
            }
            
            $result->close();
        } else{
            $mysqli->close();
            return 'An error has occured.';
        }
    } else {
        $mysqli->close();
        return 'An error has occured.';
    }
    
    // get workflow action details
    $workflow_action_sql ="select `add` as add_button,`update` as update_button,`delete` as delete_button, cancel as cancel_button, send_email as send_email, workflow_data_id as workflow_data_id from workflow_action where status=1 and workflow_action_id=$action_id";
    
    $add_button = '';
    $update_button = '';
    $delete_button = '';
    $cancel_button = '';
    $send_email = '';
    $workflow_data_id = '';
    
    if ($result = $mysqli->query($workflow_action_sql)) {
        while($row = $result->fetch_object()) {
            $add_button = $row->add_button;
            $update_button = $row->update_button;
            $delete_button = $row->delete_button;
            $cancel_button = $row->cancel_button;
            $send_email = $row->send_email;
            $workflow_data_id = $row->workflow_data_id;
        }
        
        $result->close();
    } else{
        $mysqli->close();
        return 'An error has occured.';
    }
    
    // get workflow data and data types and mappings
    $workflow_data_details = "select wfd.workflow_data_item_id as item_id, wfd.label as label, wfd.name as name,".
                             "wfd.description as description, wfd.mandatory as mandatory, wfd.validate_for as validate, wfdt.name as type,".
                             "wfdm.data_type as data_type,wfdm.data_origin as value,wfdm.data_origin_criteria as criteria from workflow_data wfd ".
                             "inner join workflow_action wfa on wfa.workflow_action_id=$action_id and wfa.status=1 ".
                             "and wfa.workflow_data_id=wfd.workflow_data_id ".
                             "inner join workflow_data_type wfdt on ".
                             "wfd.workflow_data_type_id=wfdt.workflow_data_type_id ".
                             "left join workflow_data_mapping wfdm on ".
                             "wfdm.workflow_data_item_id = wfd.workflow_data_item_id ".
                             "and wfd.status=1 and wfdt.status=1 order by wfd.display_order";
    
    $workflow_form = '';
    
    // parse workflow details
    if ($result = $mysqli->query($workflow_data_details)) {
        while($row = $result->fetch_object()) {
            
            // draw text box
            if($row->type=='text') {
                
                // check if mandatory==1
                if($row->mandatory==1){
                    // get any special validation
                    $validate = ($row->validate!='') ? ' '.$row->validate : '';
                    $workflow_form .= '<label for="'.$row->name.'">'.$row->label.'</label><em>*</em><div id="helplabel">'.$row->description.'</div><input data="'.$row->item_id.'" ';
                    
                    if($row->data_type=='data') {
                        
                        // extract database details for data
                        $databases = array();
                        $tables = array();
                        $columns = array();
                                            
                        $data_details = explode(",",$row->value);  // split into db.table.col array
                        
                        $temp = array();
                        foreach($data_details as $detail) {
                           $temp = explode(".",$detail);
                           $database[] = $temp[0];
                           $tables[] = $temp[1];
                           $columns[] = $temp[2];
                        }
                        
                        // create sql
                        $sql = "SELECT ".$columns[0]." as name FROM ".$database[0].".".$tables[0];
                        
                        if(!empty($row->criteria)) {
                            $sql .=" WHERE $row->criteria";    
                        }
                        
                        // get records
                        if ($data_result = $mysqli->query($sql)) {
                            while($data_row = $data_result->fetch_object()) {
                                $workflow_form .= 'value="'.$data_row->name.'" ';
                            }
                            
                            $data_result->close();
                        }                  
                    }
                    
                    $workflow_form .= 'class="required'.$validate.'" type="text" id="'.$row->name.'" name="'.$row->name.'">';
                } else {
                    $workflow_form .= '<label for="'.$row->name.'">'.$row->label.'</label><div id="helplabel">'.$row->description.'</div><input data="'.$row->item_id.'" type="text" id="'.$row->name.'" ';
                    
                    if($row->data_type=='data') {
                        // extract database details for data
                        $databases = array();
                        $tables = array();
                        $columns = array();
                                            
                        $data_details = explode(",",$row->value);  // split into db.table.col array
                        
                        $temp = array();
                        foreach($data_details as $detail) {
                           $temp = explode(".",$detail);
                           $database[] = $temp[0];
                           $tables[] = $temp[1];
                           $columns[] = $temp[2];
                        }
                        
                        // create sql
                        $sql = "SELECT ".$columns[0]." as name FROM ".$database[0].".".$tables[0];
                        
                        if(!empty($row->criteria)) {
                            $sql .=" WHERE $row->criteria";    
                        }
                        
                        // get records
                        if ($data_result = $mysqli->query($sql)) {
                            while($data_row = $data_result->fetch_object()) {
                                $workflow_form .= 'value="'.$data_row->name.'" ';
                            }
                            
                            $data_result->close();
                        }                  
                    }
                    
                    $workflow_form .= 'name="'.$row->name.'">';
                }
            }
            
            
            // draw textarea
            if($row->type=='textarea') {
                
                // TODO: check if mandatory==1
                if($row->mandatory==1){
                    // get any special validation
                    $validate = ($row->validate!='') ? ' '.$row->validate : '';
                    $workflow_form .= '<label for="'.$row->name.'">'.$row->label.'</label><em>*</em><div id="helplabel">'.$row->description.'</div><textarea data="'.$row->item_id.'" class="required'.$validate.'" id="'.$row->name.'" name="'.$row->name.'">';
                    
                    if($row->data_type=='data') {
                        // extract database details for data
                        $databases = array();
                        $tables = array();
                        $columns = array();
                                            
                        $data_details = explode(",",$row->value);  // split into db.table.col array
                        
                        $temp = array();
                        foreach($data_details as $detail) {
                           $temp = explode(".",$detail);
                           $database[] = $temp[0];
                           $tables[] = $temp[1];
                           $columns[] = $temp[2];
                        }
                        
                        // create sql
                        $sql = "SELECT ".$columns[0]." as name FROM ".$database[0].".".$tables[0];
                        
                        if(!empty($row->criteria)) {
                            $sql .=" WHERE $row->criteria";    
                        }
                        
                        // get records
                        if ($data_result = $mysqli->query($sql)) {
                            while($data_row = $data_result->fetch_object()) {
                                $workflow_form .= $data_row->name;
                            }
                            
                            $data_result->close();
                        }                  
                    }
                    
                    $workflow_form .= '</textarea>';
                } else {
                    $workflow_form .= '<label for="'.$row->name.'">'.$row->label.'</label><div id="helplabel">'.$row->description.'</div><textarea data="'.$row->item_id.'" id="'.$row->name.'" name="'.$row->name.'">';
                    
                    if($row->data_type=='data') {
                        // extract database details for data
                        $databases = array();
                        $tables = array();
                        $columns = array();
                                            
                        $data_details = explode(",",$row->value);  // split into db.table.col array
                        
                        $temp = array();
                        foreach($data_details as $detail) {
                           $temp = explode(".",$detail);
                           $database[] = $temp[0];
                           $tables[] = $temp[1];
                           $columns[] = $temp[2];
                        }
                        
                        // create sql
                         $sql = "SELECT ".$columns[0]." as name FROM ".$database[0].".".$tables[0];
                        
                        if(!empty($row->criteria)) {
                            $sql .=" WHERE $row->criteria";    
                        }
                        
                        // get records
                        if ($data_result = $mysqli->query($sql)) {
                            while($data_row = $data_result->fetch_object()) {
                                $workflow_form .= $data_row->name;
                            }
                            
                            $data_result->close();
                        }                  
                    }
                    
                    $workflow_form .= '</textarea>';
                }
            }
            
            // draw hidden field
            if($row->type=='hidden') {
                
                // construct hidden value for mailto
                if($row->data_type=='mailto') {
                    $workflow_form .= '<input data="'.$row->item_id.'" type="hidden" id="'.$row->name.'" name="'.$row->name.'" value="'.$row->value.'">';
                }
                
                // hidden value for session vars
                if($row->data_type=='session') {
                    $session_var = $row->value;
                    $workflow_form .= '<input data="'.$row->item_id.'" type="hidden" id="'.$row->name.'" name="'.$row->name.'" value="'.$_SESSION[$session_var].'">';
                }
                
                if($row->data_type=='data') {
                    // extract database details for data
                    $databases = array();
                    $tables = array();
                    $columns = array();
                                        
                    $data_details = explode(",",$row->value);  // split into db.table.col array
                    
                    $temp = array();
                    foreach($data_details as $detail) {
                       $temp = explode(".",$detail);
                       $database[] = $temp[0];
                       $tables[] = $temp[1];
                       $columns[] = $temp[2];
                    }
                    
                    // create sql
                     $sql = "SELECT ".$columns[0]." as name FROM ".$database[0].".".$tables[0];
                    
                    if(!empty($row->criteria)) {
                        $sql .=" WHERE $row->criteria";    
                    }
                    
                    // get records
                    if ($data_result = $mysqli->query($sql)) {
                        while($data_row = $data_result->fetch_object()) {
                            $workflow_form .= '<input data="'.$data_row->name.'" type="hidden" id="'.$columns[0].'" name="'.$row->name.'" value="'.$row->name.'">';
                        }
                        
                        $data_result->close();
                    }                  
                }
            }
            
            // draw dropdown select box
            if($row->type=='dropdown') {
                
                // TODO: check if mandatory==1
                if($row->mandatory==1){
                    $workflow_form .= '<label for="'.$row->name.'">'.$row->label.'</label><em>*</em><div id="helplabel">'.$row->description.'</div><select data="'.$row->item_id.'" class="required" id="'.$row->name.'" name="'.$row->name.'">';
                } else {
                    $workflow_form .= '<label for="'.$row->name.'">'.$row->label.'</label><div id="helplabel">'.$row->description.'</div><select data="'.$row->item_id.'" id="'.$row->name.'" name="'.$row->name.'">';
                }
                
                // check where we get the data from
                if($row->data_type=='list') {
                    
                    // get list items
                    $list_items = array();
                    
                    $list_items = explode(",",$row->value);
                    
                    foreach($list_items as $item) {
                        $workflow_form .='<option id="'.$item.'" name="'.$item.'">'.$item.'</option>';
                    }
                }
                else if($row->data_type=='data') {
                    
                    // extract database details for data
                    $databases = array();
                    $tables = array();
                    $columns = array();
                                        
                    $data_details = explode(",",$row->value);  // split into db.table.col array
                    
                    $temp = array();
                    foreach($data_details as $detail) {
                       $temp = explode(".",$detail);
                       $database[] = $temp[0];
                       $tables[] = $temp[1];
                       $columns[] = $temp[2];
                    }
                    
                    // create sql
                    $sql = "SELECT ".$columns[0]." as id, ".$columns[1]." as name FROM ".$database[0].".".$tables[0];
                    
                    if(!empty($row->criteria)) {
                        $sql .=" WHERE $row->criteria";    
                    }
                    
                    // get records
                    if ($data_result = $mysqli->query($sql)) {
                        while($data_row = $data_result->fetch_object()) {
                            $workflow_form .= '<option id="'.$data_row->id.'" name="'.$data_row->id.'">'.$data_row->name.'</option>';
                        }
                        
                        $data_result->close();
                    }                    
                }
                
                $workflow_form .= '</select>';
            }
            
            // draw a data grid
            if($row->type=='grid') {
                
                $workflow_form .= '<h3>'.$row->name.'</h3>';
                $workflow_form .= '<table id="data_grid" name="data-grid">';
                               
                // get grid data
                if($row->data_type=='data') {
                    
                    $sql = $row->value;
                    $cols=0;
                    $status_cols = array();
                    $status_col_names = array();
                    $switch_status_cols = array();
                    $switch_status_col_names = array();
                    $switch_status_columns = array('approved', 'rejected');
                    $status_columns = array('status','visible');
                    
                    // get records
                    if ($data_result = $mysqli->query($sql)) {
                        
                        $data_table_cols = $data_result->fetch_fields();
                        $workflow_form .= '<tr>';
                        foreach ($data_table_cols as $table_col) {
                            $workflow_form .= "<td>$table_col->name</td>";
                            
                            // record that column is a status field
                            if(in_array($table_col->name,$switch_status_columns)) {
                                $switch_status_cols[] = $cols;
                                $switch_status_col_names[$cols] = $table_col->name;
                            }
                            
                            if(in_array($table_col->name, $status_columns)) {
                                $status_cols[] = $cols;
                                $status_col_names[$cols] = $table_col->name;
                            }
                            
                            $cols++;
                        }
                        $workflow_form .= '';
                        $workflow_form .= '</tr>';
                        
                        while($data_row = $data_result->fetch_array(MYSQLI_NUM)) {
                            
                            $workflow_form .= '<tr>';
                            for($index=0; $index<$cols; $index++) {
                                
                                // show status column check box
                                if(in_array($index,$switch_status_cols)) {
                                    $checked = $data_row[$index] == 1 ? 'checked' : '';
                                    $workflow_form .= '<td><input name="'.$data_row[0].'" id="'.$switch_status_col_names[$index].$data_row[0].'" value="'.$data_row[$index].'" type="radio" '.$checked.'></td>';
                                } else if(in_array($index,$status_cols)) {
                                    $checked = $data_row[$index] == 1 ? 'checked' : '';
                                    $workflow_form .= '<td><input name="'.$status_col_names[$index].'" id="'.$status_col_names[$index].$data_row[0].'" type="checkbox" value="'.$data_row[$index].'" '.$checked.'></td>';
                                } else {
                                    if($index==0) {
                                        // first column is unique id
                                        $workflow_form .= '<td>'.$data_row[$index].'<input type="hidden" name="id'.$data_row[$index].'" id="id'.$data_row[$index].'" value="'.$data_row[$index].'"></td>';
                                    } else {
                                        $workflow_form .= "<td>$data_row[$index]</td>";
                                    }
                                }
                            }
                            
                            $workflow_form .= '</tr>';
                        }
                        
                        $data_result->close();
                    }
                    
                    $workflow_form .= '</table>';
                }
            }
        }
        
        $result->close();
    } else{
        $mysqli->close();
        return 'An error has occured.';
    }
    
    
    $buttons ='<hr>';
    
    if($add_button==1) {
        $buttons .= '<input type="submit" class="submit" name="add" id="add" value="Add">';
    }

    if($update_button==1) {
        $buttons .= '<input type="submit" class="submit" name="update" id="update" value="Update">';
    }
    
    if($delete_button==1) {
        $buttons .= '<input type="submit" class="submit" name="delete" id="delete" value="Delete">';
    }
    
    if($cancel_button==1) {
        // show reset and cancel buttons
        $buttons .= '<input type="submit" class="submit" name="resetform" id="resetform" value="Reset">';
        $buttons .= '<input type="submit" class="submit" name="cancel" id="cancel" value="Cancel">';
    }
    
    if($send_email==1) {
        // TODO: is this needed ? (can we just use the hidden email as a mailto)
        // tell the engine that theere is a hidden maito on the form
    }
    
    // prepare form
    $workflow_action = '<legend>'.$action_name.'</legend>';
    $workflow_action .= '<form name="action" id="action">';
    $workflow_action .= '<input type="hidden" id="action_id" name="action_id" value="'.$action_id.'">';
    $workflow_action .= $workflow_form;
    $workflow_action .= $buttons;
    $workflow_action .= '</form>';
    
    $mysqli->close();
    
    return $workflow_action;
}




/*
 * Workflow Designer
 *
 */

function get_designer_workflows() {
    global $CFG;
    
    // get wokflows, steps and sub steps
    $mysqli =  new mysqli($CFG->db_host, $CFG->db_user, $CFG->db_pass, $CFG->db_name);
    
    $workflow ='';
    
    if (mysqli_connect_error()) {
        header('Location: login.php?error=4');
        exit;
    }
    
    $workflow .= '<form id="designer_workflow" name="designer_workflow" action="designer.php" method="post">';
    
    // get all active workflows
    $workflow_sql="select workflow_id as id, name as name, description as description, status as status from workflow";

    if($step_id==false) {
        $current_workflow_step_id='0';
    } else {
        $current_workflow_step_id=$step_id;
    }
    
    // get workflows
    if ($workflow_result = $mysqli->query($workflow_sql)) {
        if($workflow_result->num_rows==0) {
            return 'An error has occured.';
        } else {  
            $workflow .= '<select id="workflows" name="workflows">';
            $workflow .= '<option id="0">Select an existing workflow</option>';
            
            // construct data
            while ($workflow_row = $workflow_result->fetch_object()) {
                $workflow_id = $workflow_row->id;
                $workflow .= '<option id="'.$workflow_row->id.'">'.$workflow_row->name.'</option>';
            }
                
            $workflow .= '</select>';
        }
        
        $workflow_result->close();
    }
    
    $workflow .= '<h2>Or</h2>';
    
    $workflow .= 'Create a new workflow<br><label for="workflow_name">Name</label><input type="text" id="workflow_name" name="workflow_name">';
    $workflow .= '<br><label for="workflow_description">Description</label><textarea id="workflow_description" name="workflow_description"></textarea>';
    $workflow .= '<br><input type="submit" class="submit" name="continue" id="continue" value="continue">';
    
    $workflow .= '<input type="submit" class="submit" name="abort" id="abort" value="cancel">';
    $workflow .= '<input type="hidden" name="workflow_id" id="workflow_id">';
    $workflow .= '<input type="hidden" name="stage" id="stage" value="1">';
    
    $workflow .= '</form>';
    $workflow .= '</fieldset>';
        
    $mysqli->close();
    
    return $workflow;
}



function create_designer_workflow_step($workflow_name, $workflow_description) {
    
    $workflow_step = '';
    
    $workflow_step .= '<form id="designer_workflow" name="designer_workflow" action="designer.php" method="post">';
    
    $workflow_step .= 'Create a new workflow step<br><label for="workflow_step_name">Name</label><input type="text" id="workflow_step_name" name="workflow_step_name">';
    $workflow_step .= '<br><label for="workflow_step_description">Description</label><textarea id="workflow_step_description" name="workflow_step_description"></textarea>';
    $workflow_step .= '<br><input type="submit" class="submit" name="continue" id="continue" value="continue">';
        
    $workflow_step .= '<input type="submit" class="submit" name="abort" id="abort" value="cancel">';
    $workflow_step .= '<input type="hidden" name="workflow_name" id="workflow_name" value="'.$workflow_name.'">';
    $workflow_step .= '<input type="hidden" name="workflow_description" id="workflow_description" value="'.$workflow_description.'">';
    $workflow_step .= '<input type="hidden" name="stage" id="stage" value="2">';
    
    $workflow_step .= '</form>';
    $workflow_step .= '</fieldset>';
    
    return $workflow_step;
    
}


function create_designer_workflow_sub_step($workflow_name,$workflow_description,$workflow_step_name,$workflow_step_description) {
    
    $workflow_sub_step = '';
    
    $workflow_sub_step .= '<form id="designer_workflow" name="designer_workflow" action="designer.php" method="post">';
    
    $workflow_sub_step .= 'Optionally create a new workflow sub step (or continue)<br><label for="workflow_sub_step_name">Name</label><input type="text" id="workflow_sub_step_name" name="workflow_sub_step_name">';
    $workflow_sub_step .= '<br><label for="workflow_sub_step_description">Description</label><textarea id="workflow_sub_step_description" name="workflow_sub_step_description"></textarea>';
    $workflow_sub_step .= '<br><input type="submit" class="submit" name="continue" id="continue" value="continue">';
        
    $workflow_sub_step .= '<input type="submit" class="submit" name="abort" id="abort" value="cancel">';
    $workflow_sub_step .= '<input type="hidden" name="workflow_name" id="workflow_name" value="'.$workflow_name.'">';
    $workflow_sub_step .= '<input type="hidden" name="workflow_description" id="workflow_description" value="'.$workflow_description.'">';
    $workflow_sub_step .= '<input type="hidden" name="workflow_step_name" id="workflow_step_name" value="'.$workflow_step_name.'">';
    $workflow_sub_step .= '<input type="hidden" name="workflow_step_description" id="workflow_step_description" value="'.$workflow_step_description.'">';
    $workflow_sub_step .= '<input type="hidden" name="stage" id="stage" value="3">';
    
    $workflow_sub_step .= '</form>';
    $workflow_sub_step .= '</fieldset>';
    
    return $workflow_sub_step;
}


function create_designer_workflow_action($workflow_name,$workflow_description,$workflow_step_name,$workflow_step_description,$workflow_sub_step_name,$workflow_sub_step_description) {

    $workflow_form = '';
    
    $workflow_form .= '<form id="designer_workflow" name="designer_workflow" action="designer.php" method="post">';
    $workflow_form .= 'Select the action to be performed<br>';
    
    $workflow_form .= '<label for="workflow_action">Action</label>';
    $workflow_form .= '<select class="required" id="workflow_action" name="workflow_action">';
    $workflow_form .= get_list(2,'');
    $workflow_form .= '</select>';
    
    $workflow_form .= '<label for="workflow_form_elements">Number of form elements</label>';
    $workflow_form .= '<input type="text" class="required number" id="workflow_form_elements" valiedate="" name="workflow_form_elements">';
    
    $workflow_form .= '<br><input type="submit" class="submit" name="continue" id="continue" value="continue">';
        
    $workflow_form .= '<input type="submit" class="submit" name="abort" id="abort" value="cancel">';
    $workflow_form .= '<input type="hidden" name="workflow_name" id="workflow_name" value="'.$workflow_name.'">';
    $workflow_form .= '<input type="hidden" name="workflow_description" id="workflow_description" value="'.$workflow_description.'">';
    $workflow_form .= '<input type="hidden" name="workflow_step_name" id="workflow_step_name" value="'.$workflow_step_name.'">';
    $workflow_form .= '<input type="hidden" name="workflow_step_description" id="workflow_step_description" value="'.$workflow_step_description.'">';
    $workflow_form .= '<input type="hidden" name="workflow_sub_step_name" id="workflow_sub_step_name" value="'.$workflow_sub_step_name.'">';
    $workflow_form .= '<input type="hidden" name="workflow_sub_step_description" id="workflow_sub_step_description" value="'.$workflow_sub_step_description.'">';
    $workflow_form .= '<input type="hidden" name="stage" id="stage" value="4">';
    
    $workflow_form .= '</form>';
    $workflow_form .= '</fieldset>';
    
    $workflow_form .= '<div id="helpbox">';
    $workflow_form .= '<h2>Help</h2>';
    $workflow_form .= '<div id="helptext"></div>';
    $workflow_form .= '</div>';
    
    return $workflow_form;
}


function create_designer_workflow_form($workflow_name,$workflow_description,$workflow_step_name,$workflow_step_description,$workflow_sub_step_name,$workflow_sub_step_description, $workflow_action, $workflow_form_elements) {
    
    $workflow_form = '';
    
    $workflow_form .= '<form id="designer_workflow" name="designer_workflow" action="designer.php" method="post">';
    $workflow_form .= 'Create your form elements<br>';
    
    for($index=0; $index<$workflow_form_elements; $index++) {
        $workflow_form  .='<div class="form_element">[Element: '.($index+1).']<br>';
        $workflow_form  .='<div class="preview" id="preview'.$index.'">';
        $workflow_form  .='</div>';
        $workflow_form .= '<label for="field_type'.$index.'">Field type</label>';
        $workflow_form .= '<select id="field_type'.$index.'" name="field_type[]">';
        $workflow_form .= get_workflow_data_types();
        $workflow_form .= '</select>';
        
        $workflow_form .= '<label for="field_label'.$index.'">Field Label</label>';
        $workflow_form .= '<input help="Text label for field type element" type="text" id="field_label'.$index.'" name="field_label[]">';
        
        $workflow_form .= '<label for="field_name'.$index.'">Field name</label>';
        $workflow_form .= '<input help="Field identifier, must be unique" type="text" id="field_name'.$index.'" name="field_name[]">';
        
        $workflow_form .= '<label for="field_description'.$index.'">Field description</label>';
        $workflow_form .= '<textarea help="Describe your field element" id="field_description'.$index.'" name="field_description[]"></textarea>';
        
        $workflow_form .= '<label for="field_mandatory'.$index.'">Is field mandatory?</label>';
        $workflow_form .= '<select help="Is this a required field?" id="field_mandatory'.$index.'" name="field_mandatory[]">';
        $workflow_form .= get_list(3,'');
        $workflow_form .= '</select>';
        
        $workflow_form .= '<label for="field_validation'.$index.'">Validation</label>';
        $workflow_form .= '<select id="field_validation'.$index.'" name="field_validation[]">';
        $workflow_form .= get_list(4,'None');
        $workflow_form .= '</select>';
        
        $workflow_form .= '</div>';
        
        $workflow_form .= '<hr>';
    }
    
    $workflow_form .= '<br><input type="submit" class="submit" name="continue" id="continue" value="continue">';
        
    $workflow_form .= '<input type="submit" class="submit" name="abort" id="abort" value="cancel">';
    $workflow_form .= '<input type="hidden" name="workflow_name" id="workflow_name" value="'.$workflow_name.'">';
    $workflow_form .= '<input type="hidden" name="workflow_description" id="workflow_description" value="'.$workflow_description.'">';
    $workflow_form .= '<input type="hidden" name="workflow_step_name" id="workflow_step_name" value="'.$workflow_step_name.'">';
    $workflow_form .= '<input type="hidden" name="workflow_step_description" id="workflow_step_description" value="'.$workflow_step_description.'">';
    $workflow_form .= '<input type="hidden" name="workflow_sub_step_name" id="workflow_sub_step_name" value="'.$workflow_sub_step_name.'">';
    $workflow_form .= '<input type="hidden" name="workflow_sub_step_description" id="workflow_sub_step_description" value="'.$workflow_sub_step_description.'">';
    $workflow_form .= '<input type="hidden" name="workflow_action" id="workflow_action" value="'.$workflow_action.'">';
    $workflow_form .= '<input type="hidden" name="workflow_form_elements" id="workflow_form_elements" value="'.$workflow_form_elements.'">';
    $workflow_form .= '<input type="hidden" name="stage" id="stage" value="5">';
    
    $workflow_form .= '</form>';
    $workflow_form .= '</fieldset>';
    
    $workflow_form .= '<div id="helpbox">';
    $workflow_form .= '<h2>Help</h2>';
    $workflow_form .= '<div id="helptext"></div>';
    $workflow_form .= '</div>';
    
    return $workflow_form;
}


function get_list($list_data_id, $default_value) {
    global $CFG;
    
    // get list data
    $mysqli =  new mysqli($CFG->db_host, $CFG->db_user, $CFG->db_pass, $CFG->db_name);
    
    $list_data ='';
    
    if (mysqli_connect_error()) {
        header('Location: login.php?error=4');
        exit;
    }
    
    $sql = "select list_data_id, item_id, name, description from list_data where item_id!=0 and list_data_id=$list_data_id and status=1";
    
    // get list data
    if ($result = $mysqli->query($sql)) {
        if($result->num_rows==0) {
            return $list_data;
        } else {  
            $list_data .='<option id="0">'.$default_value.'</option>';
            
            // construct data
            while ($row = $result->fetch_object()) {
                $list_data .='<option help="'.$row->description.'" id="'.$row->item_id.'">'.$row->name.'</option>';
            }
        }
        
        /* free result set */
        $result->close();
    }
    
    $mysqli->close();
    
    return $list_data;    
}



function get_workflow_data_types() {
    global $CFG;
    
    // get workflow data types
    $mysqli =  new mysqli($CFG->db_host, $CFG->db_user, $CFG->db_pass, $CFG->db_name);
    
    $workflow_data_types ='';
    
    if (mysqli_connect_error()) {
        header('Location: login.php?error=4');
        exit;
    }
    
    $sql = "select workflow_data_type_id as id, name, data_type, concat(name,' (', data_type, ')') as fullname from workflow_data_type where status=1";
    
    // get workflow data types data
    if ($result = $mysqli->query($sql)) {
        if($result->num_rows==0) {
            return $workflow_data_types;
        } else {  
            $workflow_data_types .='<option id="0"></option>';
            
            // construct data
            while ($row = $result->fetch_object()) {
                $help1 = get_help(7, $row->name);
                $help2 = get_help(8, $row->data_type);
                $workflow_data_types .='<option data="'.$row->name.'" help="'.$row->name. '<br> ' .$help1 .'<br><br>' .$row->data_type . '<br>'.$help2.'" id="'.$row->id.'">'.$row->fullname.'</option>';
            }
        }
        
        /* free result set */
        $result->close();
    }
    
    $mysqli->close();
    
    return $workflow_data_types;    
}


function get_help($list_data_id, $name) {
    global $CFG;
    
    // get description for list data item
    $mysqli =  new mysqli($CFG->db_host, $CFG->db_user, $CFG->db_pass, $CFG->db_name);
    
    $list_data_description ='';
    
    if (mysqli_connect_error()) {
        header('Location: login.php?error=4');
        exit;
    }
    
    $sql = "select description from list_data where name='$name' and list_data_id=$list_data_id and item_id!=0";
    
    // get workflow data types data
    if ($result = $mysqli->query($sql)) {
        if($result->num_rows==0) {
            return $list_data_description;
        } else {  
            // construct data
            while ($row = $result->fetch_object()) {
                $list_data_description = $row->description;
            }
        }
        
        /* free result set */
        $result->close();
    }
    
    $mysqli->close();
    
    return $list_data_description;    
}