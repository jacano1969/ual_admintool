<?php

session_start();

require_once('lib.php');

if(is_logged_in()){
    
    $programme='';
    $course_year='';
    $course='';
    $unit='';
    
    if(!empty($_POST['P'])) {
        $programme = $_POST['P'];
    }
    
    if(!empty($_POST['Y'])) {
        $course_year = $_POST['Y'];
    }
    
    if(!empty($_POST['C'])) {
        $course = $_POST['C'];
    }
    
    if(!empty($_POST['U'])) {
        $unit = $_POST['U'];
    }
    
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