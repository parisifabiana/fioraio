<?php
// config.php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'fioraio');

$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if($conn === false){
    die("ERRORE: Impossibile connettersi. " . mysqli_connect_error());
}

return [
    'clientId' => 'YOUR_CLIENT_ID',
    'clientSecret' => 'YOUR_CLIENT_SECRET',
    'tenantId' => 'common',
    'refreshToken' => 'YOUR_REFRESH_TOKEN',
    'emailAddress' => 'fabianaparisi.itconsulting@outlook.it',
    'debug' => true  // Imposta a false in produzione
];
?>