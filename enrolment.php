<?php

//session_start();

//require_once('lib.php');

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
    
    exit;
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    $result_type='';
$programme='';
$course_year='';
$course='';
$unit='';
$pagenum=1;
$numrecords=0;
$totalpages=0;

if(!empty($_GET['pagenum'])) {
    $pagenum = $_GET['pagenum'];
    
    if($pagenum=="undefined") {
        $pagenum=1;
    }
}

if(!empty($_GET['T'])) {
    $result_type = $_GET['T'];
    
    if($result_type=="undefined") {
        $result_type='';
    }
}

if(!empty($_GET['P'])) {
    $programme = $_GET['P'];
    
    if($programme=="undefined") {
        $programme='';
    }
}

if(!empty($_GET['Y'])) {
    $course_year = $_GET['Y'];
    
    if($course_year=="undefined") {
        $course_year='';
    }
}

if(!empty($_GET['C'])) {
    $course = $_GET['C'];
    
    if($course=="undefined") {
        $course='';
    }
}

if(!empty($_GET['U'])) {
    $unit = $_GET['U'];
    
    if($unit=="undefined") {
        $unit='';
    }
}

if(is_logged_in()){
        
    // get results based on filters
    $content ='';
    
    global $CFG;
    
    // get all enrolments for the currently logged in user
    // based on filter
    $mysqli =  new mysqli($CFG->db_host, $CFG->db_user, $CFG->db_pass, $CFG->db_name);
    
    $results ='';
    $sql='';
    
    if (mysqli_connect_error()) {
        header('Location: login.php?error=4');
        exit;
    }
    
    $loggedin_username = $_SESSION['USERNAME'];
    
    
    // user enrolments / default
    if($result_type=='ue' || $result_type=='') {
        // users enrolments
        $sql = "SELECT DISTINCT " .
               "CASE WHEN c.aos_code like('L%') THEN 'Programme' ELSE " . 
               "CASE WHEN c.aos_code REGEXP '^[0-9]' THEN 'Course' ELSE " .
               "CASE WHEN c.aos_code REGEXP '^[A-Z]' THEN 'Unit' " .
               "END END END as 'Type', " .
               "e.record_id, e.enrolmentid, e.staffid, e.stageid, " .
               "c.courseid, c.aos_code, c.aos_period, c.acad_period, c.college, c.aos_description," .
               "c.full_description, c.school,c.aos_type " .
               "from STAFF_ENROLMENTS e " .
               "inner join COURSES c on c.courseid = e.courseid ";
        
        if($unit!=''){
            $sql .=" and c.aos_code='$unit'";
        }else if($course!=''){
            $sql .=" and c.aos_code='$course' ";
        }else if($programme!=''){
            $sql .=" and c.aos_code='$programme' ";
        }
        
        if($course_year!='') {
            $sql .=" and c.acad_period='$course_year' ";
        }
        
        $limit = $pagenum * 15;
        $sql .="inner join COURSE_STRUCTURE cs on cs.aos_code = c.aos_code " .
               "and e.staffid = '$loggedin_username'";
        
        // get num of records for full query
        if ($res = $mysqli->query($sql)) {
            $numrecords = $res->num_rows;
            $totalpages = $numrecords/15;
        }
        
        $sql .="LIMIT $pagenum, $limit";
                          
    } else {
        // course user is NOT enrolled on
        /*$sql ="SELECT c.courseid, c.aos_code, c.aos_period, c.acad_period, " .
              "cs.aoscd_link, cs.lnk_aos_period, cs.lnk_period, cs.compulsry_yn," .
              "c.college, c.aos_description, c.full_description, c.school, c.aos_type " .
              "FROM COURSE_STRUCTURE cs " .
              "INNER JOIN COURSES c " .
              "ON c.aos_code LIKE CONCAT('%', cs.AOS_CODE ,'%') ";*/
        
        $sql ="SELECT c.courseid, c.aos_code, c.aos_period, c.acad_period, " .
              "c.college, c.aos_description, c.full_description, c.school, c.aos_type " .
              "FROM COURSES c ";
              
        /*if($unit!=''){
            $sql .=" and c.aos_code='$unit'";
        }else if($course!=''){
            $sql .=" and c.aos_code='$course' ";
        }else if($programme!=''){
            $sql .=" and c.aos_code='$programme' ";
        }
        
        if($course_year!='') {
            $sql .=" and c.acad_period='$course_year' ";
        }*/
        
        $limit = $pagenum * 15;
        
        /*$sql .="AND c.courseid NOT IN (SELECT e.courseid FROM STAFF_ENROLMENTS e " .
               "WHERE e.staffid = '$loggedin_username') LIMIT $pagenum, $limit";*/
        $sql .="WHERE c.courseid NOT IN (SELECT e.courseid FROM STAFF_ENROLMENTS e " .
               "WHERE e.staffid = '$loggedin_username') ";
               
        // get num of records for full query
        if ($res = $mysqli->query($sql)) {
            $numrecords = $res->num_rows;
            $totalpages = $numrecords/15;
        }
        
        $sql.= "LIMIT $pagenum, $limit";
    }
                          
    /*if($programme!=0){
        $enrolments_sql .= "inner join ";
        select distinct c.aos_code as id,
                        c.full_description as name,
                        c.acad_period as year
                        from COURSES c
                        inner join STAFF_ENROLMENTS e on e.staffid='$loggedin_username'
                        and e.courseid=concat(c.aos_code, c.aos_period, c.acad_period)
                        and c.aos_code like('L%') order by name
    }*/
    
    // testing
    //$content .= $enrolments_sql;
    
    $content .='Page:';
    for($index=1; $index<$totalpages; $index++) {
        if($pagenum==$index) {
            $content .=' '.$index .' ';
        } else {
            $content .=' <a href="#" name="'.$index.'" id="pagenumber">'.$index.'</a> ';
        }
    }
    $content .=$totalpages;
    
    $content .='<form id="results">';
    $content .='<input type="hidden" id="programmes" value="'.$programme.'">';
    $content .='<input type="hidden" id="courseyears" value="'.$course_year.'">';
    $content .='<input type="hidden" id="courses" value="'.$course.'">';
    $content .='<input type="hidden" id="units" value="'.$unit.'">';
    $content .='<input type="hidden" id="resulttype" value="'.$result_type.'">';
    $content .='</form>';
    
    $data_grid .='<div class="data_grid">';
    $data_grid .='<div class="box">';
    $data_grid .='<div class="header">';
    $data_grid .='<img width="16" height="16" src="img/icons/packs/fugue/16x16/shadeless/table-excel.png">';
    $data_grid .='<h3>Enrolments</h3><span></span>';
    $data_grid .='</div>';
    $data_grid .='<div class="content">';
    $data_grid .='<div class="dataTables_wrapper" id="table-example_wrapper">';
    
    $content .='<table class="results" id="table-example">';
    
    if ($result = $mysqli->query($sql)) {
        if($result->num_rows==0) {
            $content .= '<tr><th>No Enrolment Data</td></th></tr>';
            
            $result->close();
        } else {
            $content .='<tr>';
            
            // show users enrolments
            if($result_type=='ue' || $result_type=='') {
                $content .='<th>Remove</th><th>Type</th><th>Record Id</th><th>Enrolment Id</th><th>Staff Id</th><th>Stage Id</th>';
                $content .='<th>Course Id</th><th>AOS Code</th><th>AOS Period</th><th>ACAD Period</th>';
                $content .='<th>College</th><th>AOS Description</th><th>Full Description</th><th>School</th><th>AOS Type</th>';
                
                $content .='</tr>';
                
                while ($row = $result->fetch_object()) {
                    $content .="<tr>";
                    $content .='<td><input type="radio" value="0" id="remove_'.$row->enrolmentid.'" name="remove_'.$row->enrolmentid.'"></td>';
                    $content .="<td>$row->Type</td>";
                    $content .="<td>$row->record_id</td>";
                    $content .="<td>$row->enrolmentid</td>";
                    $content .="<td>$row->staffid</td>";
                    $content .="<td>$row->stageid</td>";
                    $content .="<td>$row->courseid</td>";
                    $content .="<td>$row->aos_code</td>";
                    $content .="<td>$row->aos_period</td>";
                    $content .="<td>$row->acad_period</td>";
                    $content .="<td>$row->college</td>";
                    $content .="<td>$row->aos_description</td>";
                    $content .="<td>$row->full_description</td>";
                    $content .="<td>$row->school</td>";
                    $content .="<td>$row->aos_type</td>";
                    $content .="</tr>";
                }
            } else {
                $content .='<th>Add</th><th>Course Id</th><th>AOS Code</th><th>Aos Period</th><th>Acad Period</th>';
                //$content .='<th>Aos CD Link</th><th>Link AOS Period</th><th>Link Period</th><th>Compulsory</th>';
                $content .='<th>College</th><th>AOS Description</th><th>Full Description</th><th>School</th><th>AOS Type</th>';
                
                $content .='</tr>';
                
                while ($row = $result->fetch_object()) {
                    $content .="<tr>";
                    $content .='<td><input type="radio" value="0" id="add_'.$row->enrolmentid.'" name="add_'.$row->enrolmentid.'"></td>';
                    $content .="<td>$row->courseid</td>";
                    $content .="<td>$row->aos_code</td>";
                    $content .="<td>$row->aos_period</td>";
                    $content .="<td>$row->acad_period</td>";
                    //$content .="<td>$row->aoscd_link</td>";
                    //$content .="<td>$row->lnk_aos_period</td>";
                    //$content .="<td>$row->lnk_period</td>";
                    //$content .="<td>$row->compulsry_yn</td>";
                    $content .="<td>$row->college</td>";
                    $content .="<td>$row->aos_description</td>";
                    $content .="<td>$row->full_description</td>";
                    $content .="<td>$row->school</td>";
                    $content .="<td>$row->aos_type</td>";
                    $content .="</tr>";
                }
            }
            
            $result->close();
        }
    }   
    
    $content .='</table>';
    
    $content .='</div> <!-- End of .content -->';
    $content .='<div class="clear"></div>';
    $content .='</div> <!-- End of .box -->';
    $content .='</div>';
    $content .='</div>';
    
    $mysqli->close();
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
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
    $footer .='$(\'#table-example\').dataTable();';
    $footer .='$(\'#table-example\').css(\'visibility\',\'visible\');';
    $footer .='});';
    $footer .='</script>';
    $footer .='<!--[if lt IE 7 ]>';
    $footer .='<script defer';
    $footer .='src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>';
    $footer .='<script';
    $footer .='defer>window.attachEvent(\'onload\',function(){CFInstall.check({mode:\'overlay\'})})</script>';
    $footer .='<![endif]-->';
    
    $footer .='</body>';
    $footer .='</html>';
    
    
    echo $header . $content . $footer;
    
    
    