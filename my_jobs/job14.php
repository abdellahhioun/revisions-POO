<?php

// StockableInterface defines the contract for stock management.
interface StockableInterface {
    public function addStocks(int $stock): self;
    public function removeStocks(int $stock): self;
}

// AbstractProduct is an abstract class that represents a general product.
// It contains common properties and methods shared by all products.
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

    // AbstractProduct constructor with optional parameters.
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

    // Getters and setters for the properties.
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

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function setPhotos(array $photos): void {
        $this->photos = $photos;
    }

    public function setPrice(int $price): void {
        $this->price = $price;
    }

    public function setDescription(string $description): void {
        $this->description = $description;
    }

    public function setQuantity(int $quantity): void {
        $this->quantity = $quantity;
    }

    public function setCategoryId(int $category_id): void {
        $this->category_id = $category_id;
    }

    public function setCreatedAt(DateTime $createdAt): void {
        $this->createdAt = $createdAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): void {
        $this->updatedAt = $updatedAt;
    }

    // Abstract methods that subclasses must implement.
    abstract public function findOneById(int $id, PDO $pdo);
    abstract public function findAll(PDO $pdo): array;
    abstract public function create(PDO $pdo);
    abstract public function update(PDO $pdo);
}

// Clothing class represents clothing products and implements StockableInterface.
class Clothing extends AbstractProduct implements StockableInterface {
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

    // Implementing StockableInterface methods.
    public function addStocks(int $stock): self {
        $this->quantity += $stock;
        return $this;
    }

    public function removeStocks(int $stock): self {
        if ($this->quantity >= $stock) {
            $this->quantity -= $stock;
        } else {
            throw new Exception("Not enough stock to remove");
        }
        return $this;
    }

    // Implementing abstract methods.
    public function findOneById(int $id, PDO $pdo) {
        $stmt = $pdo->prepare('SELECT * FROM product WHERE id = :id AND type = "clothing"');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $productData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($productData) {
            return new Clothing(
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

    public function findAll(PDO $pdo): array {
        $stmt = $pdo->query('SELECT * FROM product WHERE type = "clothing"');
        $products = [];
        while ($productData = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $products[] = new Clothing(
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
        return $products;
    }

    public function create(PDO $pdo) {
        $stmt = $pdo->prepare('INSERT INTO product (name, photos, price, description, quantity, category_id, createdAt, updatedAt, size, color, type, material_fee) 
                               VALUES (:name, :photos, :price, :description, :quantity, :category_id, :createdAt, :updatedAt, :size, :color, :type, :material_fee)');
        $stmt->execute([
            ':name' => $this->name,
            ':photos' => json_encode($this->photos),
            ':price' => $this->price,
            ':description' => $this->description,
            ':quantity' => $this->quantity,
            ':category_id' => $this->category_id,
            ':createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            ':updatedAt' => $this->updatedAt->format('Y-m-d H:i:s'),
            ':size' => $this->size,
            ':color' => $this->color,
            ':type' => $this->type,
            ':material_fee' => $this->material_fee
        ]);

        $this->id = $pdo->lastInsertId();
        return $this;
    }

    public function update(PDO $pdo) {
        $stmt = $pdo->prepare('UPDATE product SET name = :name, photos = :photos, price = :price, description = :description, quantity = :quantity, category_id = :category_id, 
                               updatedAt = :updatedAt, size = :size, color = :color, type = :type, material_fee = :material_fee WHERE id = :id');
        $stmt->execute([
            ':name' => $this->name,
            ':photos' => json_encode($this->photos),
            ':price' => $this->price,
            ':description' => $this->description,
            ':quantity' => $this->quantity,
            ':category_id' => $this->category_id,
            ':updatedAt' => (new DateTime())->format('Y-m-d H:i:s'),
            ':size' => $this->size,
            ':color' => $this->color,
            ':type' => $this->type,
            ':material_fee' => $this->material_fee,
            ':id' => $this->id
        ]);
    }
}

// Electronic class represents electronic products and implements StockableInterface.
class Electronic extends AbstractProduct implements StockableInterface {
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

    // Implementing StockableInterface methods.
    public function addStocks(int $stock): self {
        $this->quantity += $stock;
        return $this;
    }

    public function removeStocks(int $stock): self {
        if ($this->quantity >= $stock) {
            $this->quantity -= $stock;
        } else {
            throw new Exception("Not enough stock to remove");
        }
        return $this;
    }

    // Implementing abstract methods.
    public function findOneById(int $id, PDO $pdo) {
        $stmt = $pdo->prepare('SELECT * FROM product WHERE id = :id AND type = "electronic"');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $productData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($productData) {
            return new Electronic(
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

    public function findAll(PDO $pdo): array {
        $stmt = $pdo->query('SELECT * FROM product WHERE type = "electronic"');
        $products = [];
        while ($productData = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $products[] = new Electronic(
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
        return $products;
    }

    public function create(PDO $pdo) {
        $stmt = $pdo->prepare('INSERT INTO product (name, photos, price, description, quantity, category_id, createdAt, updatedAt, brand, warranty_fee) 
                               VALUES (:name, :photos, :price, :description, :quantity, :category_id, :createdAt, :updatedAt, :brand, :warranty_fee)');
        $stmt->execute([
            ':name' => $this->name,
            ':photos' => json_encode($this->photos),
            ':price' => $this->price,
            ':description' => $this->description,
            ':quantity' => $this->quantity,
            ':category_id' => $this->category_id,
            ':createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            ':updatedAt' => $this->updatedAt->format('Y-m-d H:i:s'),
            ':brand' => $this->brand,
            ':warranty_fee' => $this->warranty_fee
        ]);

        $this->id = $pdo->lastInsertId();
        return $this;
    }

    public function update(PDO $pdo) {
        $stmt = $pdo->prepare('UPDATE product SET name = :name, photos = :photos, price = :price, description = :description, quantity = :quantity, category_id = :category_id, 
                               updatedAt = :updatedAt, brand = :brand, warranty_fee = :warranty_fee WHERE id = :id');
        $stmt->execute([
            ':name' => $this->name,
            ':photos' => json_encode($this->photos),
            ':price' => $this->price,
            ':description' => $this->description,
            ':quantity' => $this->quantity,
            ':category_id' => $this->category_id,
            ':updatedAt' => (new DateTime())->format('Y-m-d H:i:s'),
            ':brand' => $this->brand,
            ':warranty_fee' => $this->warranty_fee,
            ':id' => $this->id
        ]);
    }
}

?>
