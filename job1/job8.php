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

    // Getters
    public function getId(): int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getPhotos(): array {
        return $this->photos;
    }

    public function getPrice(): int {
        return $this->price;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getQuantity(): int {
        return $this->quantity;
    }

    public function getCategoryId(): int {
        return $this->category_id;
    }

    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime {
        return $this->updatedAt;
    }

    // Setters
    public function setId(int $id): void {
        $this->id = $id;
        $this->updateTimestamp();
    }

    public function setName(string $name): void {
        $this->name = $name;
        $this->updateTimestamp();
    }

    public function setPhotos(array $photos): void {
        $this->photos = $photos;
        $this->updateTimestamp();
    }

    public function setPrice(int $price): void {
        $this->price = $price;
        $this->updateTimestamp();
    }

    public function setDescription(string $description): void {
        $this->description = $description;
        $this->updateTimestamp();
    }

    public function setQuantity(int $quantity): void {
        $this->quantity = $quantity;
        $this->updateTimestamp();    
    }

    public function setCategoryId(int $category_id): void {
        $this->category_id = $category_id;
        $this->updateTimestamp();
    }

    public function setCreatedAt(DateTime $createdAt): void {
        $this->createdAt = $createdAt;
        $this->updateTimestamp();
    }

    public function setUpdatedAt(DateTime $updatedAt): void {
        $this->updatedAt = $updatedAt;
        $this->updateTimestamp();
    }

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

    // Méthode pour récupérer un produit par ID
    public static function findOneById(PDO $pdo, int $id) {
        $stmt = $pdo->prepare('SELECT * FROM product WHERE id = :id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $productData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($productData) {
            return new Product(
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
            return false;  // Indicateur que le produit n'a pas été trouvé
        }
    }

    // Méthode pour récupérer tous les produits
    public static function findAll(PDO $pdo): array {
        $stmt = $pdo->query('SELECT * FROM product');
        $productsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $products = [];
        foreach ($productsData as $productData) {
            $products[] = new Product(
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
        }

        return $products;
    }

    // Méthode privée pour mettre à jour la date de mise à jour
    private function updateTimestamp() {
        $this->updatedAt = new DateTime();
    }
}

?>
