<?php

require("Client.php");

/**
* creating Client Object to be used for calling its functions.
*/
$cliObj = new Client();


/**
* Example for findServers call.
*/
$data = array();
$data['columnName'] = "IP";
$data['value'] = "178.34.234.44";
$result = $cliObj->findServers($data);
echo print_r($result,true);


/**
* Example for listAllServers call.
*/
$data = array();
$data['limit'] = "5";
$data['offset'] = "5";
$result = $cliObj->listAllServers($data);
echo print_r($result,true);


/**
* Example for addServer call.
*/
$data = array();
$data['ServerId'] = "Serv789";
$data['ServerName'] = "Media Streaming";
$data['IP'] = "12.12.12.1";
$data['Location'] = "Home";
$data['Description'] = "home streaming server";
$result = $cliObj->addServer($data);
echo print_r($result,true);


/**
* Example for updateServer call.
*/
$data = array();
$data['ServerId'] = "Serv789";
$data['IP'] = "12.12.12.2";
$data['Location'] = "Friend home";
$data['Description'] = "Friend home streaming server";
$result = $cliObj->updateServer($data);
echo print_r($result,true);


/**
* Example for deleteServers call.
* for deleting only one server.
*/
$data = array();
$data['ServerId'] = "Serv789";
$result = $cliObj->deleteServers($data);
echo print_r($result,true);


/**
* Example for deleteServers call.
* for deleting multiple servers at once.
*/
$data = array();
$data['ServerId'] = array("Serv1","Serv2","Serv3");
$result = $cliObj->deleteServers($data);
echo print_r($result,true);