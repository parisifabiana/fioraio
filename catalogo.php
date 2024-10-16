<?php
require_once 'config.php';
require_once 'CatalogoProdotti.php';

$catalogo = new CatalogoProdotti($conn);
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
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php require 'footer.html'; ?>
</body>
</html>