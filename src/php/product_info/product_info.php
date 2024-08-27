<?php
class Product {
    private $dbh;
    private $product_id;
    private $product_data;

    public function __construct($dbh, $product_id) {
        $this->dbh = $dbh;
        $this->product_id = $product_id;
        $this->fetchProductData();
    }

    private function fetchProductData() {
        $stmt = $this->dbh->prepare('SELECT * FROM Product WHERE id = ?');
        $stmt->execute(array($this->product_id));
        $this->product_data = $stmt->fetch();
    }

    public function isValid() {
        return ($this->product_data !== false);
    }
    
    public function getID() {
        return $this->product_data['id'];
    }
    
    public function getName() {
        return $this->product_data['name'];
    }

    public function getPrice() {
        return $this->product_data['price'];
    }

    public function getDescription() {
        return $this->product_data['description'];
    }

    public function getImage() {
        $productPics = $this->getProductPics();
        if (empty($productPics)){
            return '../../images/product/default.png';
        }
        else{
            return $productPics[0];
        }
    }

    public function getQuantity() {
        return $this->product_data['quantity'];
    }

    public function getPublishDate() {
        return $this->product_data['publishDate'];
    }

    public function getProductPic() {
        $productPics = $this->getProductPics();
        if (empty($productPics)){
            return '../../images/product/default.png';
        }
        else{
            return $productPics[0];
        }
    }

    public function getProductPics() {
        $productDir = '../../images/product/' . $this->product_data['id'] . '/';
        $productPics = [];
        
        if (is_dir($productDir)) {
            $files = scandir($productDir);
            $files = array_diff($files, array('.', '..'));

            foreach ($files as $file) {
                $productPics[] = $productDir . $file;
            }
        }
        return $productPics;
    }
    

    public function getAddress() {
        return $this->product_data['address'];
    }

    public function getZipcode() {
        return $this->product_data['zipcode'];
    }

    public function getCategories() {
        $categories = array();
    
        /// Categories from Product_Furniture
        $stmt = $this->dbh->prepare('SELECT Furniture.name FROM Furniture INNER JOIN Product_Furniture ON Furniture.id = Product_Furniture.category WHERE Product_Furniture.product = ?');
        $stmt->execute(array($this->product_id));
        $furniture_categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $categories = array_merge($categories, $furniture_categories);
    
        /// Categories from Product_Decoration
        $stmt = $this->dbh->prepare('SELECT Decoration.name FROM Decoration INNER JOIN Product_Decoration ON Decoration.id = Product_Decoration.category WHERE Product_Decoration.product = ?');
        $stmt->execute(array($this->product_id));
        $decoration_categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $categories = array_merge($categories, $decoration_categories);
    
        /// Categories from Product_Size
        $stmt = $this->dbh->prepare('SELECT Size.name FROM Size INNER JOIN Product_Size ON Size.id = Product_Size.category WHERE Product_Size.product = ?');
        $stmt->execute(array($this->product_id));
        $size_categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $categories = array_merge($categories, $size_categories);
    
        /// Categories from Product_Condition
        $stmt = $this->dbh->prepare('SELECT Condition.name FROM Condition INNER JOIN Product_Condition ON Condition.id = Product_Condition.category WHERE Product_Condition.product = ?');
        $stmt->execute(array($this->product_id));
        $condition_categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $categories = array_merge($categories, $condition_categories);
    
        /// Remove duplicates (it shouldn't happen, but admins may add categories with the same name)
        $categories = array_unique($categories);
        return $categories;
    }
}
?>