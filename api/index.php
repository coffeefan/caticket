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
use Firebase\JWT\JWT;
require 'config.php';

require_once 'Model/Response.php';
require_once 'Model/EventManager.php';
require_once 'Model/EMailManager.php';
require_once  'Model/AuthManager.php';





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
    sendResponse($em->getActiveEvents());
});

Flight::route('/events/callforreservation/@eventid', function($eventid){
    $em=new EventManager();
    sendResponse($em->callforRegistration($eventid));
});

Flight::route('/events/checkreservationwindow/', function(){
    $body = json_decode(Flight::request()->getBody());
    $em=new EventManager();
    sendResponse($em->checkReservationWindow($body->reservationkey));
});

Flight::route('POST /events/reservation/', function(){
    $body = json_decode(Flight::request()->getBody());


    $em=new EventManager();
    sendResponse($em->createReservation($body->firstname,$body->lastname,$body->address,
        $body->city,$body->mobile,$body->reservationkey,$body->email));
});

Flight::route('DELETE /events/reservation/@eventid/@reservationkey', function($eventid,$reservationkey){
    $em=new EventManager();
    sendResponse($em->deleteReservationWindow($reservationkey,$eventid));
});

Flight::route('/events/sendmail', function(){
    EMailManager::sendMail();
    echo "test";
});


Flight::route('/admin', function(){
    AuthManager::checkTocken();
    echo 'hello world! This is the secure';
});


Flight::route('/token', function(){
    $body = json_decode(Flight::request()->getBody());
    $am=new AuthManager();
    sendResponse($am->login($body->username,$body->password));
});

/*
 * Init for creating admin user
Flight::route('/admin/createuser', function(){
    $am=new AuthManager();
    $am->register("Christian","Bachmann","admin","1234");
});
*/


Flight::start();