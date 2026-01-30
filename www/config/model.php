<?php
require_once 'conexion.php';

class Model {
    protected $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    protected function prepare($sql) {
        return $this->db->prepare($sql);
    }

    protected function query($sql) {
        return $this->db->query($sql);
    }
}
