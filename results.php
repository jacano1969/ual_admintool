<?php

session_start();

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
    
    $content .='<input type="button" class="close" value="close">';
    
    $content .='<table>';
    
    $content .='<tr><td>Programme</td><td>Course Year</td><td>Course</td><td>Unit</td></tr>';
    $content .='<tr><td>'.$programme.'</td><td>'.$course_year.'</td><td>'.$course.'</td><td>'.$unit.'</td></tr>';
    
    
    $content .='</table>';
    
    echo $content;
    
} else {
    header('Location: login.php');
    exit;
}