<?php

class Config{
    public static $dbserver="yourserver";
    public static $dbname="yourdbname";
    public static $dbusername="yourdbuser";
    public static $dbpassword="yourpassword";
    
    public static $reservationpreperationtime=300;
    public static $JWTSECRETKEY='your JWTSECRETKEY';

    public static $smtphost="ssl://smtp.gmail.com";
    public static $smtpusername="your Username/E-Mail";
    public static $smtppasswort="your Passwort";


    public static $sendreportsMinutesBeforeStart=62;
    public static $sendreportsTo=["E-Mail One","E-Mail Two",..];
}