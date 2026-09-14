<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;

final class RoleMiddleware
{
    public function __construct(private readonly array $roles) {}

    public function handle(): void
    {
        if (!Auth::check() || !in_array(Auth::role(), $this->roles, true)) {
            http_response_code(403);
            require APP_PATH . '/views/errors/403.php';
            exit;
        }
    }
}
