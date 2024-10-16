<?php
// CatalogoProdotti.php
require_once 'config.php';

class CatalogoProdotti {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function aggiungiProdotto($nome, $descrizione, $prezzo, $immagine) {
        $sql = "INSERT INTO prodotti (nome, descrizione, prezzo, immagine) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssds", $nome, $descrizione, $prezzo, $immagine);
        return $stmt->execute();
    }

    public function modificaProdotto($id, $nome, $descrizione, $prezzo, $immagine) {
        $sql = "UPDATE prodotti SET nome = ?, descrizione = ?, prezzo = ?, immagine = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssdsi", $nome, $descrizione, $prezzo, $immagine, $id);
        return $stmt->execute();
    }

    public function getProdotti() {
        $sql = "SELECT * FROM prodotti";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getProdotto($id) {
        $sql = "SELECT * FROM prodotti WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
    return $result->fetch_assoc();
    }
    
    public function eliminaProdotto($id) {
        $sql = "DELETE FROM prodotti WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}

?>