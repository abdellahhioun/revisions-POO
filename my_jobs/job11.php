<?php

class Product {
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

    // Method to get the associated category
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
            return null;  // Or you could throw an exception if the category is not found
        }
    }

    // Method to find a product by ID
    public static function findOneById(PDO $pdo, int $id) {
        $stmt = $pdo->prepare('SELECT * FROM product WHERE id = :id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $productData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($productData) {
            // Check if it's a Clothing or Electronic product
            $stmtClothing = $pdo->prepare('SELECT * FROM clothing WHERE product_id = :id');
            $stmtClothing->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtClothing->execute();
            $clothingData = $stmtClothing->fetch(PDO::FETCH_ASSOC);

            if ($clothingData) {
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
                    $clothingData['size'],
                    $clothingData['color'],
                    $clothingData['type'],
                    $clothingData['material_fee']
                );
            }

            $stmtElectronic = $pdo->prepare('SELECT * FROM electronic WHERE product_id = :id');
            $stmtElectronic->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtElectronic->execute();
            $electronicData = $stmtElectronic->fetch(PDO::FETCH_ASSOC);

            if ($electronicData) {
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
                    $electronicData['brand'],
                    $electronicData['warranty_fee']
                );
            }
        }

        return false;  // Indicate that the product was not found
    }

    // Method to find all products
    public static function findAll(PDO $pdo) {
        $stmt = $pdo->query('SELECT * FROM product');
        $products = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $product = new Product(
                $row['id'],
                $row['name'],
                json_decode($row['photos'], true),
                $row['price'],
                $row['description'],
                $row['quantity'],
                $row['category_id'],
                new DateTime($row['createdAt']),
                new DateTime($row['updatedAt'])
            );

            $stmtClothing = $pdo->prepare('SELECT * FROM clothing WHERE product_id = :id');
            $stmtClothing->bindParam(':id', $row['id'], PDO::PARAM_INT);
            $stmtClothing->execute();
            $clothingData = $stmtClothing->fetch(PDO::FETCH_ASSOC);

            if ($clothingData) {
                $product = new Clothing(
                    $product->getId(),
                    $product->getName(),
                    $product->getPhotos(),
                    $product->getPrice(),
                    $product->getDescription(),
                    $product->getQuantity(),
                    $product->getCategoryId(),
                    $product->getCreatedAt(),
                    $product->getUpdatedAt(),
                    $clothingData['size'],
                    $clothingData['color'],
                    $clothingData['type'],
                    $clothingData['material_fee']
                );
            } else {
                $stmtElectronic = $pdo->prepare('SELECT * FROM electronic WHERE product_id = :id');
                $stmtElectronic->bindParam(':id', $row['id'], PDO::PARAM_INT);
                $stmtElectronic->execute();
                $electronicData = $stmtElectronic->fetch(PDO::FETCH_ASSOC);

                if ($electronicData) {
                    $product = new Electronic(
                        $product->getId(),
                        $product->getName(),
                        $product->getPhotos(),
                        $product->getPrice(),
                        $product->getDescription(),
                        $product->getQuantity(),
                        $product->getCategoryId(),
                        $product->getCreatedAt(),
                        $product->getUpdatedAt(),
                        $electronicData['brand'],
                        $electronicData['warranty_fee']
                    );
                }
            }

            $products[] = $product;
        }

        return $products;
    }

    // Method to create a new product
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

            // Insert into specific tables
            if ($this instanceof Clothing) {
                $stmtClothing = $pdo->prepare('
                    INSERT INTO clothing (product_id, size, color, type, material_fee)
                    VALUES (:product_id, :size, :color, :type, :material_fee)
                ');
                $stmtClothing->bindParam(':product_id', $this->id, PDO::PARAM_INT);
                $stmtClothing->bindParam(':size', $this->size);
                $stmtClothing->bindParam(':color', $this->color);
                $stmtClothing->bindParam(':type', $this->type);
                $stmtClothing->bindParam(':material_fee', $this->material_fee, PDO::PARAM_INT);
                $stmtClothing->execute();
            } elseif ($this instanceof Electronic) {
                $stmtElectronic = $pdo->prepare('
                    INSERT INTO electronic (product_id, brand, warranty_fee)
                    VALUES (:product_id, :brand, :warranty_fee)
                ');
                $stmtElectronic->bindParam(':product_id', $this->id, PDO::PARAM_INT);
                $stmtElectronic->bindParam(':brand', $this->brand);
                $stmtElectronic->bindParam(':warranty_fee', $this->warranty_fee, PDO::PARAM_INT);
                $stmtElectronic->execute();
            }

            return $this;
        } else {
            return false;
        }
    }

    // Method to update an existing product
    public function update(PDO $pdo) {
        $stmt = $pdo->prepare('
            UPDATE product
            SET name = :name, photos = :photos, price = :price, description = :description,
                quantity = :quantity, category_id = :category_id, updatedAt = :updatedAt
            WHERE id = :id
        ');
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':photos', json_encode($this->photos));
        $stmt->bindParam(':price', $this->price, PDO::PARAM_INT);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':quantity', $this->quantity, PDO::PARAM_INT);
        $stmt->bindParam(':category_id', $this->category_id, PDO::PARAM_INT);
        $stmt->bindParam(':updatedAt', $this->updatedAt->format('Y-m-d H:i:s'));

        if ($stmt->execute()) {
            // Update specific tables
            if ($this instanceof Clothing) {
                $stmtClothing = $pdo->prepare('
                    UPDATE clothing
                    SET size = :size, color = :color, type = :type, material_fee = :material_fee
                    WHERE product_id = :product_id
                ');
                $stmtClothing->bindParam(':product_id', $this->id, PDO::PARAM_INT);
                $stmtClothing->bindParam(':size', $this->size);
                $stmtClothing->bindParam(':color', $this->color);
                $stmtClothing->bindParam(':type', $this->type);
                $stmtClothing->bindParam(':material_fee', $this->material_fee, PDO::PARAM_INT);
                $stmtClothing->execute();
            } elseif ($this instanceof Electronic) {
                $stmtElectronic = $pdo->prepare('
                    UPDATE electronic
                    SET brand = :brand, warranty_fee = :warranty_fee
                    WHERE product_id = :product_id
                ');
                $stmtElectronic->bindParam(':product_id', $this->id, PDO::PARAM_INT);
                $stmtElectronic->bindParam(':brand', $this->brand);
                $stmtElectronic->bindParam(':warranty_fee', $this->warranty_fee, PDO::PARAM_INT);
                $stmtElectronic->execute();
            }

            return $this;
        } else {
            return false;
        }
    }

    private function updateTimestamp() {
        $this->updatedAt = new DateTime();
    }
}


