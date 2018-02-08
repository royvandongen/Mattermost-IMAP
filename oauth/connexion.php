<?php
session_start();
/**
 * @author Roy van Dongen <roy at rvandongen dot net>
 */

require_once("config_email.php");

include("header.html");

echo '

    <body>
        <main role="main" class="container">
';

// Verify all fields have been filled 
if (empty($_POST['user']) || empty($_POST['password'])) 
{
    echo 'Please fill in your Username and Password<br /><br />';
    echo 'Click <a href="./index.php">here</a> to come back to login page';
}
else
{
	// Check received data length (to prevent code injection) 
	if (strlen($_POST['user']) > 100)
	{
		echo 'Username has incorrect format... Please try again<br /><br />';
		echo 'Click <a href="./index.php">here</a> to come back to login page';
    }
    elseif (strlen($_POST['password']) > 50 || strlen($_POST['password']) <= 7)
    {
	echo 'Password has incorrect format... Please try again<br /><br />';
		echo 'Click <a href="./index.php">here</a> to come back to login page';
    } 
    else {
        // Remove every html tag and useless space on username (to prevent XSS)
        $user=strip_tags(trim($_POST['user']));

        $user=strtolower($_POST['user']);
        $pass=$_POST['password'];

        $mbox = imap_open ("{" . $mail_host . ":" . $mail_port . "/" . $mail_proto . "/ssl}INBOX", $user, $pass, NULL, 1, array('DISABLE_AUTHENTICATOR' => 'PLAIN'));
 
        if(!empty($mbox)) {
            $_SESSION['uid'] = $user;

            // If user came here with an autorize request, redirect him to the authorize page. Else prompt a simple message.
            if (isset($_SESSION['auth_page'])) {
                $auth_page=$_SESSION['auth_page'];
                header('Location: ' . $auth_page);
                exit();
            } else {
                echo "Authentication Succesfull! <br />";
            }
        } else {
        // check login on LDAP has failed. Login and password were invalid or LDAP is unreachable
            echo "Authentication Failed ... Check your username and password.<br />If error persist contact your administrator.<br /><br />";
            echo 'Click <a href="./index.php">here</a> to come back to login page';
        }
    }
}

echo '
    </main>
  </body';
include("footer.html");

?>
