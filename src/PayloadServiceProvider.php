<?php

namespace PsCs\UniversalModule\Psr7Middlewares\Middleware;


use Interop\Container\ServiceProviderInterface;
use Psr\Container\ContainerInterface;
use Psr7Middlewares\Middleware\Payload;
use TheCodingMachine\Funky\Annotations\Factory;
use TheCodingMachine\Funky\Annotations\Tag;
use TheCodingMachine\Funky\ServiceProvider;
use TheCodingMachine\MiddlewareListServiceProvider;
use TheCodingMachine\MiddlewareOrder;

class PayloadServiceProvider implements ServiceProviderInterface {
    /**
     * Returns a list of all container entries registered by this service provider.
     *
     * - the key is the entry name
     * - the value is a callable that will return the entry, aka the **factory**
     *
     * Factories have the following signature:
     *        function(\Psr\Container\ContainerInterface $container)
     *
     * @return callable[]
     */
    public function getFactories()
    {
        return [
            Payload::class => [ self::class, 'createPayload' ],
        ];
    }

    public static function createPayload(ContainerInterface $container) : Payload
    {
        return new Payload();
    }

    public static function updatePriorityQueue(ContainerInterface $container, $extendedService = null): \SplPriorityQueue
    {
        if ($extendedService) {
            $extendedService->insert($container->get(Payload::class), MiddlewareOrder::UTILITY_EARLY);
            return $extendedService;
        } else {
            throw new \InvalidArgumentException("Could not find declaration for service '".MiddlewareListServiceProvider::MIDDLEWARES_QUEUE."'.");
        }
    }

    /**
     * Returns a list of all container entries extended by this service provider.
     *
     * - the key is the entry name
     * - the value is a callable that will return the modified entry
     *
     * Callables have the following signature:
     *        function(Psr\Container\ContainerInterface $container, $previous)
     *     or function(Psr\Container\ContainerInterface $container, $previous = null)
     *
     * About factories parameters:
     *
     * - the container (instance of `Psr\Container\ContainerInterface`)
     * - the entry to be extended. If the entry to be extended does not exist and the parameter is nullable, `null` will be passed.
     *
     * @return callable[]
     */
    public function getExtensions()
    {
        return [
            MiddlewareListServiceProvider::MIDDLEWARES_QUEUE => [self::class, 'updatePriorityQueue']
        ];
    }
}
