<?php

namespace Oro\Bundle\ActionBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages ActionBundle service configuration
 */
class OroActionExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('assemblers.yml');
        $loader->load('block_types.yml');
        $loader->load('conditions.yml');
        $loader->load('configuration.yml');
        $loader->load('form_types.yml');
        $loader->load('actions.yml');
        $loader->load('services.yml');
        $loader->load('duplicator.yml');
        $loader->load('commands.yml');
        $loader->load('controllers.yml');

        if ('test' === $container->getParameter('kernel.environment')) {
            $loader->load('services_test.yml');
        }
    }

    public function prepend(ContainerBuilder $container)
    {
        if ('test' === $container->getParameter('kernel.environment')) {
            $path = dirname(__DIR__) . '/Tests/Functional/Stub/views';
            $container->prependExtensionConfig('twig', ['paths' => [$path => 'OroActionStub']]);
        }
    }
}
