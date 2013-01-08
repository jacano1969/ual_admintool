<?php

session_start();

require_once('lib.php');

if(is_logged_in()){

    // get results based on filters
    echo '<input type="button" class="close" value="close">';
    
} else {
    header('Location: login.php');
    exit;
}