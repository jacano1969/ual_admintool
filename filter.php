<?php

session_start();

require_once('lib.php');

if(is_logged_in()){
    // get params
    if(!empty($_GET['type'])){
    
        $type = false;
        $data = false;
        
        if(!empty($_GET['data'])){
            $type = $_GET['type'];
            $data = $_GET['data'];
        }       
        echo show_header();
        echo '<body id="home-page">';
        echo get_filter_data($type, $data);
        echo show_footer();
    } else {
        echo show_header();
        echo '<body id="home-page">';
        echo get_filter_data(false, $data);
        echo show_footer();
    }
} else {
    header('Location: login.php');
    exit;
}