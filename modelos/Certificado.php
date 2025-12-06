<?php
// modelos/Certificado.php
require_once __DIR__ . '/../core/Model.php';

class Certificado extends Model
{
    protected $tabela = 'certificados_emitidos';

    public function todos()
    {
        $sql = "SELECT ce.*, c.nome AS nome,
                       c.data_inicio AS curso_inicio,
                       c.data_fim AS curso_fim
                FROM certificados_emitidos ce
                LEFT JOIN cursos c ON c.id = ce.curso_id
                ORDER BY ce.id DESC";

        $rows = $this->db->query($sql)->fetchAll();

        foreach ($rows as &$r) {
            $r['carga'] = $r['carga_horaria'] ?? '';
        }

        return $rows;
    }

    /**
     * Salva vários certificados (um por cursoId).
     * @param array $cursoIds
     * @param string|null $cargaHoraria formato "HH:MM:SS"
     * @return bool
     */
    public function salvarVarios(array $cursoIds, $cargaHoraria = null)
    {
        // usuário fixo (teste)
        $usuarioId = 1;

        // Preparar insert. Colunas devem existir no BD: curso_id, carga_horaria, data_emissao, usuario_id, serial (opcional)
        $sql = "INSERT INTO certificados_emitidos 
                (curso_id, carga_horaria, data_emissao, usuario_id) 
                VALUES (:curso_id, :carga_horaria, :data_emissao, :usuario_id)";

        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare($sql);
            $data = date('Y-m-d H:i:s');

            foreach ($cursoIds as $cursoId) {
                $stmt->execute([
                    ':curso_id' => $cursoId,
                    ':carga_horaria' => $cargaHoraria,
                    ':data_emissao' => $data,
                    ':usuario_id' => $usuarioId
                ]);
            }

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            // log no servidor (como fallback, usa error_log)
            error_log('Erro salvarVarios certificados: ' . $e->getMessage());
            return false;
        }
    }

    public function limpar()
    {
        $this->db->exec("TRUNCATE TABLE certificados_emitidos");
        return true;
    }

    public function buscarPorIdEmitido(int $id)
    {
        $sql = "SELECT ce.*, c.nome AS curso_nome,
                       c.data_inicio AS curso_inicio,
                       c.data_fim AS curso_fim
                FROM certificados_emitidos ce
                LEFT JOIN cursos c ON c.id = ce.curso_id
                WHERE ce.id = :id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }
}
