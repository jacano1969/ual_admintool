<?php

    session_start();

    // get action_id
    $workflow_action_id=0;
    if(!empty($_GET['action_id'])) {
        $workflow_action_id=$_GET['action_id'];
    } else {
        echo "action_id false";
    }
    
    // get id of record to be rejected
    $id=0;
    if(!empty($_GET['id'])) {
        $id=$_GET['id'];
    } else {
        echo "id false";
    }
    
    include_once('../dbconfig.php');
    include_once('../lib.php');
    
    // get workflow data id
    $sql="select workflow_data_id from workflow_action where workflow_action_id=$workflow_action_id";
    
    $mysqli = new mysqli($CFG->db_host, $CFG->db_user, $CFG->db_pass, $CFG->db_name);
    
    $workflow_data_id = '';
        
    if (mysqli_connect_error()) {
        header('Location: login.php?error=4');
        exit;
    }
    
    if ($result = $mysqli->query($sql)) {
        
        while($row = $result->fetch_object()) {
            $workflow_data_id=$row->workflow_data_id;
        }
        
        $result->close();
    } else{
        $mysqli->close();
        echo "error excecuting sql 1";
    }
        
    // get workflow data
    $sql="select data as 'table_name' from workflow_data where workflow_data_id=$workflow_data_id";
    
    // approve record
    if ($result2 = $mysqli->query($sql)) {
        while($row2 = $result2->fetch_object()) {
            $table_name=$row2->table_name;
        }
        
        $result2->close();
    } else{
        $mysqli->close();
        echo "error excecuting sql 2";
    }
    
    if(sql_update("update $table_name set rejected=1 where id=$id")==true){
        
        // send reject email
        $sql = "SELECT message FROM course_request_email where email_type=3 and status=1";

        $mailto = '';
        $message = '';
        $subject = '';
        
        // get records
        $mysqli = new mysqli($CFG->db_host, $CFG->db_user, $CFG->db_pass, $CFG->db_name);

        if ($message_result = $mysqli->query($sql)) {
            if($message_result->num_rows!=0) {
                while($message_row = $message_result->fetch_object()) {
                    $message = $message_row->message;
                }
            }
            $message_result->close();
        }                  
        
        // create sql
        $sql = "SELECT subject FROM course_request_email where email_type=3 and status=1";

        // get records
        if ($subject_result = $mysqli->query($sql)) {
            if($subject_result->num_rows!=0) {
                while($subject_row = $subject_result->fetch_object()) {
                    $subject = $subject_row->subject;
                }
            }
            
            $subject_result->close();
        }
        
        // get the username requesting course
        $sql="SELECT requested_by from course_request where id=$id";
        
        $requesting_user = '';
        
        if ($result = $mysqli->query($sql)) {
            while($row = $result->fetch_object()) {
                $requesting_user=$row->requested_by;
            }
            
            $result->close();
        } else{
            $mysqli->close();
            echo "error excecuting sql: $sql";
        }
        
        // get email address for requesting user
        $sql="SELECT EMAIL from users where USERNAME='$requesting_user'";

        if ($result = $mysqli->query($sql)) {
            while($row = $result->fetch_object()) {
                $mailto=$row->EMAIL;
            }
            
            $result->close();
        } else{
            $mysqli->close();
            echo "error excecuting sql: $sql";
        }
        
        // check if requesting user is a new user
        if($mailto=='') {
            $sql="SELECT EMAIL from new_users where USERNAME='$requesting_user'";

            if ($result = $mysqli->query($sql)) {
                while($row = $result->fetch_object()) {
                    $mailto=$row->EMAIL;
                }
                
                $result->close();
            } else{
                $mysqli->close();
                echo "error excecuting sql: $sql";
            }   
        }
        
        // check if an email is to be sent        
        if($mailto!='') {
            
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $headers .= 'To: ' . $mailto . "\r\n";
            $headers .= 'From: UAL AdminTool' . "\r\n";
            
            if($message!='') {
                
                // replace any user fields
                $message=str_ireplace('$FIRSTNAME',$_SESSION['FIRSTNAME'],$message);
                $message=str_ireplace('$LASTNAME',$_SESSION['LASTNAME'],$message);
                $message=str_ireplace('$USERNAME',$_SESSION['USERNAME'],$message);
                $message=str_ireplace('$EMAIL',$_SESSION['EMAIL'],$message);
                $message=str_ireplace('$MOBILEPHONE',$_SESSION['MOBILEPHONE'],$message);
                
                $breaks = array("<br />","<br>","<br/>");  
                $message_ready = str_ireplace($breaks, "\r\n", $message);

                // if debugging
                if(!empty($CFG->debug) && $CFG->debug==true) {
                    // print out mail instead of sending
                    //echo "Headers: $headers \nMailto: $mailto \nMessage: $message";
                } else {
                    mail($mailto, $subject, $message_ready, $headers);
                }
                
            }
        }
        
        $mysqli->close();
        
        // construct JSON string
        $json_data= '{ "username" : "'.$requesting_user.'", "message" : "This course has been rejected."}';
        
        echo $json_data;     
    } else {
        $mysqli->close();
        echo "An Error occurred.";
    }