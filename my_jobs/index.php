<?php

$productId = 7;
$stmt = $pdo->prepare('SELECT * FROM product WHERE id = :id');
$stmt->bindParam(':id', $productId, PDO::PARAM_INT);
$stmt->execute();

$productData = $stmt->fetch(PDO::FETCH_ASSOC);

if ($productData) {
    // Si le produit existe, nous allons créer une instance de la classe Product
    $product = new Product(
        $productData['id'],
        $productData['name'],
        json_decode($productData['photos'], true), // Les photos sont stockées sous forme de JSON
        $productData['price'],
        $productData['description'],
        $productData['quantity'],
        $productData['category_id'],
        new DateTime($productData['createdAt']),
        new DateTime($productData['updatedAt'])
    );

    // Utilisation de var_dump pour vérifier l'objet hydraté
    var_dump($product);
} else {
    echo "Produit avec l'ID 7 non trouvé.";
}
?>
