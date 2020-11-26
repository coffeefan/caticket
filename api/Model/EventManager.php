<?php
class EventManager{
    protected $db;

    public function __construct()
    {
        $this->db= new PDO(sprintf("mysql:host=%s;dbname=%s;charset=utf8",Config::$dbserver,Config::$dbname), Config::$dbusername, Config::$dbpassword);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    }

    public function getActiveEvents(){
        $date = new DateTime("now", new DateTimeZone('Europe/Berlin') );
        $statement = $this->db->prepare("
            SELECT eventid, eventname,eventstart,eventend,maxvisitors, 
                (SELECT COUNT(*) FROM visitors as bv WHERE eventid=events.eventid AND isdeleted!=1 AND STATUS=10) as bookedvisitors,
                (SELECT COUNT(*) FROM visitors as rv WHERE eventid=events.eventid AND isdeleted!=1 AND STATUS!=10) as reservedvisitors 
                from events
            where eventstart>:now Order by eventstart");
        $statement->execute(array(":now"=>$date->format('Y-m-d H:i:s')));
        return new Response($statement->fetchAll(\PDO::FETCH_ASSOC));
    }

    public function getReservation($reservationkey){
        $date = new DateTime("now", new DateTimeZone('Europe/Berlin') );
        $statement = $this->db->prepare("
            SELECT reservationkey, firstname,lastname,email, address,city, mobile, eventname, eventstart FROM visitors JOIN events ON visitors.eventid=events.eventid WHERE reservationkey=:reservationkey AND STATUS=10 AND isdeleted!=1");
        $statement->execute(array(":reservationkey"=>$reservationkey));
        return new Response($statement->fetch(\PDO::FETCH_ASSOC));
    }

    public function clearReservation(){
        $timleftsince = new DateTime("now", new DateTimeZone('Europe/Berlin') );
        $timleftsince->modify("-8 minutes");
        $statement = $this->db->prepare("Delete from visitors WHERE STATUS=0 AND isdeleted!=1 and reservationstart<:timleftsince");
        $statement->execute(array(":timleftsince"=>$timleftsince->format('Y-m-d H:i:s')));
        return new Response([]);
    }

    public function callforRegistration($eventid){

        $statement = $this->db->prepare("SELECT COUNT(*) AS currentamount, (SELECT maxvisitors FROM events WHERE eventid=:eventid) as maxamount FROM visitors WHERE eventid=:eventid AND isdeleted!=1");
        $statement->execute(array(":eventid"=>$eventid));
        $eventinfo=$statement->fetch(\PDO::FETCH_ASSOC);

        if($eventinfo==null) return new Response([],400,"Event registration not possible");
        if($eventinfo["currentamount"]<$eventinfo["maxamount"]){
            $date = new DateTime("now", new DateTimeZone('	Europe/Berlin') );
            $reservationkey = time().bin2hex(random_bytes(5));
            $statement = $this->db->prepare("
            Insert into visitors (eventid,reservationkey,reservationstart) VALUES (:eventid,:reservationkey,:reservationstart) ");
            $statement->execute(array(":eventid"=>$eventid,":reservationkey"=>$reservationkey,":reservationstart"=>$date->format('Y-m-d H:i:s')));
            return new Response(array("reservationkey"=>$reservationkey));
        }

        return new Response([],406,"Event registration not possible anymore");
    }

    public function checkReservationWindow($reservationkey,$remainplus=0){
        $date = new DateTime("now", new DateTimeZone('Europe/Berlin') );
        $statement = $this->db->prepare("SELECT reservationkey, reservationstart,eventname, eventstart FROM visitors JOIN events ON visitors.eventid=events.eventid
WHERE reservationkey=:reservationkey AND STATUS=0 AND isdeleted!=1");
        $statement->execute(array(":reservationkey"=>$reservationkey));
        $eventinfo=$statement->fetch(\PDO::FETCH_ASSOC);

        if($eventinfo==null) return new Response([],404,"Registrationkey not found");

        $now = new DateTime("now", new DateTimeZone('Europe/Berlin') );
        $diff = strtotime($date->format('Y-m-d H:i:s')) - strtotime($eventinfo["reservationstart"]);

        $remainingseconds=(Config::$reservationpreperationtime+$remainplus)-$diff;
        if($remainingseconds<=0) return new Response([],406,"Registrationkey not valid anymore");

        $eventinfo["remainingseconds"]=$remainingseconds;
        return new Response($eventinfo);
    }

    public function deleteReservationWindow($reservationkey,$eventid){
        $date = new DateTime("now", new DateTimeZone('Europe/Berlin') );
        $statement = $this->db->prepare("Delete FROM visitors WHERE reservationkey=:reservationkey AND eventid=:eventid AND STATUS=0 AND isdeleted!=1");
        $statement->execute(array(":reservationkey"=>$reservationkey,":eventid"=>$eventid));

        return new Response([]);
    }

    public function createReservation($firstname,$lastname,$address,$city,$mobile,$reservationkey,$email){
        $response=$this->checkReservationWindow($reservationkey,60);
        if($response->getErrorcode()!=-1)return new Response([],400,"Not valid ");

        $statement = $this->db->prepare("Update visitors
        set firstname=:firstname, lastname=:lastname,address=:address,city=:city,mobile=:mobile, email=:email, 
        status=10 where reservationkey=:reservationkey");
        $statement->execute(array(":reservationkey"=>$reservationkey,":reservationkey"=>$reservationkey,":reservationkey"=>$reservationkey,
            ":firstname"=>$firstname,":lastname"=>$lastname,":address"=>$address,":city"=>$city,":mobile"=>$mobile,":email"=>$email));
        return new Response(["ok"]);
    }
}