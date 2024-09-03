<?php

abstract class AbstractProduct {
    protected int $id;
    protected string $name;
    protected array $photos;
    protected int $price;
    protected string $description;
    protected int $quantity;
    protected int $category_id;
    protected DateTime $createdAt;
    protected DateTime $updatedAt;

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

    // Abstract methods that must be implemented in subclasses
    abstract public static function findOneById(PDO $pdo, int $id);
    abstract public static function findAll(PDO $pdo);
    abstract public function create(PDO $pdo);
    abstract public function update(PDO $pdo);

    // Additional common methods
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

    protected function updateTimestamp(): void {
        $this->updatedAt = new DateTime();
    }
}

class Clothing extends AbstractProduct {
    private string $size;
    private string $color;
    private string $type;
    private int $material_fee;

    public function __construct(
        int $id = 0,
        string $name = '',
        array $photos = [],
        int $price = 0,
        string $description = '',
        int $quantity = 0,
        int $category_id = 0,
        DateTime $createdAt = null,
        DateTime $updatedAt = null,
        string $size = '',
        string $color = '',
        string $type = '',
        int $material_fee = 0
    ) {
        parent::__construct($id, $name, $photos, $price, $description, $quantity, $category_id, $createdAt, $updatedAt);
        $this->size = $size;
        $this->color = $color;
        $this->type = $type;
        $this->material_fee = $material_fee;
    }

    public function getSize(): string {
        return $this->size;
    }

    public function setSize(string $size): void {
        $this->size = $size;
    }

    public function getColor(): string {
        return $this->color;
    }

    public function setColor(string $color): void {
        $this->color = $color;
    }

    public function getType(): string {
        return $this->type;
    }

    public function setType(string $type): void {
        $this->type = $type;
    }

    public function getMaterialFee(): int {
        return $this->material_fee;
    }

    public function setMaterialFee(int $material_fee): void {
        $this->material_fee = $material_fee;
    }

    // Implement abstract methods from AbstractProduct
    public static function findOneById(PDO $pdo, int $id) {
        $stmt = $pdo->prepare('
            SELECT p.*, c.size, c.color, c.type, c.material_fee 
            FROM product p 
            JOIN clothing c ON p.id = c.product_id 
            WHERE p.id = :id
        ');
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
                new DateTime($productData['updatedAt']),
                $productData['size'],
                $productData['color'],
                $productData['type'],
                $productData['material_fee']
            );
        } else {
            return false;
        }
    }

    public static function findAll(PDO $pdo) {
        $stmt = $pdo->query('
            SELECT p.*, c.size, c.color, c.type, c.material_fee 
            FROM product p 
            JOIN clothing c ON p.id = c.product_id
        ');
        $productsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $clothingProducts = [];
        foreach ($productsData as $productData) {
            $clothingProducts[] = new self(
                $productData['id'],
                $productData['name'],
                json_decode($productData['photos'], true),
                $productData['price'],
                $productData['description'],
                $productData['quantity'],
                $productData['category_id'],
                new DateTime($productData['createdAt']),
                new DateTime($productData['updatedAt']),
                $productData['size'],
                $productData['color'],
                $productData['type'],
                $productData['material_fee']
            );
        }

        return $clothingProducts;
    }

    public function create(PDO $pdo) {
        $stmt = $pdo->prepare('
            INSERT INTO product (name, photos, price, description, quantity, category_id, createdAt, updatedAt)
            VALUES (:name, :photos, :price, :description, :quantity, :category_id, :createdAt, :updatedAt)
        ');
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':photos', json_encode($this->photos));
        $stmt->bindParam(':price', $this->price, PDO::PARAM_INT);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':quantity', $this->quantity, PDO::PARAM_INT);
        $stmt->bindParam(':category_id', $this->category_id, PDO::PARAM_INT);
        $stmt->bindParam(':createdAt', $this->createdAt->format('Y-m-d H:i:s'));
        $stmt->bindParam(':updatedAt', $this->updatedAt->format('Y-m-d H:i:s'));

        if ($stmt->execute()) {
            $this->id = $pdo->lastInsertId();
            $stmt = $pdo->prepare('
                INSERT INTO clothing (product_id, size, color, type, material_fee)
                VALUES (:product_id, :size, :color, :type, :material_fee)
            ');
            $stmt->bindParam(':product_id', $this->id, PDO::PARAM_INT);
            $stmt->bindParam(':size', $this->size);
            $stmt->bindParam(':color', $this->color);
            $stmt->bindParam(':type', $this->type);
            $stmt->bindParam(':material_fee', $this->material_fee, PDO::PARAM_INT);
            $stmt->execute();

            return $this;
        } else {
            return false;
        }
    }

    public function update(PDO $pdo) {
        $stmt = $pdo->prepare('
            UPDATE product
            SET name = :name, photos = :photos, price = :price, description = :description, quantity = :quantity, 
                category_id = :category_id, updatedAt = :updatedAt
            WHERE id = :id
        ');
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':photos', json_encode($this->photos));
        $stmt->bindParam(':price', $this->price, PDO::PARAM_INT);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':quantity', $this->quantity, PDO::PARAM_INT);
        $stmt->bindParam(':category_id', $this->category_id, PDO::PARAM_INT);
        $stmt->bindParam(':updatedAt', $this->updatedAt->format('Y-m-d H:i:s'));
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

        $result = $stmt->execute();
        if ($result) {
            $stmt = $pdo->prepare('
                UPDATE clothing
                SET size = :size, color = :color, type = :type, material_fee = :material_fee
                WHERE product_id = :product_id
            ');
            $stmt->bindParam(':product_id', $this->id, PDO::PARAM_INT);
            $stmt->bindParam(':size', $this->size);
            $stmt->bindParam(':color', $this->color);
            $stmt->bindParam(':type', $this->type);
            $stmt->bindParam(':material_fee', $this->material_fee, PDO::PARAM_INT);
            $stmt->execute();
        }
        return $result;
    }
}

