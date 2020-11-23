<?php
class EventManager{
    protected $db;

    public function __construct()
    {
        $this->db= new PDO(sprintf("mysql:host=%s;dbname=%s",Config::$dbserver,Config::$dbname), Config::$dbusername, Config::$dbpassword);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    }

    public function getActiveEvents(){
        $date = new DateTime("now", new DateTimeZone('	Europe/Berlin') );
        $statement = $this->db->prepare("
            SELECT eventid, eventname,eventstart,eventend,maxvistors FROM events 
            where eventstart>:now Order by eventstart");
        $statement->execute(array(":now"=>$date->format('Y-m-d H:i:s')));
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
}