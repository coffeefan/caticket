<?php

class Response {
    private $errormessage="";
    private $errorcode=-1;
    private $body=[];

    /**
     * Response constructor.
     * @param array $errors
     * @param array $body
     */
    public function __construct(array $body,$errorcode=-1,$errormessage="")
    {
        $this->errorcode = $errorcode;
        $this->errormessage = $errormessage;
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getErrormessage()
    {
        return $this->errormessage;
    }

    /**
     * @param string $errormessage
     */
    public function setErrormessage($errormessage)
    {
        $this->errormessage = $errormessage;
    }

    /**
     * @return int
     */
    public function getErrorcode()
    {
        return $this->errorcode;
    }

    /**
     * @param int $errorcode
     */
    public function setErrorcode($errorcode)
    {
        $this->errorcode = $errorcode;
    }


    /**
     * @return array
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param array $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }


}

function sendResponse(Response $response){
    if($response->getErrorcode()!=-1){
        sendError($response->getErrorcode(),$response->getErrormessage());
    }
    echo json_encode($response->getBody());
}

function sendError($code,$message){
    http_response_code($code);
    echo $message;
    exit();
}