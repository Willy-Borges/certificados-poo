<?php
// modelos/Usuario.php
require_once __DIR__ . '/../core/Model.php';

class Usuario extends Model
{
    protected $tabela = 'usuarios';

    // lista todos (não deve ser usado agora, mas deixamos completo)
    public function todos()
    {
        $sql = "SELECT * FROM usuarios ORDER BY nome ASC";
        return $this->db->query($sql)->fetchAll();
    }

    // buscar por id (vamos usar este no sistema)
    public function buscarPorId(int $id)
    {
        $sql = "SELECT * FROM usuarios WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }
}
