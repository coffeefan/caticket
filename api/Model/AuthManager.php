<?php
use Firebase\JWT\JWT;

class AuthManager
{
    protected $db;

    public function __construct()
    {
        $this->db= new PDO(sprintf("mysql:host=%s;dbname=%s;charset=utf8",Config::$dbserver,Config::$dbname), Config::$dbusername, Config::$dbpassword);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    }

    public function register($firstname,$lastname,$username,$password){

        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        $statement = $this->db->prepare("Insert into users
       SET  firstname = :firstname,
            lastname = :lastname,
            username = :username,
            password = :password");
        $statement->execute(array(":firstname"=>$firstname,":lastname"=>$lastname,":username"=>$username,
            ":password"=>$password_hash));

    }

    public function login($username,$password){

        $statement = $this->db->prepare("SELECT id, username, firstname, lastname, password FROM users WHERE username=:username LIMIT 0,1");
        $statement->execute(array(":username"=>$username));
        $u=$statement->fetch(\PDO::FETCH_ASSOC);
        if($u!=null){
            if(password_verify($password,$u["password"])){
                $secret_key = Config::$JWTSECRETKEY;
                $issuer_claim = "CATICKET"; // this can be the servername
                $audience_claim = "THE_AUDIENCE";
                $issuedat_claim = time(); // issued at
                $notbefore_claim = $issuedat_claim; //not before in seconds
                $expire_claim = $issuedat_claim + 86400; // expire time in seconds
                $token = array(
                    "iss" => $issuer_claim,
                    "aud" => $audience_claim,
                    "iat" => $issuedat_claim,
                    "nbf" => $notbefore_claim,
                    "exp" => $expire_claim,
                    "data" => array(
                        "id" => $u["id"],
                        "firstname" => $u["firstname"],
                        "lastname" => $u["lastname"],
                        "username" => $u["username"]
                    ));

                $jwt = JWT::encode($token, $secret_key);
                return new Response(
                    array(
                        "message" => "Successful login.",
                        "jwt" => $jwt,
                        "username" => $username,
                        "expireAt" => $expire_claim
                    ));
            }else{
                return new Response([],401,"Login failed");
            }
        }
    }

    public static function checkTocken(){
        $jwt=null;
        try {

            $headers = apache_request_headers();
            if(isset($headers['Authorization'])){
                $matches = array();
                preg_match('/Token token="(.*)"/', $headers['Authorization'], $matches);
                if(isset($matches[1])){
                    $jwt = $matches[1];
                }
            }
        } catch (Exception $e) {
            mail("christianbachmann@outlook.com","Fehler-Bug",$e->getMessage()."first");
            http_response_code(401);
            echo "Access denied";

            die();
        }
        if($jwt!=null) {

            try {
                $decoded = JWT::decode($jwt, Config::$JWTSECRETKEY, array('HS256'));
                return new Response([true]);


            } catch (Exception $e) {
                mail("christianbachmann@outlook.com","Fehler-Bug",$e->getMessage());
                http_response_code(401);

                echo "Access denied";
                die();
            }
        }
    }

}