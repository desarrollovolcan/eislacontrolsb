<?php 
require_once __DIR__ . '/../../assest/config/BDCONFIG.php';
class DocumentoModel {
    private $db;

    public function __construct() {
        $dbConfig = new BDCONFIG();  // Conectar a la base de datos
        $this->db = $dbConfig->conectar();
    }

    public function getDocumentosByProductor($productorId) {
        $query = "SELECT * FROM tb_documento WHERE productor_documento = :productorId AND estado_documento = 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['productorId' => $productorId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getDocumentosByProductorEspecie($productorId, $especieId) {
        $query = "SELECT * FROM tb_documento WHERE productor_documento = :productorId AND estado_documento = 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['productorId' => $productorId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getUltimosDocumentosByProductores(array $productores, $especieId = null, $limit = 5) {
        if (empty($productores)) {
            return [];
        }

        $limit = (int) $limit;
        $placeholders = implode(',', array_fill(0, count($productores), '?'));
        $params = $productores;

        $query = "SELECT * FROM tb_documento WHERE estado_documento = 1 AND productor_documento IN ($placeholders)";

        if ($especieId) {
            $query .= " AND especie_documento = ?";
            $params[] = $especieId;
        }

        $query .= " ORDER BY create_documento DESC LIMIT $limit";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getDocumentosPorVencerByProductores(array $productores, $especieId = null, $limit = 5, $diasAviso = 45) {
        if (empty($productores)) {
            return [];
        }

        $limit = (int) $limit;
        $diasAviso = (int) $diasAviso;
        $placeholders = implode(',', array_fill(0, count($productores), '?'));
        $params = $productores;

        $query = "SELECT * FROM tb_documento WHERE estado_documento = 1 AND productor_documento IN ($placeholders)";
        $query .= " AND DATE(vigencia_documento) >= CURDATE()";

        if ($especieId) {
            $query .= " AND especie_documento = ?";
            $params[] = $especieId;
        }

        $query .= " AND DATE(vigencia_documento) <= DATE_ADD(CURDATE(), INTERVAL $diasAviso DAY)";
        $query .= " ORDER BY vigencia_documento ASC, create_documento DESC LIMIT $limit";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getDocumentoById($id) {
        $query = "SELECT * FROM tb_documento WHERE id_documento = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function deleteDocumento($id) {
        $query = "UPDATE tb_documento SET estado_documento = 3 WHERE id_documento = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);
    }
}
