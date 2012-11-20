<?php

session_start();

require_once('lib.php');

$action = '';
$record_data = '';
//$record_id = '';
$search_text='';

if(!empty($_GET['action'])) {
    $action = $_GET['action'];
}

if(!empty($_GET['record_data'])) {
    $action = $_GET['record_data'];
}

/*if(!empty($_POST['record_id'])) {
    $action = $_POST['record_id'];
}*/

if(!empty($_POST['search_text'])) {
    $action = $_POST['search_text'];
}


if(is_logged_in()){

    switch($action) {
        case 'add' :    add($record_data);
                        break;
                    
        case 'update' : update($record_data);
                        break;
                    
        case 'delete' : delete($record_data);
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


