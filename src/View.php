<?php

declare(strict_types=1);

namespace App;

use App\Http\Response;

final class View
{
    public function __construct(
        private string $viewPath,
        private string $appName = 'Stok Envanter Takip'
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function render(
        string $template,
        array $data = [],
        string $title = 'Stok Envanter Takip',
        int $statusCode = 200
    ): Response {
        $templateFile = $this->viewPath . '/' . $template . '.php';
        if (!is_file($templateFile)) {
            throw new \RuntimeException('Görünüm şablonu bulunamadı: ' . $template);
        }

        $data['flashes'] = $data['flashes'] ?? pull_flashes();
        $data['appName'] = $data['appName'] ?? $this->appName;

        ob_start();
        extract($data, EXTR_SKIP);
        require $templateFile;
        $content = (string) ob_get_clean();

        ob_start();
        extract([
            'content' => $content,
            'title' => $title,
            'page' => $data['page'] ?? '',
            'appName' => $data['appName'],
            'flashes' => $data['flashes'],
        ], EXTR_SKIP);
        require $this->viewPath . '/layout.php';

        return Response::html((string) ob_get_clean(), $statusCode);
    }
}
