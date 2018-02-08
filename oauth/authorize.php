<?php
session_start();

/**
 * @author Denis CLAVIER <clavierd at gmail dot com>
 * Adapted from Oauth2-server-php cookbook
 * @see http://bshaffer.github.io/oauth2-server-php-docs/cookbook/
 */

// include our OAuth2 Server object
require_once __DIR__.'/server.php';

$request = OAuth2\Request::createFromGlobals();
$response = new OAuth2\Response();

// validate the authorize request
if (!$server->validateAuthorizeRequest($request, $response)) {
    $response->send();
    die;
}

// if user is not yet authenticated, he is redirected.
if (!isset($_SESSION['uid']))
{
  //store the authorize request
  $explode_url=explode("/", strip_tags(trim($_SERVER['REQUEST_URI']))); 
  $_SESSION['auth_page']=end($explode_url);
  header('Location: index.php');
  exit();
}


// display an authorization form
if (empty($_POST)) {
include("header.html");
  exit('

<body>
    <main role="main" class="container">
        <form method="post" class="form-signin">
            <h2 class="form-signin-heading">Mattermost OAuth Authorisation</h2>
            <div class="form-group">
                <label>Hi, <b>'. $_SESSION['uid'] . '</b><br />Your credentials will be used to login into Mattermost</label>
            </div>
            <div class="form-group">
                <button class="btn btn-success" type="submit" name="authorized" value="Authorize">Accept</button>
                <button class="btn btn-danger" type="submit" name="authorized" value="Deny">Deny</button>
            </div>
        </form>
');
include("footer.html");
}

// print the authorization code if the user has authorized your client
$is_authorized = ($_POST['authorized'] === 'Authorize');
$server->handleAuthorizeRequest($request, $response, $is_authorized,$_SESSION['uid']);

if ($is_authorized) 
{
  // this is only here so that you get to see your code in the cURL request. Otherwise, we'd redirect back to the client
  $code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=')+5, 40);
  header('Location: ' . $response->getHttpHeader('Location'));
  exit();
}

// Send message in case of error
$response->send();

/*<form method="post">
  <label>Mattermost souhaite accéder à vos données LDAP (Identifiant, nom complet, mail) </label><br />
  <input type="submit" name="authorized" value="Authorize">
  <input type="submit" name="authorized" value="Deny">
</form>
*/
