<?php
class Product{
    private int $id;
    private string $name;
    private array $photos;
    private int $price;
    private string $description;
    private int $quantity;
    private DateTime $createdAt;
    private DateTime $updatedAt;

    public function __construct($id, $name, $photos, $price, $description, $quantity, $createdAt, $updatedAt){
        $this->id = $id;
        $this->name = $name;
        $this->photos = $photos;
        $this->price = $price;
        $this->description = $description;
        $this->quantity = $quantity;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(){
        return $this->id;
    }

    public function getName(){  
        return $this->name;
    }

    public function getPhotos(){
        return $this->photos;
    }

    public function getPrice(){
        return $this->price;
    }

    public function getDescription(){
        return $this->description;
    }

    public function getQuantity(){  
        return $this->quantity;
    }

    public function getCreatedAt(){
        return $this->createdAt;
    }

    public function getUpdatedAt(){
        return $this->updatedAt;
    }
    





}
  




?>