<?php

declare(strict_types=1);

namespace App;

final class View
{
    /**
     * Render views/{$template}.php inside views/layout.php (or another layout).
     * Templates are plain PHP; all dynamic values must go through e().
     */
    public static function render(string $template, array $data = [], ?string $layout = 'layout'): string
    {
        $content = self::partial($template, $data);
        if ($layout === null) {
            return $content;
        }
        return self::partial($layout, $data + ['content' => $content]);
    }

    public static function partial(string $template, array $data = []): string
    {
        $file = dirname(__DIR__) . '/views/' . $template . '.php';
        if (!preg_match('#^[a-z0-9/_-]+$#', $template) || !is_file($file)) {
            throw new \RuntimeException("View not found: {$template}");
        }
        extract($data, EXTR_SKIP);
        ob_start();
        try {
            require $file;
        } catch (\Throwable $ex) {
            ob_end_clean();
            throw $ex;
        }
        return (string) ob_get_clean();
    }
}
