<?php
include 'db_conn.php';
$productId = 7;
$stmt = $pdo->prepare('SELECT * FROM product WHERE id = :id');
$stmt->bindParam(':id', $productId, PDO::PARAM_INT);
$stmt->execute();

$productData = $stmt->fetch(PDO::FETCH_ASSOC);

if ($productData) {

    $product = new Product(
        $productData['id'],
        $productData['name'],
        json_decode($productData['photos'], true),
        $productData['price'],
        $productData['description'],
        $productData['quantity'],
        $productData['category_id'],
        new DateTime($productData['createdAt']),
        new DateTime($productData['updatedAt'])
    );

    $category = $product->getCategory($pdo);
    if ($category) {
        // Affichage des informations de la catégorie
        var_dump($category);
    } else {
        echo "Catégorie non trouvée.";
    }
} else {
    echo "Produit avec l'ID 7 non trouvé.";
}






?>