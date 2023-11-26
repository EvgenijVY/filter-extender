<?php

declare(strict_types=1);

namespace EvgenijVY\FilterExtender\Service;

use ApiPlatform\Metadata\Exception\ResourceClassNotFoundException;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\ResourceMetadataCollection;

class FiltersResourceMetadataCollectionFactoryDecorator implements ResourceMetadataCollectionFactoryInterface
{
    public function __construct(
        private readonly ?ResourceMetadataCollectionFactoryInterface $decorated = null
    )
    {
    }

    public function create(string $resourceClass): ResourceMetadataCollection
    {
        try {
            $reflectionClass = new \ReflectionClass($resourceClass);
        } catch (\ReflectionException) {
            throw new ResourceClassNotFoundException(sprintf('Resource "%s" not found.', $resourceClass));
        }

        $resourceMetadataCollection = is_null($this->decorated) ?
            new ResourceMetadataCollection($resourceClass) :
            $this->decorated->create($resourceClass);

        foreach ($resourceMetadataCollection as $i => $resource) {
            $needUpdate = false;
            foreach ($operations = $resource->getOperations() ?? [] as $operationName => $operation) {
                if (!is_null($operation->getFilters())) {
                    $operations->add($operationName, $operation->withFilters(array_unique($operation->getFilters())));
                    $needUpdate = true;
                }
            }
            if ($needUpdate) {
                $resourceMetadataCollection[$i] = $resource->withOperations($operations);
            }

            $needUpdate = false;
            foreach ($graphQlOperations = $resource->getGraphQlOperations() ?? [] as $operationName => $operation) {
                if (!is_null($operation->getFilters())) {
                    $graphQlOperations[$operationName] = $operation->withFilters(
                        array_unique($operation->getFilters())
                    );
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