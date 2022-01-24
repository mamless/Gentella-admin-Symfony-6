<?php
namespace App\Factory;

use Symfony\Component\DependencyInjection\ContainerInterface;

class ServiceFactory
{

    /** @var ContainerInterface */
    private $container;


    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getService($alias)
    {
        return $this->container->get($alias);
    }

    /**
     * @param $alias
     * @return bool
     */
    public function hasService($alias)
    {
        return $this->container->has($alias);
    }
}