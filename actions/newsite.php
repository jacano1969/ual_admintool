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
    
    
    // create form
    $newsite = ''; 
    $newsite .= '<fieldset>';
    $newsite .= '<legend>';
    $newsite .= 'Create new site';
    $newsite .= '</legend>';
    $newsite .= '<form id="newsite" name="newsite">';
    
    $newsite .= '</form>';
    $newsite .= '</fieldset>';