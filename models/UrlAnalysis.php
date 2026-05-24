<?php

class UrlAnalysis
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function save(string $url, int $score, string $result, array $details): bool
    {
        $sql = 'INSERT INTO analisis_url (url, puntuacion, resultado, detalles)
                VALUES (:url, :puntuacion, :resultado, :detalles)';
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'url' => $url,
            'puntuacion' => $score,
            'resultado' => $result,
            'detalles' => json_encode($details, JSON_UNESCAPED_UNICODE),
        ]);
    }

    public function latest(int $limit = 10): array
    {
        $stmt = $this->db->prepare('SELECT * FROM analisis_url ORDER BY fecha DESC LIMIT :limit');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}

