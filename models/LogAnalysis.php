<?php

class LogAnalysis
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function save(int $userId, string $fileName, array $analysis): bool
    {
        $sql = 'INSERT INTO analisis_logs
                (usuario_id, archivo, lineas_totales, lineas_sospechosas, nivel_riesgo, amenazas, ejemplos)
                VALUES
                (:usuario_id, :archivo, :lineas_totales, :lineas_sospechosas, :nivel_riesgo, :amenazas, :ejemplos)';
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'usuario_id' => $userId,
            'archivo' => $fileName,
            'lineas_totales' => $analysis['totalLines'],
            'lineas_sospechosas' => $analysis['suspiciousLines'],
            'nivel_riesgo' => $analysis['riskLevel'],
            'amenazas' => json_encode($analysis['threats'], JSON_UNESCAPED_UNICODE),
            'ejemplos' => json_encode($analysis['examples'], JSON_UNESCAPED_UNICODE),
        ]);
    }

    public function latestByUser(int $userId, int $limit = 10): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM analisis_logs WHERE usuario_id = :usuario_id ORDER BY fecha DESC LIMIT :limit'
        );
        $stmt->bindValue(':usuario_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}

