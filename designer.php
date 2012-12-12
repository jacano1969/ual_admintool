<?php

session_start();

require_once('lib.php');


$stage = '';

if(!empty($_GET['stage'])) {
    $stage = $_GET['stage'];
}


if(is_logged_in()){
    
    // header
    $header = '';
    $header .= '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
    $header .= '<html lang="en" dir="ltr">';
    $header .= '<head>';
    $header .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
    $header .= '<title>UAL Admn Tool Designer</title>';
    $header .= '<link href="css/style.css" type="text/css" rel="stylesheet">';
    $header .= '<script src="script/jquery-1.8.1.min.js" type="text/javascript"></script>';
    #$header .= '<script src="script/jquery.lightbox_me.js" type="text/javascript"></script>';
    $header .= '<script src="script/jquery.validate.min.js" type="text/javascript"></script>';
    $header .= '<script src="script/jquery.stepy.min.js" type="text/javascript"></script>';
    #$header .= '<script type="text/javascript" src="script/jquery.tablesorter.js"></script>';
    #$header .= '<script src="script/ual_admintool.js" type="text/javascript"></script>';
    $header .= '</head>';
    
    
	// main
    $main = '';
    $main .= '<body id="designer-page">';
    $main .= '<div class="container">';
    
	$main .= '<fieldset>';
    $main .= '<legend>';
    $main .= 'Workflow Designer';
    $main .= '</legend>';
	
	// begin
	if($stage=='') {
	    // get workflow
	    // select workflow_id as id, name as name, description as description from workflow where status=1;
		$main .= get_designer_workflows();
	}
	
	
	
    
    // get designer wizard
    
	/*<form id="custom">
		<fieldset title="Thread 1">
		    <legend>description one</legend>

			<label>User:</label>
			<!-- Hidden fields are not focused.  -->
			<input type="hidden" name="hidden" />

			<!-- Disabled fields are not validated.  -->
			<input type="text" value="wbotelhos" size="40" name="user" disabled="disabled" />

			<label>E-mail:</label>
			<input type="text" size="40" name="email" />
			<input type="checkbox" name="checked" /> Checked?

			<label>Newsletter?</label>
			<input type="radio" name="newsletter" /> Yep
			<input type="radio" name="newsletter" /> Nope

			<label>Password:</label>
			<input type="password" name="password" size="40" />
		</fieldset>

		<fieldset title="Thread 2">
			<legend>description two</legend>

			<label>Nick Name:</label>
			<input type="text" size="30" />

			<label>Bio:</label>
			<textarea name="bio" rows="5" cols="60"></textarea>
		</fieldset>

		<fieldset title="Thread 3">
			<legend>description three</legend>

			<label>Birthday:</label>
			<select name="day">
				<option></option>
				<option>23</option>
			</select>

			<select>
				<option>10</option>
			</select>

			<select>
				<option>1984</option>
			</select>

			<label>Site:</label>
			<input type="text" name="site" size="40" />
		</fieldset>

		<input type="submit" class="finish" value="Finish!" />
	</form><br/>

	<script>
	$('#custom').stepy({
            backLabel:      'Backward',
            block:          true,
            errorImage:     true,
            nextLabel:      'Forward',
            titleClick:     true,
            validate:       true
    }); 
    </script>*/
	
	$main .= '</fieldset>';
    $main .= '</div>';    
    
    
	
	// footer
    $footer = '';
    $footer .='</body>';
    $footer .='</html>';
    
    
	// display designer
    $content = $header . $footer;
    
    echo $content;
    
} else {
    header('Location: login.php');
    exit;
}

