<?php
class Category {
    private int $id;
    private string $name;
    private string $description;
    private DateTime $createdAt;
    private DateTime $updatedAt;

    public function __construct($id, $name, $description, $createdAt, $updatedAt) {   
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getName() {  
        return $this->name;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getCreatedAt() {
        return $this->createdAt;
    }

    public function getUpdatedAt() {  
        return $this->updatedAt;
    }

    // Setters
    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function setDescription(string $description): void {
        $this->description = $description;
    }

    public function setCreatedAt(DateTime $createdAt): void {
        $this->createdAt = $createdAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): void {
        $this->updatedAt = $updatedAt;
    }

    // Method to retrieve all products in the category
    public function getProducts(PDO $pdo): array {
        $stmt = $pdo->prepare('SELECT * FROM product WHERE category_id = :category_id');
        $stmt->bindParam(':category_id', $this->id, PDO::PARAM_INT);
        $stmt->execute();

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
}
?>
