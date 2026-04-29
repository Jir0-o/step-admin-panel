<?php

namespace App\Services;

class StoreTokenSyncService
{
    public function __construct(private readonly StoreTokenService $tokenService)
    {
    }

    public function syncForUser(int $userId, ?string $fallbackEmail = null, ?string $fallbackPassword = null, bool $forceRefresh = false): array
    {
        return $this->tokenService->syncForUser($userId, $fallbackEmail, $fallbackPassword, $forceRefresh);
    }
}
