<?php
// index.php
session_start();

// Verifica se l'utente è loggato
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config.php';
require_once 'CatalogoProdotti.php';

$catalogo = new CatalogoProdotti($conn);

// Funzione per ridimensionare l'immagine
function resizeImage($file, $max_width = 800, $max_height = 600) {
    list($width, $height, $type) = getimagesize($file);
    $new_width = $width;
    $new_height = $height;

    if ($width > $max_width || $height > $max_height) {
        $ratio = min($max_width / $width, $max_height / $height);
        $new_width = $width * $ratio;
        $new_height = $height * $ratio;
    }

    $new_image = imagecreatetruecolor($new_width, $new_height);

    switch ($type) {
        case IMAGETYPE_JPEG:
            $source = imagecreatefromjpeg($file);
            break;
        case IMAGETYPE_PNG:
            $source = imagecreatefrompng($file);
            imagealphablending($new_image, false);
            imagesavealpha($new_image, true);
            break;
        case IMAGETYPE_GIF:
            $source = imagecreatefromgif($file);
            break;
        default:
            return false;
    }

    imagecopyresampled($new_image, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

    switch ($type) {
        case IMAGETYPE_JPEG:
            imagejpeg($new_image, $file, 90);
            break;
        case IMAGETYPE_PNG:
            imagepng($new_image, $file, 9);
            break;
        case IMAGETYPE_GIF:
            imagegif($new_image, $file);
            break;
    }

    imagedestroy($new_image);
    imagedestroy($source);

    return true;
}

// Funzione per gestire l'upload delle immagini
function handleImageUpload($file) {
    $target_dir = "immagini/";
    
    if (!file_exists($target_dir) && !is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $target_file = $target_dir . uniqid() . '_' . basename($file["name"]);
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

    if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
        echo "Solo JPG, JPEG, PNG & GIF sono permessi.";
        return false;
    }

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        // Ridimensiona l'immagine dopo il caricamento
        if (resizeImage($target_file)) {
            return basename($target_file);
        } else {
            echo "Errore nel ridimensionamento dell'immagine.";
            unlink($target_file);
            return false;
        }
    } else {
        echo "C'è stato un errore nel caricamento del file.";
        return false;
    }
}

// Gestione delle azioni
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['azione'])) {
        switch ($_POST['azione']) {
            case 'aggiungi':
                $immagine = '';
                if (isset($_FILES['immagine']) && $_FILES['immagine']['error'] == 0) {
                    $immagine = handleImageUpload($_FILES['immagine']);
                    if ($immagine === false) {
                        break;
                    }
                }
                if ($catalogo->aggiungiProdotto($_POST['nome'], $_POST['descrizione'], floatval($_POST['prezzo']), $immagine)) {
                    echo "<script>alert('Prodotto aggiunto con successo.');</script>";
                } else {
                    echo "<script>alert('Errore nell'aggiunta del prodotto.');</script>";
                }
                break;
            case 'modifica':
                $immagine = $_POST['immagine_attuale'];
                if (isset($_FILES['immagine']) && $_FILES['immagine']['error'] == 0) {
                    $nuova_immagine = handleImageUpload($_FILES['immagine']);
                    if ($nuova_immagine !== false) {
                        if ($immagine && file_exists("immagini/" . $immagine)) {
                            unlink("immagini/" . $immagine);
                        }
                        $immagine = $nuova_immagine;
                    }
                }
                if ($catalogo->modificaProdotto($_POST['id'], $_POST['nome'], $_POST['descrizione'], floatval($_POST['prezzo']), $immagine)) {
                    echo "<script>alert('Prodotto modificato con successo.');</script>";
                } else {
                    echo "<script>alert('Errore nella modifica del prodotto.');</script>";
                }
                break;
            case 'elimina':
                $prodotto = $catalogo->getProdotto($_POST['id']);
                if ($prodotto) {
                    if ($prodotto['immagine'] && file_exists("immagini/" . $prodotto['immagine'])) {
                        unlink("immagini/" . $prodotto['immagine']);
                    }
                    if ($catalogo->eliminaProdotto($_POST['id'])) {
                        echo "<script>alert('Prodotto eliminato con successo.');</script>";
                    } else {
                        echo "<script>alert('Errore durante l'eliminazione del prodotto.');</script>";
                    }
                } else {
                    echo "<script>alert('Prodotto non trovato.');</script>";
                }
                break;
        }
    }
}

