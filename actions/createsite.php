<?php

    session_start();

    include_once('../dbconfig.php');
    include_once('../lib.php');
    
    $site_data = '';
    
    // new course values
    $id = '';
    $requesting_user ='';
    $courseid = '';
    $aos_code= '';
    $aos_period = '';
    $acad_period = '';
    $college = '';
    $aos_description ='';
    $full_description = '';
    $school = '';
    $aos_type = '';
    
    if(!empty($_GET['site_data'])) {
        $site_data = $_GET['site_data'];
    }
    
    // extract json data
    $new_site_data = json_decode($site_data,true);
    
    // process new site data
    $id = $new_site_data['id'];
    $requesting_user = $new_site_data['requesting_user'];
    $courseid = $new_site_data['courseid'];
    $aos_code = $new_site_data['aos_code'];
    $aos_period = $new_site_data['aos_period'];
    $acad_period = $new_site_data['acad_period'];
    $college = $new_site_data['college'];
    $aos_description = $new_site_data['aos_description'];
    $full_description = $new_site_data['full_description'];
    $school = $new_site_data['school'];
    $aos_type = $new_site_data['aos_type'];
    
    // update new course
    $sql="UPDATE new_courses SET COURSEID='$courseid '," .
         "AOS_CODE='$aos_code',AOS_PERIOD='$aos_period'," .
         "ACAD_PERIOD='$acad_period',COLLEGE='$college'," .
         "AOS_DESCRIPTION='$aos_description'," .
         "FULL_DESCRIPTION='$full_description'," .
         "SCHOOL='$school',AOS_TYPE='$aos_type' " .
         "WHERE id=$id";
         
    global $CFG;
    
    // connect to db
    $mysqli = new mysqli($CFG->db_host, $CFG->db_user, $CFG->db_pass, $CFG->db_name);
    
    if (mysqli_connect_error()) {
        return false;
    }

    //$sql_update = $mysqli->real_escape_string($sql);  // preserve line breaks
    
    $mysqli->set_charset("utf8");
    
    if($result = $mysqli->query($sql)) {
        
        // enrol user on course as course leader
        $sql="INSERT INTO staff_enrolments (STAFFID,COURSEID,role) values ('$requesting_user','$courseid',2)";
        
        if (mysqli_connect_error()) {
            return false;
        }
        
        //$sql_insert = $mysqli->real_escape_string($sql);  // preserve line breaks
    
        $mysqli->set_charset("utf8");
        
        if($result = $mysqli->query($sql)) {
            $mysqli->close();
            
            echo "New site created and requesting user enrolled as course leader.";
        } else {
            echo "An error occured.";
            $mysqli->close();
        }
    } else {
        echo "An error occured.";
        $mysqli->close();
    }
    
    
    