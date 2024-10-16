<?php
session_start();

// Reindirizza alla dashboard se l'utente è già loggato
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Gestione del form di login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once 'config.php';
    
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // In un'applicazione reale, dovresti usare password_hash() e password_verify()
    // Questo è solo un esempio semplificato
    $query = "SELECT id, username FROM users WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: index.php");
        exit();
    } else {
        $error = "Username o password non validi.";
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - L'arte dei fiori</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <?php require 'header.html'; ?>

    <div class="container">
        <h1>Login</h1>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="button">Accedi</button>
        </form>
        <p><a href="index.html">Torna alla Home</a></p>
    </div>

    <?php require 'footer.html'; ?>

</body>
</html>