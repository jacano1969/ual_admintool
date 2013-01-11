<?php

session_start();

require_once('lib.php');

$result_type='';
$programme='';
$course_year='';
$course='';
$unit='';
$pagenum=1;

if(!empty($_GET['pagenum'])) {
    $pagenum = $_GET['pagenum'];
    
    if($pagenum=="undefined") {
        $pagenum=1;
    }
}

if(!empty($_GET['T'])) {
    $result_type = $_GET['T'];
    
    if($result_type=="undefined") {
        $result_type='';
    }
}

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
    $sql='';
    
    if (mysqli_connect_error()) {
        header('Location: login.php?error=4');
        exit;
    }
    
    $loggedin_username = $_SESSION['USERNAME'];
    
    
    // user enrolments / default
    if($result_type=='ue' || $result_type=='') {
        // users enrolments
        $sql = "SELECT DISTINCT " .
               "CASE WHEN c.aos_code like('L%') THEN 'Programme' ELSE " . 
               "CASE WHEN c.aos_code REGEXP '^[0-9]' THEN 'Course' ELSE " .
               "CASE WHEN c.aos_code REGEXP '^[A-Z]' THEN 'Unit' " .
               "END END END as 'Type', " .
               "e.record_id, e.enrolmentid, e.staffid, e.stageid, " .
               "c.courseid, c.aos_code, c.aos_period, c.acad_period, c.college, c.aos_description," .
               "c.full_description, c.school,c.aos_type " .
               "from STAFF_ENROLMENTS e " .
               "inner join COURSES c on c.courseid = e.courseid ";
        
        if($unit!=''){
            $sql .=" and c.aos_code='$unit'";
        }else if($course!=''){
            $sql .=" and c.aos_code='$course' ";
        }else if($programme!=''){
            $sql .=" and c.aos_code='$programme' ";
        }
        
        if($course_year!='') {
            $sql .=" and c.acad_period='$course_year' ";
        }
        
        $limit = $pagenum * 20;
        $sql .="inner join COURSE_STRUCTURE cs on cs.aos_code = c.aos_code " .
                          "and e.staffid = '$loggedin_username' LIMIT $pagenum, $limit";
                          
    } else {
        // course user is NOT enrolled on
        /*$sql ="SELECT c.courseid, c.aos_code, c.aos_period, c.acad_period, " .
              "cs.aoscd_link, cs.lnk_aos_period, cs.lnk_period, cs.compulsry_yn," .
              "c.college, c.aos_description, c.full_description, c.school, c.aos_type " .
              "FROM COURSE_STRUCTURE cs " .
              "INNER JOIN COURSES c " .
              "ON c.aos_code LIKE CONCAT('%', cs.AOS_CODE ,'%') ";*/
        
        $sql ="SELECT c.courseid, c.aos_code, c.aos_period, c.acad_period, " .
              "c.college, c.aos_description, c.full_description, c.school, c.aos_type " .
              "FROM COURSES c ";
              
        /*if($unit!=''){
            $sql .=" and c.aos_code='$unit'";
        }else if($course!=''){
            $sql .=" and c.aos_code='$course' ";
        }else if($programme!=''){
            $sql .=" and c.aos_code='$programme' ";
        }
        
        if($course_year!='') {
            $sql .=" and c.acad_period='$course_year' ";
        }*/
        
        $limit = $pagenum * 20;
        /*$sql .="AND c.courseid NOT IN (SELECT e.courseid FROM STAFF_ENROLMENTS e " .
               "WHERE e.staffid = '$loggedin_username') LIMIT $pagenum, $limit";*/
        $sql .="WHERE c.courseid NOT IN (SELECT e.courseid FROM STAFF_ENROLMENTS e " .
               "WHERE e.staffid = '$loggedin_username') LIMIT $pagenum, $limit";
    }
                          
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
    
    // testing
    //$content .= $enrolments_sql;
    
    $content .='<table class="results">';
    
    if ($result = $mysqli->query($sql)) {
        if($result->num_rows==0) {
            $content .= '<tr><th>No Enrolment Data</td></th></tr>';
            
            $result->close();
        } else {
            $content .='<tr>';
            
            // show users enrolments
            if($result_type=='ue' || $result_type=='') {
                $content .='<th>Remove</th><th>Type</th><th>Record Id</th><th>Enrolment Id</th><th>Staff Id</th><th>Stage Id</th>';
                $content .='<th>Course Id</th><th>AOS Code</th><th>AOS Period</th><th>ACAD Period</th>';
                $content .='<th>College</th><th>AOS Description</th><th>Full Description</th><th>School</th><th>AOS Type</th>';
                
                $content .='</tr>';
                
                while ($row = $result->fetch_object()) {
                    $content .="<tr>";
                    $content .='<td><input type="radio" value="0" id="remove_'.$row->enrolmentid.'" name="remove_'.$row->enrolmentid.'"></td>';
                    $content .="<td>$row->Type</td>";
                    $content .="<td>$row->record_id</td>";
                    $content .="<td>$row->enrolmentid</td>";
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
            } else {
                $content .='<th>Add</th><th>Course Id</th><th>AOS Code</th><th>Aos Period</th><th>Acad Period</th>';
                //$content .='<th>Aos CD Link</th><th>Link AOS Period</th><th>Link Period</th><th>Compulsory</th>';
                $content .='<th>College</th><th>AOS Description</th><th>Full Description</th><th>School</th><th>AOS Type</th>';
                
                $content .='</tr>';
                
                while ($row = $result->fetch_object()) {
                    $content .="<tr>";
                    $content .='<td><input type="radio" value="0" id="add_'.$row->enrolmentid.'" name="add_'.$row->enrolmentid.'"></td>';
                    $content .="<td>$row->courseid</td>";
                    $content .="<td>$row->aos_code</td>";
                    $content .="<td>$row->aos_period</td>";
                    $content .="<td>$row->acad_period</td>";
                    //$content .="<td>$row->aoscd_link</td>";
                    //$content .="<td>$row->lnk_aos_period</td>";
                    //$content .="<td>$row->lnk_period</td>";
                    //$content .="<td>$row->compulsry_yn</td>";
                    $content .="<td>$row->college</td>";
                    $content .="<td>$row->aos_description</td>";
                    $content .="<td>$row->full_description</td>";
                    $content .="<td>$row->school</td>";
                    $content .="<td>$row->aos_type</td>";
                    $content .="</tr>";
                }
            }
            
            $result->close();
        }
    }   
    
    $content .='</table>';
    
    $mysqli->close();
    
    echo $content;
    
} else {
    header('Location: login.php');
    exit;
}