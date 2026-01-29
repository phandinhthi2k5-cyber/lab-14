<?php
require_once __DIR__ . '/../libs/helper.php';

class Product {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function countAll() {
        return $this->db->query("SELECT COUNT(*) FROM products")->fetchColumn();
    }

    public function getPage($limit, $offset) {
        $stmt = $this->db->prepare("SELECT * FROM products ORDER BY id DESC LIMIT :lim OFFSET :off");
        $stmt->bindValue(':lim', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function add($name, $image) {
        $stmt = $this->db->prepare("INSERT INTO products (name, image) VALUES (?, ?)");
        return $stmt->execute([$name, $image]);
    }

    public function update($id, $name, $image) {
        $stmt = $this->db->prepare("UPDATE products SET name = ?, image = ? WHERE id = ?");
        return $stmt->execute([$name, $image, $id]);
    }

    public function delete($id) {
        $item = $this->getById($id);
        if ($item && $item['image'] !== 'default.png') {
            $path = __DIR__ . "/../public/uploads/" . $item['image'];
            if (file_exists($path)) unlink($path);
        }
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>