<?php

declare(strict_types=1);

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import('../config/{packages}/*.yaml');
        $container->import('../config/{packages}/' . $this->environment . '/*.yaml');

        $container->import('../config/services.yaml');
        $container->import('../config/services/controllers.xml');
        $container->import('../config/services/event_source.xml');
        $container->import('../config/services/message_handlers.xml');
        $container->import('../config/services/repositories.xml');
        $container->import('../config/services/services.xml');
        $container->import('../config/services/console.yaml');
        $container->import('../config/services/consumers.yaml');
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import('../config/{routes}/' . $this->environment . '/*.yaml');
        $routes->import('../config/{routes}/*.yaml');
        $routes->import('../config/routes.xml');
    }
}
