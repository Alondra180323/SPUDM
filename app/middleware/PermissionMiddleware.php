<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;

final class PermissionMiddleware
{
    public function __construct(private readonly string $permission) {}

    public function handle(): void
    {
        if (!Auth::check() || !Auth::can($this->permission)) {
            http_response_code(403);
            require APP_PATH . '/views/errors/403.php';
            exit;
        }
    }
}
