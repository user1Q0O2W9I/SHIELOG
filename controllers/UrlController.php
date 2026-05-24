<?php

class UrlController
{
    private UrlAnalysis $urlModel;

    public function __construct()
    {
        $this->urlModel = new UrlAnalysis();
    }

    public function index(?array $analysis = null): void
    {
        $title = 'Analisis de URLs';
        $history = $this->urlModel->latest();
        require __DIR__ . '/../views/urls/index.php';
    }

    public function analyze(): void
    {
        $url = trim($_POST['url'] ?? '');

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Introduce una URL valida con http:// o https://.'];
            redirect('urls');
        }

        $analysis = $this->analyzeUrl($url);
        $this->urlModel->save($url, $analysis['score'], $analysis['result'], $analysis['rules']);
        $this->index($analysis);
    }

    private function analyzeUrl(string $url): array
    {
        $score = 0;
        $rules = [];
        $parsed = parse_url($url);
        $host = $parsed['host'] ?? '';
        $scheme = strtolower($parsed['scheme'] ?? '');
        $fullText = strtolower($url);

        // Cada regla activada suma puntos. La puntuacion final determina la categoria.
        $this->addRule($rules, $score, $scheme !== 'https', 20, 'La URL no usa HTTPS.');
        $this->addRule($rules, $score, strlen($url) > 90, 15, 'La URL es demasiado larga.');
        $this->addRule($rules, $score, filter_var($host, FILTER_VALIDATE_IP) !== false, 25, 'Usa una IP en lugar de un dominio.');

        $suspiciousWords = ['login', 'verify', 'account', 'secure', 'update', 'bank', 'password', 'confirm'];
        foreach ($suspiciousWords as $word) {
            if (str_contains($fullText, $word)) {
                $this->addRule($rules, $score, true, 10, "Contiene la palabra sospechosa: {$word}.");
            }
        }

        $this->addRule($rules, $score, str_contains($url, '@'), 20, 'Contiene el caracter @, usado para ocultar destinos.');
        $this->addRule($rules, $score, substr_count($host, '-') >= 2, 10, 'El dominio contiene varios guiones.');
        $this->addRule($rules, $score, substr_count($host, '.') >= 3, 15, 'Tiene demasiados subdominios.');

        $result = match (true) {
            $score >= 60 => 'Peligroso',
            $score >= 30 => 'Sospechoso',
            default => 'Seguro',
        };

        return [
            'url' => $url,
            'score' => $score,
            'result' => $result,
            'rules' => $rules,
        ];
    }

    private function addRule(array &$rules, int &$score, bool $condition, int $points, string $message): void
    {
        if ($condition) {
            $score += $points;
            $rules[] = [
                'points' => $points,
                'message' => $message,
            ];
        }
    }
}
