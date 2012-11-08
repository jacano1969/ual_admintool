<?php

session_set_cookie_params(0, '/', 'w01.ual01test.wf.ulcc.ac.uk');
session_start();

require_once('lib.php');

$action = '';
$record_type = '';
$record_id = '';
$search_text='';

if(!empty($_POST['action'])) {
    $action = $_POST['action'];
}

if(!empty($_POST['record_type'])) {
    $action = $_POST['record_type'];
}

if(!empty($_POST['record_id'])) {
    $action = $_POST['record_id'];
}

if(!empty($_POST['search_text'])) {
    $action = $_POST['search_text'];
}


if(is_logged_in()){

    // TODO:
    // show logged in header
    echo show_header();

    switch($action) {
        case 'add' :    add($record_type);
                        break;
                    
        case 'update' : update($record_type, $record_id);
                        break;
                    
        case 'delete' : delete($record_type, $record_id);
                        break;
                    
        case 'search' : search($record_type, $search_text);
                        break;
                    
        case 'logout' : do_logout();
                        break;
                    
        default :       echo show_home();
                        break;
    }
    
    // show footer
    echo show_footer();
} else {
    header('Location: login.php');
    exit;
}


