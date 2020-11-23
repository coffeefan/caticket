<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: Origin, Content-Type, Authorization, X-Auth-Token');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, HEAD, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET' || $_SERVER['REQUEST_METHOD'] === 'PUT' || $_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if(strpos( $_SERVER['REQUEST_URI'], "sync") !== false){
        header("Content-Type: text/html");
    }
    if(strpos( $_SERVER['REQUEST_URI'], "directdownloadfile") !== false){

    }
    else{
        header("Content-type:application/json");
    }

}
require 'vendor/autoload.php';
require 'config.php';

require_once 'Model/EventManager.php';
require_once 'Model/EventManager.php';


// error reporting (this is a demo, after all!)
ini_set('display_errors', 1);
error_reporting(E_ALL);

/*
Flight::map('error', function(Exception $ex){
    http_response_code($ex->getCode());
    echo json_encode(array(
        'message' => $ex->getMessage()
    ));
});*/

Flight::route('OPTIONS /*', function(){
    echo 200;
});
 

Flight::route('/', function(){
    echo 'hello world! This is the api';
});

Flight::route('/events/active', function(){
    $em=new EventManager();
    echo json_encode($em->getActiveEvents());
});


Flight::start();