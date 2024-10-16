<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    
    // Validazione dei dati
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        echo "Per favore, compila tutti i campi.";
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Indirizzo email non valido.";
        exit;
    }
    
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = 2;                                 // Abilita output di debug dettagliato
        $mail->isSMTP();                                      // Usa SMTP
        $mail->Host       = 'smtp.office365.com';        // Server SMTP di Hotmail/Outlook
        $mail->SMTPAuth   = true;                             // Abilita autenticazione SMTP
        $mail->Username   = '';      // Il tuo indirizzo email Hotmail/Outlook
        $mail->Password   = '';           // La tua password o password per app
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   // Abilita TLS implicito
        $mail->Port       = 587;                              // Porta TCP da usare

        //Recipients
        $mail->setFrom('fabianaparisi.itconsulting@outlook.it', 'Fabiana');
        $mail->addAddress('fabianaparisi.itconsulting@outlook.it');
        $mail->addReplyTo($email, $name);

        // Content
        $mail->isHTML(false);
        $mail->Subject = "Nuovo messaggio da: " . htmlspecialchars($name);
        $mail->Body    = "Hai ricevuto un nuovo messaggio.\n\n" .
                         "Nome: " . htmlspecialchars($name) . "\n" .
                         "Email: " . htmlspecialchars($email) . "\n" .
                         "Oggetto: " . htmlspecialchars($subject) . "\n" .
                         "Messaggio:\n" . htmlspecialchars($message);

        $mail->send();

        // Reindirizzamento alla pagina di ringraziamento
        
        header("Location: grazie.html");
        exit();
    } catch (Exception $e) {
        echo "Si è verificato un errore nell'invio del messaggio. Dettagli dell'errore: {$mail->ErrorInfo}";
    }
} else {
    echo "Accesso non consentito.";
}
?>
