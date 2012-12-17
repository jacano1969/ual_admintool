<?php

session_start();

require_once('lib.php');


$stage = '';

if(!empty($_POST['stage'])) {
    $stage = $_POST['stage'];
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
    #$header .= '<script src="script/jquery.stepy.min.js" type="text/javascript"></script>';
    #$header .= '<script type="text/javascript" src="script/jquery.tablesorter.js"></script>';
    $header .= '<script src="script/ual_admintool.js" type="text/javascript"></script>';
    $header .= '</head>';
    
    
	// main
    $main = '';
    $main .= '<body id="designer-page">';
    $main .= '<div class="container">';
    
	$main .= '<fieldset>';
    $main .= '<legend>';
    $main .= 'Workflow Designer';
    $main .= '</legend>';
	
	// stage 0: select or create new workflow
	if($stage=='') {
	    // get workflow
	    // select workflow_id as id, name as name, description as description from workflow where status=1;
		$main .= get_designer_workflows();
	}
	
	// stage 1: select or create workflow step
	if($stage=='1') {
		$workflow_id=0;
		$workflow_name='';
		$workflow_description='';
		
		// check if create or new
		if(!empty($_POST['workflow_id'])) {
            $workflow_id = $_POST['workflow_id'];
			
			// TODO:
			// select or create new workflow step belonging to this workflow
			//$main .= get_designer_workflow_step($workflow_id);
        } else {
			
			// create new workflow
			if(!empty($_POST['workflow_name']) && !empty($_POST['workflow_description'])) {
			    
				$workflow_name=$_POST['workflow_name'];
		        $workflow_description=$_POST['workflow_description'];
				
				$main .= create_designer_workflow_step($workflow_name, $workflow_description);
			} else {
				// TODO:
				// go back to first stage
				$main .= "An Error has occured";
			}
		}		
	}
	
	if($stage=='2') {
		$workflow_name='';
		$workflow_description='';
		$workflow_step_name='';
		$workflow_step_description='';
		
		if(!empty($_POST['workflow_name']) && !empty($_POST['workflow_description']) &&
			!empty($_POST['workflow_step_name']) && !empty($_POST['workflow_step_description'])) {
		
			$workflow_name=$_POST['workflow_name'];
			$workflow_description=$_POST['workflow_description'];
			$workflow_step_name=$_POST['workflow_step_name'];
			$workflow_step_description=$_POST['workflow_step_description'];
		
		    $main .= create_designer_workflow_sub_step($workflow_name,$workflow_description,$workflow_step_name,$workflow_step_description);
		} else {
		    // TODO:
			// go back to first stage
			$main .= "An Error has occured";
		}
			
	}
	
	if($stage=='3') {
		$workflow_name='';
		$workflow_description='';
		$workflow_step_name='';
		$workflow_step_description='';
		$workflow_sub_step_name='';
		$workflow_sub_step_description='';
		
		if(!empty($_POST['workflow_name']) && !empty($_POST['workflow_description']) &&
			!empty($_POST['workflow_step_name']) && !empty($_POST['workflow_step_description']) && 
			!empty($_POST['workflow_sub_step_name']) && !empty($_POST['workflow_sub_step_description'])) {
		
			$workflow_name=$_POST['workflow_name'];
			$workflow_description=$_POST['workflow_description'];
			$workflow_step_name=$_POST['workflow_step_name'];
			$workflow_step_description=$_POST['workflow_step_description'];
			$workflow_sub_step_name=$_POST['workflow_sub_step_name'];
			$workflow_sub_step_description=$_POST['workflow_sub_step_description'];
		
		    // create form with sub step
		    $main .= create_designer_workflow_action($workflow_name,$workflow_description,$workflow_step_name,$workflow_step_description,$workflow_sub_step_name,$workflow_sub_step_description);
		}
		
		if(!empty($_POST['workflow_name']) && !empty($_POST['workflow_description']) &&
			!empty($_POST['workflow_step_name']) && !empty($_POST['workflow_step_description'])) {
			$workflow_name=$_POST['workflow_name'];
			$workflow_description=$_POST['workflow_description'];
			$workflow_step_name=$_POST['workflow_step_name'];
			$workflow_step_description=$_POST['workflow_step_description'];
		
		    // create form without sub step
		    $main .= create_designer_workflow_action($workflow_name,$workflow_description,$workflow_step_name,$workflow_step_description, '', '');
		}	
	}
	
	if($stage=='4') {
		$workflow_name='';
		$workflow_description='';
		$workflow_step_name='';
		$workflow_step_description='';
		$workflow_sub_step_name='';
		$workflow_sub_step_description='';
		$workflow_action='';
		$workflow_form_elements='';
		
		if(!empty($_POST['workflow_name']) && !empty($_POST['workflow_description']) &&
			!empty($_POST['workflow_step_name']) && !empty($_POST['workflow_step_description']) && 
			!empty($_POST['workflow_sub_step_name']) && !empty($_POST['workflow_sub_step_description']) && 
			!empty($_POST['workflow_action']) && !empty($_POST['workflow_form_elements'])) {
			
			$workflow_name=$_POST['workflow_name'];
			$workflow_description=$_POST['workflow_description'];
			$workflow_step_name=$_POST['workflow_step_name'];
			$workflow_step_description=$_POST['workflow_step_description'];
			$workflow_sub_step_name=$_POST['workflow_sub_step_name'];
			$workflow_sub_step_description=$_POST['workflow_sub_step_description'];
			$workflow_action=$_POST['workflow_action'];
			$workflow_form_elements=$_POST['workflow_form_elements'];
			
			$main .= create_designer_workflow_form($workflow_name,$workflow_description,$workflow_step_name,$workflow_step_description,$workflow_sub_step_name,$workflow_sub_step_description, $workflow_action, $workflow_form_elements);
		}
	}
	
	$main .= '</fieldset>';
    $main .= '</div>';    
    
    
	
	// footer
    $footer = '';
    $footer .='</body>';
    $footer .='</html>';
    
    
	// display designer
    $content = $header . $main . $footer;
    
    echo $content;
    
} else {
    header('Location: login.php');
    exit;
}

