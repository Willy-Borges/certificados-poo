<?php
// modelos/Curso.php
require_once __DIR__ . '/../core/Model.php';

class Curso extends Model
{
    protected $tabela = 'cursos';

    public function todos()
    {
        $sql = "SELECT * FROM cursos ORDER BY nome ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function buscarPorId(int $id)
    {
        $sql = "SELECT * FROM cursos WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }
}
