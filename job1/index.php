<?php

// Inclure la classe Product
require_once 'job1.php';

// Instanciation d'un objet Product
$product = new Product(
    1, 
    'T-shirt', 
    ['https://picsum.photos/200/300'], 
    1000, 
    'T-shirt for men', 
    10, 
    2,
    new DateTime('2023-09-01 12:00:00'), 
    new DateTime('2023-09-01 12:00:00')
);

// Utilisation des getters pour récupérer les propriétés
var_dump($product->getId());
var_dump($product->getName());
var_dump($product->getPhotos());
var_dump($product->getPrice());
var_dump($product->getDescription());
var_dump($product->getQuantity());
var_dump($product->getCreatedAt());
var_dump($product->getUpdatedAt());

// Modification de certaines propriétés via les setters
$product->setName("Updated T-shirt");
$product->setPrice(1200);
$product->setQuantity(15);

// Vérification des nouvelles valeurs après modification
var_dump($product->getName());
var_dump($product->getPrice());
var_dump($product->getQuantity());
var_dump($product->getUpdatedAt()); // Vérifiez que la date de mise à jour a changé

?>
