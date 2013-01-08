<?php

session_start();

require_once('lib.php');

$programme='';
$course_year='';
$course='';
$unit='';

if(!empty($_GET['P'])) {
    $programme = $_GET['P'];
}

if(!empty($_GET['Y'])) {
    $course_year = $_GET['Y'];
}

if(!empty($_GET['C'])) {
    $course = $_GET['C'];
}

if(!empty($_GET['U'])) {
    $unit = $_GET['U'];
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