<?php

declare(strict_types=1);

namespace EvgenijVY\FilterExtender\Service;

use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\ResourceMetadataCollection;

class FiltersResourceMetadataCollectionFactoryDecoratorInjector implements ResourceMetadataCollectionFactoryInterface
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
            $needUpdate = false;
            foreach ($operations = $resource->getOperations() ?? [] as $operationName => $operation) {
                $filters = $this->operationService->gatOperationFilters($operationName);
                if (!is_null($filters)) {
                    $operations->add($operationName, $operation->withFilters(array_unique($filters)));
                    $needUpdate = true;
                }
            }
            if ($needUpdate) {
                $resourceMetadataCollection[$i] = $resource->withOperations($operations);
            }

            $needUpdate = false;
            foreach ($graphQlOperations = $resource->getGraphQlOperations() ?? [] as $operationName => $operation) {
                $filters = $this->operationService->gatOperationFilters($operationName);
                if (!is_null($filters)) {
                    $graphQlOperations[$operationName] = $operation->withFilters(array_unique($filters));
                    $needUpdate = true;
                }
            }
            if ($needUpdate) {
                $resourceMetadataCollection[$i] = $resource->withGraphQlOperations($graphQlOperations);
            }
        }

        return $resourceMetadataCollection;
    }
}