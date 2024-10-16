<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\OAuth;
use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

// Carica le configurazioni da un file esterno
$config = require 'config.php';

// Imposta il provider OAuth
$provider = new GenericProvider([
    'clientId'                => $config['clientId'],
    'clientSecret'            => $config['clientSecret'],
    'urlAuthorize'            => "https://login.microsoftonline.com/{$config['tenantId']}/oauth2/v2.0/authorize",
    'urlAccessToken'          => "https://login.microsoftonline.com/{$config['tenantId']}/oauth2/v2.0/token",
    'urlResourceOwnerDetails' => 'https://graph.microsoft.com/v1.0/me',
    'scopes'                  => 'https://outlook.office.com/SMTP.Send offline_access'
]);

function sendEmail($name, $email, $subject, $message, $provider, $config)
{
    $mail = new PHPMailer(true);

    try {
        // Configurazione del server
        $mail->isSMTP();
        $mail->Host = 'smtp.office365.com';
        $mail->Port = 587;
        $mail->SMTPAuth = true;
        $mail->AuthType = 'XOAUTH2';

        // Ottieni un nuovo access token
        try {
            $accessToken = $provider->getAccessToken('refresh_token', [
                'refresh_token' => $config['refreshToken']
            ]);
        } catch (IdentityProviderException $e) {
            throw new Exception("Errore OAuth: " . $e->getMessage());
        }

        $mail->setOAuth(
            new OAuth([
                'provider' => $provider,
                'clientId' => $config['clientId'],
                'clientSecret' => $config['clientSecret'],
                'refreshToken' => $config['refreshToken'],
                'userName' => $config['emailAddress'],
                'accessToken' => $accessToken->getToken(),
            ])
        );

        // Impostazioni del mittente e del destinatario
        $mail->setFrom($config['emailAddress'], 'Fabiana Parisi');
        $mail->addAddress($config['emailAddress']);
        $mail->addReplyTo($email, $name);

        // Contenuto dell'email
        $mail->isHTML(false);
        $mail->Subject = "Nuovo messaggio da: " . $name;
        $mail->Body = "Hai ricevuto un nuovo messaggio.\n\n" .
                      "Nome: {$name}\n" .
                      "Email: {$email}\n" .
                      "Oggetto: {$subject}\n" .
                      "Messaggio:\n{$message}";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Errore nell'invio dell'email: " . $e->getMessage());
        throw $e;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Validazione
    if (!$name || !$email || !$subject || !$message) {
        die("Tutti i campi sono obbligatori.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Indirizzo email non valido.");
    }

    try {
        if (sendEmail($name, $email, $subject, $message, $provider, $config)) {
            header("Location: grazie.html");
            exit();
        }
    } catch (Exception $e) {
        echo "Si è verificato un errore: " . htmlspecialchars($e->getMessage());
        
        // Log dell'errore
        error_log("Errore nell'invio dell'email: " . $e->getMessage());
        
        // Informazioni di debug aggiuntive
        if (isset($config['debug']) && $config['debug']) {
            echo "<pre>";
            print_r($e);
            echo "</pre>";
        }
    }
} else {
    echo "Accesso non consentito.";
}
?>