<?php

class LogController
{
    private const MAX_FILE_SIZE = 2 * 1024 * 1024;

    private LogAnalysis $logModel;

    public function __construct()
    {
        $this->logModel = new LogAnalysis();
    }

    public function index(?array $analysis = null): void
    {
        $title = 'Analisis de logs';
        $history = $this->logModel->latestByUser(currentUser()['id']);
        require __DIR__ . '/../views/logs/index.php';
    }

    public function uploadAndAnalyze(): void
    {
        if (!isset($_FILES['log_file']) || $_FILES['log_file']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'No se pudo subir el archivo.'];
            redirect('logs');
        }

        $file = $_FILES['log_file'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, ['log', 'txt'], true) || $file['size'] > self::MAX_FILE_SIZE) {
            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => 'Solo se permiten archivos .log o .txt de hasta 2 MB.',
            ];
            redirect('logs');
        }

        $safeName = date('YmdHis') . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
        $destination = __DIR__ . '/../uploads/logs/' . $safeName;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Error al guardar el archivo en el servidor.'];
            redirect('logs');
        }

        $analysis = $this->analyzeLogFile($destination);
        $this->logModel->save(currentUser()['id'], $safeName, $analysis);
        $this->index($analysis);
    }

    private function analyzeLogFile(string $path): array
    {
        $handle = fopen($path, 'rb');

        if ($handle === false) {
            return [
                'totalLines' => 0,
                'suspiciousLines' => 0,
                'riskLevel' => 'bajo',
                'threats' => ['lectura_archivo' => 1],
                'examples' => [[
                    'line' => '-',
                    'content' => 'No se pudo leer el archivo subido.',
                    'threats' => ['lectura_archivo'],
                ]],
                'ipCounter' => [],
            ];
        }

        $totalLines = 0;
        $suspiciousLines = 0;
        $threats = [];
        $examples = [];
        $ipCounter = [];

        $patterns = [
            'codigo_peligroso' => '/\b(eval|exec|system|shell_exec|passthru|cmd|powershell)\b/i',
            'descarga_remota' => '/\b(wget|curl|Invoke-WebRequest|bitsadmin)\b/i',
            'login_fallido' => '/(failed password|login failed|authentication failure|invalid user|401 unauthorized)/i',
        ];

        // Leer linea a linea evita cargar archivos grandes completos en memoria.
        while (($line = fgets($handle)) !== false) {
            $totalLines++;
            $lineThreats = [];

            foreach ($patterns as $type => $pattern) {
                if (preg_match($pattern, $line)) {
                    $lineThreats[] = $type;
                    $threats[$type] = ($threats[$type] ?? 0) + 1;
                }
            }

            if (preg_match_all('/\b(?:[A-Za-z0-9+\/]{24,}={0,2})\b/', $line, $matches)) {
                foreach ($matches[0] as $candidate) {
                    if ($this->looksLikeBase64($candidate)) {
                        $lineThreats[] = 'base64';
                        $threats['base64'] = ($threats['base64'] ?? 0) + 1;
                        break;
                    }
                }
            }

            if (preg_match_all('/\b(?:\d{1,3}\.){3}\d{1,3}\b/', $line, $ips)) {
                foreach ($ips[0] as $ip) {
                    if (filter_var($ip, FILTER_VALIDATE_IP)) {
                        $ipCounter[$ip] = ($ipCounter[$ip] ?? 0) + 1;
                    }
                }
            }

            if ($lineThreats !== []) {
                $suspiciousLines++;
                if (count($examples) < 8) {
                    $examples[] = [
                        'line' => $totalLines,
                        'content' => trim($line),
                        'threats' => array_values(array_unique($lineThreats)),
                    ];
                }
            }
        }

        fclose($handle);

        foreach ($ipCounter as $ip => $count) {
            if ($count >= 10) {
                $threats['ip_repetida'] = ($threats['ip_repetida'] ?? 0) + 1;
                if (count($examples) < 8) {
                    $examples[] = [
                        'line' => '-',
                        'content' => "La IP {$ip} aparece {$count} veces.",
                        'threats' => ['ip_repetida'],
                    ];
                }
            }
        }

        $riskLevel = $this->calculateRisk($totalLines, $suspiciousLines, $threats);

        return [
            'totalLines' => $totalLines,
            'suspiciousLines' => $suspiciousLines,
            'riskLevel' => $riskLevel,
            'threats' => $threats,
            'examples' => $examples,
            'ipCounter' => $ipCounter,
        ];
    }

    private function looksLikeBase64(string $value): bool
    {
        if (strlen($value) % 4 !== 0) {
            return false;
        }

        $decoded = base64_decode($value, true);

        return $decoded !== false && preg_match('/[ -~]{8,}/', $decoded) === 1;
    }

    private function calculateRisk(int $totalLines, int $suspiciousLines, array $threats): string
    {
        $ratio = $totalLines > 0 ? $suspiciousLines / $totalLines : 0;
        $score = $suspiciousLines * 5 + count($threats) * 10 + (int) ($ratio * 100);

        return match (true) {
            $score >= 70 => 'alto',
            $score >= 30 => 'medio',
            default => 'bajo',
        };
    }
}
