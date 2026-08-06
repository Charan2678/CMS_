<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Controller
 *
 * Base Controller class for all CMS module controllers.
 * Provides view rendering, JSON response formatting, input sanitization, and flash messaging helpers.
 */
abstract class Controller
{
    /**
     * Render a view file with data.
     *
     * @param string $viewPath Path relative to app/modules/ (e.g. 'Authentication/views/login')
     * @param array $data Variables to extract into the view context
     * @param string|null $layout Optional layout file name (e.g. 'layout' or null for no layout)
     */
    protected function render(string $viewPath, array $data = [], ?string $layout = 'layout'): void
    {
        extract($data);

        $viewFile = APP_PATH . '/modules/' . ltrim($viewPath, '/') . '.php';

        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View file not found: {$viewFile}");
        }

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        if ($layout !== null) {
            $layoutFile = APP_PATH . '/modules/Master/views/' . $layout . '.php';
            if (file_exists($layoutFile)) {
                require $layoutFile;
                return;
            }
        }

        echo $content;
    }

    /**
     * Send a JSON response.
     */
    protected function json(mixed $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Redirect to a URI.
     */
    protected function redirect(string $url): never
    {
        redirect($url);
    }

    /**
     * Check if request method is POST.
     */
    protected function isPost(): bool
    {
        return is_post();
    }

    /**
     * Sanitize user string input.
     */
    protected function input(string $key, string $default = ''): string
    {
        return trim((string) ($_POST[$key] ?? $_GET[$key] ?? $default));
    }
}
