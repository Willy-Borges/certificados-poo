<?php
// core/Database.php

class Database 
{
    private static $instancia = null;
    private $conexao;

    private function __construct()
    {
        $config = require __DIR__ . '/../config/database.php';

        try {
            $this->conexao = new PDO(
                "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}",
                $config['usuario'],
                $config['senha']
            );

            $this->conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conexao->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            die("Erro ao conectar ao banco: " . $e->getMessage());
        }
    }

    public static function getConexao()
    {
        if (self::$instancia === null) {
            self::$instancia = new Database();
        }

        return self::$instancia->conexao;
    }

    public static function conectar()
    {
        return self::getConexao();
    }
}
