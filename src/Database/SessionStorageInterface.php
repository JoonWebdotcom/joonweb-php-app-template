<?php
namespace App\Database;

interface SessionStorageInterface {
    public function saveToken(string $siteDomain, array $tokenData): bool;
    public function getToken(string $siteDomain): ?array;
    public function deleteToken(string $siteDomain): bool;
}