class Electronic extends AbstractProduct {
    private string $brand;
    private int $warranty_fee;

    public function __construct(
        int $id = 0,
        string $name = '',
        array $photos = [],
        int $price = 0,
        string $description = '',
        int $quantity = 0,
        int $category_id = 0,
        DateTime $createdAt = null,
        DateTime $updatedAt = null,
        string $brand = '',
        int $warranty_fee = 0
    ) {
        parent::__construct($id, $name, $photos, $price, $description, $quantity, $category_id, $createdAt, $updatedAt);
        $this->brand = $brand;
        $this->warranty_fee = $warranty_fee;
    }

    public function getBrand(): string {
        return $this->brand;
    }

    public function setBrand(string $brand): void {
        $this->brand = $brand;
    }

    public function getWarrantyFee(): int {
        return $this->warranty_fee;
    }

    public function setWarrantyFee(int $warranty_fee): void {
        $this->warranty_fee = $warranty_fee;
    }

    // Implement abstract methods from AbstractProduct
    public static function findOneById(PDO $pdo, int $id) {
        $stmt = $pdo->prepare('
            SELECT p.*, e.brand, e.warranty_fee 
            FROM product p 
            JOIN electronic e ON p.id = e.product_id 
            WHERE p.id = :id
        ');
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
                new DateTime($productData['updatedAt']),
                $productData['brand'],
                $productData['warranty_fee']
            );
        } else {
            return false;
        }
    }

    public static function findAll(PDO $pdo) {
        $stmt = $pdo->query('
            SELECT p.*, e.brand, e.warranty_fee 
            FROM product p 
            JOIN electronic e ON p.id = e.product_id
        ');
        $productsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $electronicProducts = [];
        foreach ($productsData as $productData) {
            $electronicProducts[] = new self(
                $productData['id'],
                $productData['name'],
                json_decode($productData['photos'], true),
                $productData['price'],
                $productData['description'],
                $productData['quantity'],
                $productData['category_id'],
                new DateTime($productData['createdAt']),
                new DateTime($productData['updatedAt']),
                $productData['brand'],
                $productData['warranty_fee']
            );
        }

        return $electronicProducts;
    }

    public function create(PDO $pdo) {
        $stmt = $pdo->prepare('
            INSERT INTO product (name, photos, price, description, quantity, category_id, createdAt, updatedAt)
            VALUES (:name, :photos, :price, :description, :quantity, :category_id, :createdAt, :updatedAt)
        ');
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':photos', json_encode($this->photos));
        $stmt->bindParam(':price', $this->price, PDO::PARAM_INT);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':quantity', $this->quantity, PDO::PARAM_INT);
        $stmt->bindParam(':category_id', $this->category_id, PDO::PARAM_INT);
        $stmt->bindParam(':createdAt', $this->createdAt->format('Y-m-d H:i:s'));
        $stmt->bindParam(':updatedAt', $this->updatedAt->format('Y-m-d H:i:s'));

        if ($stmt->execute()) {
            $this->id = $pdo->lastInsertId();
            $stmt = $pdo->prepare('
                INSERT INTO electronic (product_id, brand, warranty_fee)
                VALUES (:product_id, :brand, :warranty_fee)
            ');
            $stmt->bindParam(':product_id', $this->id, PDO::PARAM_INT);
            $stmt->bindParam(':brand', $this->brand);
            $stmt->bindParam(':warranty_fee', $this->warranty_fee, PDO::PARAM_INT);
            $stmt->execute();

            return $this;
        } else {
            return false;
        }
    }

    public function update(PDO $pdo) {
        $stmt = $pdo->prepare('
            UPDATE product
            SET name = :name, photos = :photos, price = :price, description = :description, quantity = :quantity, 
                category_id = :category_id, updatedAt = :updatedAt
            WHERE id = :id
        ');
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':photos', json_encode($this->photos));
        $stmt->bindParam(':price', $this->price, PDO::PARAM_INT);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':quantity', $this->quantity, PDO::PARAM_INT);
        $stmt->bindParam(':category_id', $this->category_id, PDO::PARAM_INT);
        $stmt->bindParam(':updatedAt', $this->updatedAt->format('Y-m-d H:i:s'));
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

        $result = $stmt->execute();
        if ($result) {
            $stmt = $pdo->prepare('
                UPDATE electronic
                SET brand = :brand, warranty_fee = :warranty_fee
                WHERE product_id = :product_id
            ');
            $stmt->bindParam(':product_id', $this->id, PDO::PARAM_INT);
            $stmt->bindParam(':brand', $this->brand);
            $stmt->bindParam(':warranty_fee', $this->warranty_fee, PDO::PARAM_INT);
            $stmt->execute();
        }
        return $result;
    }
}
?>
