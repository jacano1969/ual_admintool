<?php

    session_start();
   
    require_once('lib.php');

    $header = '';
    $header .= '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
    $header .= '<html lang="en" dir="ltr">';
    $header .= '<head>';
    $header .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
    $header .= '<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">';
    $header .= '<title>UAL Admn Tool</title>';
    
    // added for data table
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
    $header .= '<link href="css/style.css" type="text/css" rel="stylesheet">';
    $header .= '</head>';
    
    $result_type='';
    
    if(!empty($_GET['T'])) {
        $result_type = $_GET['T'];
        
        if($result_type=="undefined") {
            $result_type='';
        }
    }
    
    if($result_type=='ue' || $result_type=='') {
        $header .= '<body id="user-enrolments">';
    } else {
        $header .= '<body id="possible-enrolments">';
    }
    
    $header .= '<div class="container">';
    
    if(is_logged_in()){
            
        // get results based on filters
        $content ='';
        
        global $CFG;
        
        // get all enrollments for the currently logged in user
        // based on filter
        $mysqli =  new mysqli($CFG->db_host, $CFG->db_user, $CFG->db_pass, $CFG->db_name);
        
        $results ='';
        $sql='';
        
        if (mysqli_connect_error()) {
            header('Location: login.php?error=4');
            exit;
        }
        
        $loggedin_username = $_SESSION['USERNAME'];
        
        
        // user enrollments / default
        if($result_type=='ue' || $result_type=='') {
            // users enrollments
            $sql = "SELECT DISTINCT " .
                   "CASE WHEN c.aos_code like('L%') THEN '(Programme)' ELSE " . 
                   "CASE WHEN c.aos_code REGEXP '^[0-9]' THEN '(Course)' ELSE " .
                   "CASE WHEN c.aos_code REGEXP '^[A-Z]' THEN '(Unit)' " .
                   "END END END as 'Type', " .
                   "e.record_id, e.enrolmentid, e.staffid, e.stageid, " .
                   "c.courseid, c.aos_code, c.aos_period, c.acad_period, c.college, c.aos_description," .
                   "c.full_description, c.school,c.aos_type " .
                   "from STAFF_ENROLMENTS e " .
                   "inner join COURSES c on c.courseid = e.courseid " .
                   "where e.staffid = '$loggedin_username'";
                   
        } else {
            // course user is NOT enrolled on            
            $sql = "SELECT DISTINCT " .
                   "CASE WHEN c.aos_code like('L%') THEN '(Programme)' ELSE " . 
                   "CASE WHEN c.aos_code REGEXP '^[0-9]' THEN '(Course)' ELSE " .
                   "CASE WHEN c.aos_code REGEXP '^[A-Z]' THEN '(Unit)' " .
                   "END END END as 'Type', " .
                   "c.courseid, c.aos_code, c.aos_period, c.acad_period, " .
                   "c.college, c.aos_description, c.full_description, c.school, c.aos_type " .
                   "FROM COURSES c " .
                   "WHERE c.courseid NOT IN (SELECT e.courseid FROM STAFF_ENROLMENTS e " .
                   "WHERE e.staffid = '$loggedin_username')";
        }
        
        $content .='<form id="results">';
        $content .='<input type="hidden" id="resulttype" value="'.$result_type.'">';
        $content .='<input type="submit" class="submit" name="reload" id="reload" value="Reload">';
        $content .='</form>';
        $content .='<input type="submit" class="submit" name="back" id="back" value="Back">';
        
        if($result_type=='ue' || $result_type=='') {
            $content .='<h2>My enrollments</h2>';
            $content .='The grid below lists the enrollments for the logged in user.<br>';
            $content .='To remove an enrollment entry, just click on the entry in the grid.';
        } else {
            $content .='<h2>Possible user enrollments</h2>';
            $content .='The grid below lists all possible courses the logged in user can be enrolled on.<br>';
            $content .='To add a course enrollment for the logged in user, just click on an entry in the grid.';
        }
        
        $content .='<div id="quick_filter"><b>Quick Filter: ';
        $content .='<a href="#" onclick="$(\'#table-example_filter input\').val(\'\'); $(\'#table-example_filter input\').keyup();">All</a> | ';
        $content .='<a href="#" onclick="$(\'#table-example_filter input\').val(\'(Programme)\'); $(\'#table-example_filter input\').keyup();">Programmes</a> | ';
        $content .='<a href="#" onclick="$(\'#table-example_filter input\').val(\'(Course)\'); $(\'#table-example_filter input\').keyup();">Courses</a> | ';
        $content .='<a href="#" onclick="$(\'#table-example_filter input\').val(\'(Unit)\'); $(\'#table-example_filter input\').keyup();">Units</a>';
        $content .='</b/></div>';
        
        $content .='<div class="data_grid">';
        $content .='<div class="box">';
        $content .='<div class="header">';
        $content .='<img width="16" height="16" src="img/icons/packs/fugue/16x16/shadeless/table-excel.png">';
        $content .='<h3>Enrolments</h3><span></span>';
        $content .='</div>';
        $content .='<div class="content">';
        $content .='<div class="dataTables_wrapper" id="table-example_wrapper">';
        
        $content .='<table class="table" id="table-example">';
        
        $mysqli->set_charset("utf8");
                
        if ($result = $mysqli->query($sql)) {
            
            if($result->num_rows==0) {
                $content .= '<tr><th>No Enrolment Data</td></th></tr>';
                
                $result->close();
            } else {
                $content .='<thead><tr>';
                
                // show users enrollments
                if($result_type=='ue' || $result_type=='') {

                    $content .='<th class="sorting_desc" rowspan="1" colspan="1">Record Id</th><th class="sorting" rowspan="1" colspan="1">Type</th><th class="sorting" rowspan="1" colspan="1">Enrolment Id</th><th class="sorting" rowspan="1" colspan="1">Staff Id</th><th class="sorting" rowspan="1" colspan="1">Stage Id</th>';
                    $content .='<th class="sorting" rowspan="1" colspan="1">Course Id</th><th class="sorting" rowspan="1" colspan="1">AOS Code</th><th class="sorting" rowspan="1" colspan="1">AOS Period</th><th class="sorting" rowspan="1" colspan="1">ACAD Period</th>';
                    $content .='<th class="sorting" rowspan="1" colspan="1">College</th><th class="sorting" rowspan="1" colspan="1">AOS Description</th><th class="sorting" rowspan="1" colspan="1">Full Description</th><th class="sorting" rowspan="1" colspan="1">School</th><th class="sorting" rowspan="1" colspan="1">AOS Type</th>';
                    
                    $content .='</tr></thead>';
                    
                    $content .='<tbody>';
                    
                    while ($row = $result->fetch_object()) {
                        $content .='<tr class="gradeA odd">';
                        $content .='<td class="sorting_1">'.$row->record_id.'</td>';
                        $content .='<td class="sorting_1">'.$row->Type.'</td>';
                        $content .='<td class="sorting_1">'.$row->enrolmentid.'</td>';
                        $content .='<td class="sorting_1">'.$row->staffid.'</td>';
                        $content .='<td class="sorting_1">'.$row->stageid.'</td>';
                        $content .='<td class="sorting_1">'.$row->courseid.'</td>';
                        $content .='<td class="sorting_1">'.$row->aos_code.'</td>';
                        $content .='<td class="sorting_1">'.$row->aos_period.'</td>';
                        $content .='<td class="sorting_1">'.$row->acad_period.'</td>';
                        $content .='<td class="sorting_1">'.$row->college.'</td>';
                        $content .='<td class="sorting_1">'.$row->aos_description.'</td>';
                        $content .='<td class="sorting_1">'.$row->full_description.'</td>';
                        $content .='<td class="sorting_1">'.$row->school.'</td>';
                        $content .='<td class="sorting_1">'.$row->aos_type.'</td>';
                        $content .='</tr>';
                    }
                } else {

                    $content .='<th class="sorting_desc" rowspan="1" colspan="1">Course Id</th><th class="sorting" rowspan="1" colspan="1">Type</th><th class="sorting" rowspan="1" colspan="1">AOS Code</th><th class="sorting" rowspan="1" colspan="1">Aos Period</th><th class="sorting" rowspan="1" colspan="1">Acad Period</th>';
                    $content .='<th class="sorting" rowspan="1" colspan="1">College</th><th class="sorting" rowspan="1" colspan="1">AOS Description</th><th class="sorting" rowspan="1" colspan="1">Full Description</th><th class="sorting" rowspan="1" colspan="1">School</th><th class="sorting" rowspan="1" colspan="1">AOS Type</th>';
                    
                    $content .='</tr></thead>';
                    
                    $content .='<tbody>';
                    
                    while ($row = $result->fetch_object()) {
                        $content .='<tr class="gradeA odd">';
                        $content .='<td class="sorting_1">'.$row->courseid.'</td>';
                        $content .='<td class="sorting_1">'.$row->Type.'</td>';                        
                        $content .='<td class="sorting_1">'.$row->aos_code.'</td>';
                        $content .='<td class="sorting_1">'.$row->aos_period.'</td>';
                        $content .='<td class="sorting_1">'.$row->acad_period.'</td>';
                        $content .='<td class="sorting_1">'.$row->college.'</td>';
                        $content .='<td class="sorting_1">'.$row->aos_description.'</td>';
                        $content .='<td class="sorting_1">'.$row->full_description.'</td>';
                        $content .='<td class="sorting_1">'.$row->school.'</td>';
                        $content .='<td class="sorting_1">'.$row->aos_type.'</td>';
                        $content .='</tr>';
                    }
                }
            }    
        }   
        
        $result->close();
        
        $content .='</tbody></table>';
        
        $content .='</div> <!-- End of .content -->';
        $content .='<div class="clear"></div>';
        $content .='</div> <!-- End of .box -->';
        $content .='</div>';
        $content .='</div>';
        
        $mysqli->close();
    }   
    
    $footer = '';
    
    // Added for grid
    $footer .='<script>window.jQuery || document.write(\'<script src="script/libs/jquery-1.7.1.min.js"><\/script>\')</script>';
    $footer .='<script>window.jQuery.ui || document.write(\'<script src="script/libs/jquery-ui-1.8.16.min.js"><\/script>\')</script>';
    
    $footer .= '<script src="script/gridmanager.js"></script>';
    
    $footer .='<script defer src="script/plugins.js"></script> <!-- REQUIRED: Different own jQuery plugnis -->';
    $footer .='<script defer src="script/mylibs/jquery.ba-resize.min.js"></script> <!-- RECOMMENDED when using sidebar: page resizing -->';
    $footer .='<script defer src="script/mylibs/jquery.easing.1.3.js"></script> <!-- RECOMMENDED: box animations -->';
    $footer .='<script defer src="script/mylibs/jquery.chosen.js"></script>';
    $footer .='<script defer src="script/mylibs/jquery.validate.js"></script>';
    $footer .='<script defer src="script/mylibs/jquery.dataTables.js"></script>';
    $footer .='<script defer src="script/script.js"></script> <!-- REQUIRED: Generic scripts -->';
    $footer .='<!-- end scripts -->';
    $footer .='<script defer>';
    $footer .='$(window).load(function() {';
    $footer .='    $(\'#table-example\').dataTable();';
    $footer .='    $(\'#table-example\').css(\'visibility\',\'visible\');';
    $footer .='});';
    $footer .='</script>';
    $footer .='<!--[if lt IE 7 ]>';
    $footer .='<script defer src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>';
    $footer .='<script defer>window.attachEvent(\'onload\',function(){CFInstall.check({mode:\'overlay\'})})</script>';
    $footer .='<![endif]-->';
    
    $footer .='</div>';
    $footer .='</body>';
    $footer .='</html>';
    
    
    echo $header . $content . $footer;
    
    
    
    
    