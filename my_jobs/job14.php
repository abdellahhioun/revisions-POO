<?php

interface StockableInterface {
    public function addStocks(int $stock): self;
    public function removeStocks(int $stock): self;
}

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

    abstract public function findOneById(int $id, PDO $pdo);
    abstract public function findAll(PDO $pdo): array;
    abstract public function create(PDO $pdo);
    abstract public function update(PDO $pdo);
}

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

    public function findOneById(int $id, PDO $pdo) {
        try {
            $stmt = $pdo->prepare('SELECT * FROM product WHERE id = :id AND type = "clothing"');
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $productData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($productData) {
                return new Clothing(
                    (int)$productData['id'],
                    $productData['name'],
                    json_decode($productData['photos'], true),
                    (int)$productData['price'],
                    $productData['description'],
                    (int)$productData['quantity'],
                    (int)$productData['category_id'],
                    new DateTime($productData['createdAt']),
                    new DateTime($productData['updatedAt']),
                    $productData['size'],
                    $productData['color'],
                    $productData['type'],
                    (int)$productData['material_fee']
                );
            } else {
                return null; // Return null if no product found
            }
        } catch (PDOException $e) {
            echo 'Database error: ' . $e->getMessage();
            return null;
        }
    }

    public function findAll(PDO $pdo): array {
        try {
            $stmt = $pdo->query('SELECT * FROM product WHERE type = "clothing"');
            $products = [];
            while ($productData = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $products[] = new Clothing(
                    (int)$productData['id'],
                    $productData['name'],
                    json_decode($productData['photos'], true),
                    (int)$productData['price'],
                    $productData['description'],
                    (int)$productData['quantity'],
                    (int)$productData['category_id'],
                    new DateTime($productData['createdAt']),
                    new DateTime($productData['updatedAt']),
                    $productData['size'],
                    $productData['color'],
                    $productData['type'],
                    (int)$productData['material_fee']
                );
            }
            return $products;
        } catch (PDOException $e) {
            echo 'Database error: ' . $e->getMessage();
            return [];
        }
    }

    public function create(PDO $pdo) {
        try {
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

            $this->id = (int)$pdo->lastInsertId();
            return $this;
        } catch (PDOException $e) {
            echo 'Database error: ' . $e->getMessage();
            return false;
        }
    }

    public function update(PDO $pdo) {
        try {
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
        } catch (PDOException $e) {
            echo 'Database error: ' . $e->getMessage();
        }
    }
}

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
        parent::__construct($id, $name, $photos, $price, $description, $quantity, $category_id, $created