$prodotti = $catalogo->getProdotti();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogo Prodotti - L'arte dei fiori</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <?php require 'header.html'; ?>

    <div class="container">
        <h1>Catalogo Prodotti - L'arte dei fiori</h1>

        <a href="catalogo.php" class="button">Visualizza Catalogo Prodotti</a>

        <h2>Aggiungi Prodotto</h2>
        
        <p>Benvenuto, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
        <a href="logout.php" class="button">Logout</a>

        <form method="post" enctype="multipart/form-data" class="add-product-form">
            <input type="hidden" name="azione" value="aggiungi">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required>
            <label for="descrizione">Descrizione:</label>
            <textarea id="descrizione" name="descrizione" required></textarea>
            <label for="prezzo">Prezzo:</label>
            <input type="number" id="prezzo" name="prezzo" step="0.01" required>
            <label for="immagine">Immagine:</label>
            <input type="file" id="immagine" name="immagine" accept="image/*">
            <input type="submit" value="Aggiungi Prodotto" class="button">
        </form>

        <h2>Catalogo Prodotti</h2>
        <div class="prodotti-grid">
            <?php foreach ($prodotti as $prodotto): ?>
            <div class="prodotto">
                <div class="prodotto-immagine">
                    <?php if ($prodotto['immagine']): ?>
                        <img src="immagini/<?php echo htmlspecialchars($prodotto['immagine']); ?>" alt="<?php echo htmlspecialchars($prodotto['nome']); ?>" class="product-image">
                    <?php else: ?>
                        <div class="no-image">Nessuna immagine</div>
                    <?php endif; ?>
                </div>
                <h3><?php echo htmlspecialchars($prodotto['nome']); ?></h3>
                <p class="descrizione"><?php echo htmlspecialchars($prodotto['descrizione']); ?></p>
                <p class="prezzo">€<?php echo number_format($prodotto['prezzo'], 2); ?></p>
                <form method="post" enctype="multipart/form-data" class="edit-form">
                    <input type="hidden" name="azione" value="modifica">
                    <input type="hidden" name="id" value="<?php echo $prodotto['id']; ?>">
                    <input type="text" name="nome" value="<?php echo htmlspecialchars($prodotto['nome']); ?>" required>
                    <textarea name="descrizione" required><?php echo htmlspecialchars($prodotto['descrizione']); ?></textarea>
                    <input type="number" name="prezzo" value="<?php echo $prodotto['prezzo']; ?>" step="0.01" required>
                    <input type="file" name="immagine" accept="image/*">
                    <input type="hidden" name="immagine_attuale" value="<?php echo htmlspecialchars($prodotto['immagine']); ?>">
                    <div class="button-group">
                        <input type="submit" value="Modifica" class="button">
                        <button type="button" class="button delete-button" onclick="eliminaProdotto(<?php echo $prodotto['id']; ?>)">Elimina</button>
                    </div>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
    function eliminaProdotto(id) {
        if (confirm('Sei sicuro di voler eliminare questo prodotto?')) {
            var form = document.createElement('form');
            form.method = 'post';
            form.innerHTML = '<input type="hidden" name="azione" value="elimina"><input type="hidden" name="id" value="' + id + '">';
            document.body.appendChild(form);
            form.submit();
        }
    }
    </script>

<?php require 'footer.html'; ?>

</body>
</html>