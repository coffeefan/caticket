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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
require 'config.php';

require_once 'Model/Response.php';
require_once  'Model/EventExcelManager.php';
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
    $response=$em->createReservation($body->firstname,$body->lastname,$body->address,
        $body->city,$body->mobile,$body->reservationkey,$body->email);

    if($response->getErrorcode()!=-1)  sendResponse($response);
    $response=$em->getReservation($body->reservationkey);
    $reservation=$response->getBody();
   sendResponse(EMailManager::sendReservationSubmitMail(
        $reservation["firstname"],$reservation["lastname"],$reservation["address"],$reservation["city"],$reservation["mobile"],$reservation["email"], $reservation["eventname"], $reservation["eventstart"]));

});

Flight::route('DELETE /events/reservation/@eventid/@reservationkey', function($eventid,$reservationkey){
    $em=new EventManager();
    sendResponse($em->deleteReservationWindow($reservationkey,$eventid));
});

Flight::route('/events/sendmail', function(){
    EMailManager::sendMail("christianbachmann@outlook.com","Eine Info","Das ist aber ein super Text","Das ist aber ein super Text");
    echo "test";
});


Flight::route('/checktoken', function(){
    AuthManager::checkTocken();
    echo 'hello world! This is the secure';
});



Flight::route('GET /admin/downloadreport/@eventid', function($eventid){
    AuthManager::checkTocken();
    $eexm=new EventExcelManager();
    $em=new EventManager();

    $revent=$em->getEvent($eventid);

    $event=$revent->getBody();
    if(count($event)>1){
        return sendResponse($response=$eexm->prepareEventReportData($event));

    }
    return sendResponse(new Response([],400,"Event registration not possible"));
});




Flight::route('/token', function(){
    $body = json_decode(Flight::request()->getBody());
    $am=new AuthManager();
    sendResponse($am->login($body->username,$body->password));
});

Flight::route('GET /admin/events/', function(){
    AuthManager::checkTocken();
    $body = json_decode(Flight::request()->getBody());
    $em=new EventManager();
    $response=$em->getEvents();
    sendResponse($response);
});

Flight::route('POST /admin/events/', function(){
    AuthManager::checkTocken();
    $body = json_decode(Flight::request()->getBody());
    $em=new EventManager();
    $response=$em->addEvent($body->eventname,$body->eventstart,$body->eventend,
        $body->maxvisitors);
    sendResponse($response);
});

Flight::route('PUT /admin/events/@eventid', function($eventid){
    AuthManager::checkTocken();
    $body = json_decode(Flight::request()->getBody());
    $em=new EventManager();
    $response=$em->updateEvent($eventid,$body->eventname,$body->eventstart,$body->eventend,
        $body->maxvisitors);
    sendResponse($response);
});

Flight::route('DELETE /admin/events/@eventid', function($eventid){
    AuthManager::checkTocken();
    $body = json_decode(Flight::request()->getBody());
    $em=new EventManager();
    $response=$em->deleteEvent($eventid);
    sendResponse($response);
});

Flight::route('GET /admin/visitors/@eventid', function($eventid){
    AuthManager::checkTocken();
    $body = json_decode(Flight::request()->getBody());
    $em=new EventManager();
    $response=$em->getAllPossibleVisitors($eventid);
    sendResponse($response);
});

Flight::route('DELETE /admin/visitors/@visitorid', function($visitor){
    AuthManager::checkTocken();
    $em=new EventManager();
    $response=$em->unsubscribe($visitor);
    sendResponse($response);
});






Flight::route('/clearing', function(){

    //Clearing reservation not used
    $body = json_decode(Flight::request()->getBody());
    $em=new EventManager();
    sendResponse($em->clearReservation());

    //Sending reports
    $eexm=new EventExcelManager();
    $em=new EventManager();

    $revent=$em->getEventForReport();

    $event=$revent->getBody();
    if(count($event)>1){
        $response=$eexm->prepareEventReportData($event);
        EMailManager::sendMail(Config::$sendreportsTo, "report", "Im Anhang befindet sich der aktuelle Report.",
            "Im Anhang befindet sich der aktuelle Report.",[$response->getBody()["filename"]]);
        $em->setReportSended($event["eventid"]);
    }

    //clearing files
    $filename="data";
    if (file_exists($filename)) {
        foreach (new DirectoryIterator($filename) as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }
            if ($fileInfo->isFile() && time() - $fileInfo->getCTime() >= 15) {
                unlink($fileInfo->getRealPath());
            }
        }
    }

    echo "ok";

});

/*
 * Init for creating admin user
Flight::route('/admin/createuser', function(){
    $am=new AuthManager();
    $am->register("Christian","Bachmann","admin","1234");
});
*/


Flight::start();