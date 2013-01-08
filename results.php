<?php

session_start();

require_once('dbconfig.php');
require_once('lib.php');

$programme='';
$course_year='';
$course='';
$unit='';

if(!empty($_GET['P'])) {
    $programme = $_GET['P'];
    
    if($programme=="undefined") {
        $programme='';
    }
}

if(!empty($_GET['Y'])) {
    $course_year = $_GET['Y'];
    
    if($course_year=="undefined") {
        $course_year='';
    }
}

if(!empty($_GET['C'])) {
    $course = $_GET['C'];
    
    if($course=="undefined") {
        $course='';
    }
}

if(!empty($_GET['U'])) {
    $unit = $_GET['U'];
    
    if($unit=="undefined") {
        $unit='';
    }
}

if(is_logged_in()){
        
    // get results based on filters
    $content ='';
    
    global $CFG;
    
    // get all enrolments for the currently logged in user
    // based on filter
    $mysqli =  new mysqli($CFG->db_host, $CFG->db_user, $CFG->db_pass, $CFG->db_name);
    
    $results ='';
    
    if (mysqli_connect_error()) {
        header('Location: login.php?error=4');
        exit;
    }
    $loggedin_username = $_SESSION['USERNAME'];
    
    $enrolments_sql = "SELECT " .
                      "CASE WHEN c.aos_code like('L%') THEN 'Programme' ELSE " . 
                      "CASE WHEN c.aos_code REGEXP '^[0-9]' THEN 'Course' ELSE " .
                      "CASE WHEN c.aos_code REGEXP '^[A-Z]' THEN 'Unit' " .
                      "END END END as 'Type', " .
                      "e.record_id, e.enrolmentid, e.staffid, e.stageid, " .
                      "c.courseid, c.aos_code, c.aos_period, c.acad_period, c.college, c.aos_description," .
                      "c.full_description, c.school,c.aos_type " .
                      "from STAFF_ENROLMENTS e " . 
                      "inner join COURSES c on c.courseid = e.courseid " .
                      "inner join COURSE_STRUCTURE cs on cs.aos_code = c.aos_code " .
                      "and e.staffid = '$loggedin_username'";
                      
    /*if($programme!=0){
        $enrolments_sql .= "inner join ";
        select distinct c.aos_code as id,
                        c.full_description as name,
                        c.acad_period as year
                        from COURSES c
                        inner join STAFF_ENROLMENTS e on e.staffid='$loggedin_username'
                        and e.courseid=concat(c.aos_code, c.aos_period, c.acad_period)
                        and c.aos_code like('L%') order by name
    }*/
    
    
    $content .='<input type="button" class="close" value="close">';
    
    $content .='<table>';
    
    if ($result = $mysqli->query($enrolments_sql)) {
        if($result->num_rows==0) {
            $content .= '<tr><td>No Data</td></tr>';
        }
    } else {
        $content .='<tr>';
        
        $content .='<td>Type</td><td>Record Id</td><td>Enrolment Id</td><td>Staff Id</td><td>Stage Id</td>';
        $content .='<td>Course Id</td><td>AOS Code</td><td>AOS Period</td><td>ACAD Period</td>';
        $content .='<td>College</td><td>ASO Description</td><td>Full Description</td><td>School</td><td>AOS Type</td>';
        
        $content .='</tr>';
        while ($row = $result->fetch_object()) {
            $content .="<tr>";
            $content .="<td>$row->Type</td>";
            $content .="<td>$row->record_id</td>";
            $content .="<td>$row->staffid</td>";
            $content .="<td>$row->stageid</td>";
            $content .="<td>$row->courseid</td>";
            $content .="<td>$row->aos_code</td>";
            $content .="<td>$row->aos_period</td>";
            $content .="<td>$row->acad_period</td>";
            $content .="<td>$row->college</td>";
            $content .="<td>$row->aos_description</td>";
            $content .="<td>$row->full_description</td>";
            $content .="<td>$row->school</td>";
            $content .="<td>$row->aos_type</td>";
            $content .="</tr>";
        }
        
        $result->close();
    }   
    
    $content .='</table>';
    
    $mysqli->close();
    
    echo $content;
    
} else {
    header('Location: login.php');
    exit;
}