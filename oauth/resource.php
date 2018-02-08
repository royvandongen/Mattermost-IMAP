<?php
/**
 * @author Denis CLAVIER <clavierd at gmail dot com>
 * Adapted from Oauth2-server-php cookbook
 * @see http://bshaffer.github.io/oauth2-server-php-docs/cookbook/
 */

// include our OAuth2 Server object
require_once __DIR__.'/server.php';

// Handle a request to a resource and authenticate the access token
if (!$server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
    $server->getResponse()->send();
    die;
}

// set default error message
$resp = array("error" => "Unknown error", "message" => "An unknown error has occured, please report this bug");

// get information on user associated to the token
$info_oauth = $server->getAccessTokenData(OAuth2\Request::createFromGlobals());
$user = $info_oauth["user_id"];
$assoc_id = $info_oauth["assoc_id"];

$email = $user;
$usernamearray = explode("@", $user);
$username = str_replace(".", " ", $usernamearray[0]);

// Create user info from the user-id
$resp = array("name" => $username,"username" => $username,"id" => (int)$assoc_id,"state" => "active","email" => $email,"login" => $user);

// send data or error message in JSON format
echo json_encode($resp);
