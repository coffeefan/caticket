<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EMailManager{

    public static function sendMail($emails,$subject,$message,$txtmessage,$attachments=[]){
        // Instantiation and passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = Config::$smtphost;                    // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = Config::$smtpusername;                     // SMTP username
            $mail->Password   = Config::$smtppasswort;                               // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = 465;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
            $mail->CharSet ="UTF-8";

            //Recipients
            $mail->setFrom(Config::$smtpusername, 'Chrischona Aadorf');
            foreach($emails as $email){
                $mail->addAddress($email);     // Add a recipient
            }

            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $subject;
            Flight::render("emaillayout.php",array("message"=>$message),'mailbody');

            $mail->Body    =  Flight::view()->get('mailbody');
            $mail->AltBody = $txtmessage;

            if($attachments!=[]) {
                foreach($attachments as $attachment){
                    $mail->addAttachment($attachment);         // Add attachments
                }
            }



            $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    public static function sendReservationSubmitMail($firstname,$lastname,$address,$city,$mobile,$email, $eventname, $eventstart){
        $phpdate = strtotime( $eventstart );
        $eventstart = date( 'd.m H:i', $phpdate );

        $subject="Reservationsbestaetigung";
        $messagetxt="Gerne bestaetigen wir die Reservation zum".$eventname." am ".$eventstart." mit folgenden Daten
".$firstname." ".$lastname."
".$address."
".$city."
".$mobile."
".$email;
        Flight::render("emailreservation.php",array("firstname"=>$firstname,"lastname"=>$lastname,
            "address"=>$address,"city"=>$city,"mobile"=>$mobile,"email"=>$email, "eventname"=>$eventname,
            "eventstart"=>$eventstart),'mailmessage');
        EMailManager::sendMail([$email],$subject,Flight::view()->get('mailmessage'),$messagetxt);
        return new Response([]);
    }
}