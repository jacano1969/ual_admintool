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
        echo '<div id="results_container">';
        echo '</div>';
        echo '<div id="filter_container" class="container">';
        echo get_filter_data($type, $data);
        echo show_footer();
    } else {
        echo show_header();
        echo '<body id="home-page">';
        echo '<div class="container">';
        echo get_filter_data(false, false);
        //echo '<div id="hiddenresultslightbox">';
        //echo '</div>';
        echo show_footer();
    }
} else {
    header('Location: login.php');
    exit;
}

