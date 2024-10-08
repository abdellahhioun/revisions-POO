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
        $this->createdAt = $createdAt ?? new DateTime(); // Par défaut, la date actuelle
        $this->updatedAt = $updatedAt ?? new DateTime(); // Par défaut, la date actuelle
    }

    // Getters et Setters

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getPhotos() {
        return $this->photos;
    }

    public function getPrice() {
        return $this->price;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getQuantity() {
        return $this->quantity;
    }

    public function getCategoryId() {
        return $this->category_id;
    }

    public function getCreatedAt() {
        return $this->createdAt;
    }

    public function getUpdatedAt() {
        return $this->updatedAt;
    }

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

    private function updateTimestamp() {
        $this->updatedAt = new DateTime();
    }
}

// Exemples d'utilisation
$product1 = new Product(1, 'T-shirt', ['https://picsum.photos/200/300'], 1000, 'T-shirt for men', 10, 2, new DateTime('2023-09-01 12:00:00'), new DateTime('2023-09-01 12:00:00'));
$product2 = new Product();
