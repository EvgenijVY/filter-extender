<?php

declare(strict_types=1);

namespace EvgenijVY\FilterExtender\Service;

class OperationService
{
    private array $operationFilters = [];

    public function addOperationFilters(string $operationName, ?array $filters): void
    {
        $this->operationFilters[$operationName] = $filters;
    }

    public function gatOperationFilters(string $operationName): ?array
    {
        return $this->operationFilters[$operationName] ?? null;
    }
}