class Clothing extends Product {
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

    // Getters
    public function getSize(): string {
        return $this->size;
    }

    public function getColor(): string {
        return $this->color;
    }

    public function getType(): string {
        return $this->type;
    }

    public function getMaterialFee(): int {
        return $this->material_fee;
    }

    // Setters
    public function setSize(string $size): void {
        $this->size = $size;
    }

    public function setColor(string $color): void {
        $this->color = $color;
    }

    public function setType(string $type): void {
        $this->type = $type;
    }

    public function setMaterialFee(int $material_fee): void {
        $this->material_fee = $material_fee;
    }

    // Override create method
    public function create(PDO $pdo) {
        $result = parent::create($pdo);
        if ($result) {
            // Insert into clothing-specific table
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
        }
        return $result;
    }

    // Override update method
    public function update(PDO $pdo) {
        $result = parent::update($pdo);
        if ($result) {
            // Update clothing-specific table
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


class Electronic extends Product {
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

    // Getters
    public function getBrand(): string {
        return $this->brand;
    }

    public function getWarrantyFee(): int {
        return $this->warranty_fee;
    }

    // Setters
    public function setBrand(string $brand): void {
        $this->brand = $brand;
    }

    public function setWarrantyFee(int $warranty_fee): void {
        $this->warranty_fee = $warranty_fee;
    }

    // Override create method
    public function create(PDO $pdo) {
        $result = parent::create($pdo);
        if ($result) {
            // Insert into electronic-specific table
            $stmt = $pdo->prepare('
                INSERT INTO electronic (product_id, brand, warranty_fee)
                VALUES (:product_id, :brand, :warranty_fee)
            ');
            $stmt->bindParam(':product_id', $this->id, PDO::PARAM_INT);
            $stmt->bindParam(':brand', $this->brand);
            $stmt->bindParam(':warranty_fee', $this->warranty_fee, PDO::PARAM_INT);
            $stmt->execute();
        }
        return $result;
    }

    // Override update method
    public function update(PDO $pdo) {
        $result = parent::update($pdo);
        if ($result) {
            // Update electronic-specific table
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

