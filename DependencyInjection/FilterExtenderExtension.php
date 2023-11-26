<?php

declare(strict_types=1);

namespace EvgenijVY\FilterExtender\DependencyInjection;

use EvgenijVY\FilterExtender\Service\FiltersResourceMetadataCollectionFactoryDecorator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class FilterExtenderExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        (new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../config')
        ))->load('config.yaml');
    }
}