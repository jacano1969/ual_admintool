<?php

    session_start();

    // get requesting user
    $requesting_user='';
    if(!empty($_GET['requesting_user'])) {
        $requesting_user=$_GET['requesting_user'];
    } else {
        echo "requesting_user false";
    }
   
    // get course id for new course
    $new_course_id=0;
    if(!empty($_GET['new_course_id'])) {
        $new_course_id=$_GET['new_course_id'];
    } else {
        echo "new_course_id false";
    }
    
    include_once('../dbconfig.php');
    include_once('../lib.php');
    
    // get new course information
    $sql="SELECT id,COURSEID,AOS_CODE,AOS_PERIOD,ACAD_PERIOD,COLLEGE,AOS_DESCRIPTION,FULL_DESCRIPTION,SCHOOL,AOS_TYPE FROM new_courses";
    
    // new course values
    $courseid = '';
    $aos_code= '';
    $aos_period = '';
    $acad_period = '';
    $college = '';
    $aos_description ='';
    $full_description = '';
    $school = '';
    $aos_type = '';
        
    $mysqli =  new mysqli($CFG->db_host, $CFG->db_user, $CFG->db_pass, $CFG->db_name);
    
    if (mysqli_connect_error()) {
        header('Location: login.php?error=4');
        exit;
    }
    
    if($result = $mysqli->query($sql)) {
        while($row = $result->fetch_object()) {
            $courseid = $row->COURSEID;
            $aos_code = $row->AOS_CODE;
            $aos_period = $row->AOS_PERIOD;
            $acad_period = $row->ACAD_PERIOD;
            $college = $row->COLLEGE;
            $aos_description = $row->AOS_DESCRIPTION;
            $full_description = $row->FULL_DESCRIPTION;
            $school = $row->SCHOOL;
            $aos_type = $row->AOS_TYPE;
        }
    }
    
    // create form
    $newsite = ''; 
    $newsite .= '<fieldset>';
    $newsite .= '<legend>';
    $newsite .= 'Create new site';
    $newsite .= '</legend>';
    $newsite .= '<form id="newsite" name="newsite">';
    
    $newsite .= '<input type="hidden" id="id" name="id" value="'.$new_course_id.'">';
    $newsite .= '<input type="hidden" id="requesting_user" name="requesting_user" value="'.$requesting_user.'">';
    
    $newsite .= '<label>Course Id:</label><em>*</em><input class="required" maxlength="32" type="text" id="courseid" name="courseid" value="'.$courseid.'">';
    $newsite .= '<label>AOS Code:</label><em>*</em><input class="required" maxlength="11" type="text" id="aos_code" name="aos_code" value="'.$aos_code.'">';
    $newsite .= '<label>AOS Period:</label><em>*</em><input class="required" type="text" maxlength="5" id="aos_period" name="aos_period" value="'.$aos_period.'">';
    $newsite .= '<label>ACAD Period:</label><em>*</em><input class="required" type="text" maxlength="5" id="acad_period" name="acad_period" value="'.$acad_period.'">';
    $newsite .= '<label>College:</label><em>*</em><input class="required" type="text" id="college" name="college" value="'.$college.'">';
    $newsite .= '<label>AOS Description:</label><em>*</em><input class="required" type="text" id="aos_description" name="aos_description" value="'.$aos_description.'">';
    $newsite .= '<label>Full Description:</label><em>*</em><input class="required" type="text" id="full_description" name="full_description" value="'.$full_description.'">';
    $newsite .= '<label>School:</label><em>*</em><input class="required" type="text" id="school" name="school" value="'.$school.'">';
    $newsite .= '<label>AOS Type:</label><em>*</em><input class="required" maxlength="1" type="text" id="aos_type" name="aos_type" value="'.$aos_type.'">';
    
    $newsite .= '<hr>';
    $newsite .= '<input id="addsite" class="submit" type="submit" value="Add" name="add">';
    //$newsite .= '<input id="cancel" class="submit" type="submit" value="Back" name="cancel">';

    $newsite .= '</form>';
    $newsite .= '</fieldset>';
    
    echo $newsite;
    
    
    