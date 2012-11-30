<?php

session_start();

require_once('lib.php');

$action = '';
$record_data = '';
//$record_id = '';
$search_text='';
$action_desc='';

if(!empty($_GET['action'])) {
    $action = $_GET['action'];
}

if(!empty($_GET['action_desc'])) {
    $action_desc = $_GET['action_desc'];
}

if(!empty($_GET['record_data'])) {
    $record_data = $_GET['record_data'];
}

/*if(!empty($_POST['record_id'])) {
    $action = $_POST['record_id'];
}*/

if(!empty($_POST['search_text'])) {
    $action = $_POST['search_text'];
}


if(is_logged_in()){

    switch($action) {
        case 'add' :    process_record($record_data, $action_desc);
                        break;
                    
        case 'update' : process_record($record_data, $action_desc);
                        break;
                    
        case 'delete' : process_record($record_data, $action_desc);
                        break;
                    
        case 'search' : search($record_data, $search_text);
                        break;
                    
        case 'courserequest' : do_course_request();
                        break;
                    
        case 'logout' : do_logout();
                        break;
                    
        default :       echo show_header();
                        echo show_home();
                        echo show_footer();
                        break;
    }    
} else {
    header('Location: login.php');
    exit;
}


