<?php
namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    /**
     * Envoie un email avec PHPMailer
     */
    public static function sendEmail($to, $subject, $body) {

        if (empty($to)) return false;

        $mail = new PHPMailer(true);
        try {
            // Configuration du serveur SMTP 
           
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; 
            $mail->SMTPAuth   = true;
            $mail->Username   = 'cestmoiriyad@gmail.com'; 
            $mail->Password   = 'ypdh oteb mwkk qdsi'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Expéditeur
            $mail->setFrom('no-reply@suivicolis-iut.fr', 'Service Suivi Colis IUT');
            
            // Destinataire
            $mail->addAddress($to);

            // Contenu
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
            return true;
        } catch (Exception $e) {
            // En production, logger l'erreur
            return false;
        }
    }
}
?>
