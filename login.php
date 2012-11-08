<?php
    if(!empty($_POST['username']) && !empty($_POST['password'])) {
        $username=stripcslashes($_POST['username']);
        $password=stripcslashes($_POST['password']);
        $username=htmlspecialchars($username, ENT_QUOTES, "ISO-8859-1");
        $password=htmlspecialchars($password, ENT_QUOTES, "ISO-8859-1");
        
        if($username !='' && $password !='') {
            require_once('lib.php');
            do_login($username,$password);
        } else {
            $error=0;
        }
    } else {
        $error=0;
    }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en" dir="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>UAL Admn Tool</title>
    <link href="css/style.css" type="text/css" rel="stylesheet">
</head>
<body id="login-page">
    <div class="container">
        <div class="login">
            <fieldset>
                <legend>Admin Tool Login</legend>
                <img src="images/logo.png" alt="UAL Logo" title="UAL Logo">
                <form name="login" method="post" action="login.php">
                    <?php
                    
                    if(!empty($_GET['error'])) {
                        $error = $_GET['error'];
                    }
                    
                    if(empty($error)) {
                        $error = 0;
                    }
                        
                    switch($error) {
                        case 0 :    break;
                        case 1 :    echo '<div id="error" class="error">Incorrect username or password.</div>';
                                    break;
                        case 2 :    echo '<div id="error" class="error">Please log in.</div>';
                                    break;
                        case 3 :    echo '<div id="error" class="error">Your session has expired.  Please log in.</div>';
                                    break;
                        case 4 :   echo '<div id="error" class="error">Error connecting to Database.  Please try again.</div>';
                                    break;
                    }
                    ?>
                    <label for="username">Username:</label><input type="text" name="username" id="username">
                    <label for="password">Password:</label><input type="password" name="password" id="password">
                    <input type="submit" value="Ok" class="submit" onmousedown="this.className='submit down';" onmouseup="this.className='submit';">
                    <input type="submit" value="Cancel" onclick="this.form.reset();return false;" class="submit" onmousedown="this.className='submit down';" onmouseup="this.className='submit';">
                </form>
            </fieldset>
        </div>
    </div>
</body>
</html>

