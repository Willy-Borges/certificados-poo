<?php
// core/Model.php

class Model
{
    protected $db;

    public function __construct()
    {
        require_once __DIR__ . '/Database.php';
        $this->db = Database::conectar();
    }
}
