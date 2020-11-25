<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EMailManager{
    /*
    public static function sendMail(){
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("chrischonaticketing@kirchenaadorf.ch", "Chrischona");
        $email->setSubject("Reservationsbestaetigung");
        $email->addTo("c.bachmann@inaffect.net", "Christian Bachmann");
        $email->addContent("text/plain", "Guten Morgen Christian du hast erfolgreich dein Ticket reserviert");
        $email->addContent(
            "text/html", "<strong>Guten Morgen Christian du hast erfolgreich dein Ticket reserviert.</strong>"
        );
        $sendgrid = new \SendGrid(Config::$SENDGRID_API_KEY);
        try {
            $response = $sendgrid->send($email);
            print $response->statusCode() . "\n";
            print_r($response->headers());
            print $response->body() . "\n";
        } catch (Exception $e) {
            echo 'Caught exception: '. $e->getMessage() ."\n";
        }
    }*/

    public static function sendMail(){

        $email="christianbachmann@outlook.com";

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
            $mail->addAddress($email);     // Add a recipient
            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'Reservationsbestätigung';
            $mail->Body    = 'Gerne bestätiguen wir die Reservation vom Gottesdienst';
            $mail->AltBody = 'Gerne bestätiguen wir die Reservation vom Gottesdienst';

            $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}