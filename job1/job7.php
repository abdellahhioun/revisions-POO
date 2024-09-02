<?php

class Product {
    private int $id;
    private string $name;
    private array $photos;
    private int $price;
    private string $description;
    private int $quantity;
    private int $category_id;
    private DateTime $createdAt;
    private DateTime $updatedAt;

    // Constructeur avec paramètres optionnels
    public function __construct(
        int $id = 0,
        string $name = '',
        array $photos = [],
        int $price = 0,
        string $description = '',
        int $quantity = 0,
        int $category_id = 0,
        DateTime $createdAt = null,
        DateTime $updatedAt = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->photos = $photos;
        $this->price = $price;
        $this->description = $description;
        $this->quantity = $quantity;
        $this->category_id = $category_id;
        $this->createdAt = $createdAt ?? new DateTime();
        $this->updatedAt = $updatedAt ?? new DateTime();
    }

    // Getters and Setters (omitted for brevity)

    // Méthode pour récupérer la catégorie associée
    public function getCategory(PDO $pdo) {
        $stmt = $pdo->prepare('SELECT * FROM category WHERE id = :id');
        $stmt->bindParam(':id', $this->category_id, PDO::PARAM_INT);
        $stmt->execute();
        $categoryData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($categoryData) {
            return new Category(
                $categoryData['id'],
                $categoryData['name'],
                $categoryData['description'],
                new DateTime($categoryData['createdAt']),
                new DateTime($categoryData['updatedAt'])
            );
        } else {
            return null;  // Ou vous pouvez lever une exception si la catégorie n'est pas trouvée
        }
    }

    // Méthode privée pour mettre à jour la date de mise à jour
    private function updateTimestamp() {
        $this->updatedAt = new DateTime();
    }

    // Static method to find a product by its ID
    public static function findOneById(int $id, PDO $pdo) {
        $stmt = $pdo->prepare('SELECT * FROM product WHERE id = :id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $productData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($productData) {
            return new self(
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
        } else {
            return false;  // Return false if the product is not found
        }
    }
}

?>
