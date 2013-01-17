<?php

    session_start();
   
    require_once('lib.php');

    $header = '';
    $header .= '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
    $header .= '<html lang="en" dir="ltr">';
    $header .= '<head>';
    $header .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
    $header .= '<title>UAL Admn Tool</title>';
    
    // addded ro gdata table
    $header .= '<link rel="stylesheet" href="css/960gs/fluid.css">';
    $header .= '<link rel="stylesheet" href="css/h5bp/normalize.css">';
    $header .= '<link rel="stylesheet" href="css/h5bp/non-semantic.helper.classes.css">';
    $header .= '<link rel="stylesheet" href="css/h5bp/print.styles.css">';
    
    $header .= '<link rel="stylesheet" href="css/sprites.css">';
    $header .= '<link rel="stylesheet" href="css/header.css">';
    $header .= '<link rel="stylesheet" href="css/navigation.css">';   
    $header .= '<link rel="stylesheet" href="css/content.css">';
    $header .= '<link rel="stylesheet" href="css/footer.css">';
    $header .= '<link rel="stylesheet" href="css/typographics.css">';
    $header .= '<link rel="stylesheet" href="css/ie.fixes.css">';
    
    
    $header .= '<link rel="stylesheet" href="css/sprite.forms.css">';
    $header .= '<link rel="stylesheet" href="css/sprite.tables.css">';
    
    $header .= '<link rel="stylesheet" href="css/sprite.lists.css">';
    $header .= '<link rel="stylesheet" href="css/icons.css">';
    $header .= '<link rel="stylesheet" href="css/external/jquery-ui-1.8.16.custom.css">';
                
    $header .= '<script src="script/libs/modernizr-2.0.6.min.js"></script>';
    //
    
    
    
    $header .= '<link href="css/style.css" type="text/css" rel="stylesheet">';
    $header .= '<script src="script/jquery-1.8.1.min.js" type="text/javascript"></script>';
    $header .= '<script src="script/jquery.lightbox_me.js" type="text/javascript"></script>';
    $header .= '<script src="script/jquery.validate.min.js" type="text/javascript"></script>';
    $header .= '<script type="text/javascript" src="script/jquery.tablesorter.js"></script>';
    $header .= '<script src="script/ual_admintool.js" type="text/javascript"></script>';
    $header .= '</head>';
    
    
    
    
    echo $header;
    
    
    