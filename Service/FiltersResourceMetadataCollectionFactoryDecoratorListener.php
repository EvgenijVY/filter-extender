<?php

declare(strict_types=1);

namespace EvgenijVY\FilterExtender\Service;

use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\ResourceMetadataCollection;

class FiltersResourceMetadataCollectionFactoryDecoratorListener implements ResourceMetadataCollectionFactoryInterface
{
    public function __construct(
        private OperationService $operationService,
        private ?ResourceMetadataCollectionFactoryInterface $decorated = null
    )
    {
    }

    public function create(string $resourceClass): ResourceMetadataCollection
    {
        $resourceMetadataCollection = is_null($this->decorated) ?
            new ResourceMetadataCollection($resourceClass) : $this->decorated->create($resourceClass);

        foreach ($resourceMetadataCollection as $i => $resource) {
            foreach ($resource->getOperations() ?? [] as $operationName => $operation) {
                $this->operationService->addOperationFilters($operationName, $operation->getFilters());
            }

            foreach ($resource->getGraphQlOperations() ?? [] as $operationName => $operation) {
                $this->operationService->addOperationFilters($operationName, $operation->getFilters());
            }
        }

        return $resourceMetadataCollection;
    }
}