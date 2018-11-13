<?php declare(strict_types=1);

namespace OpenTraining;

use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerTrait;

/**
 * Just to make ControllerTrait work
 */
trait AutowiredControllerTrait
{
    use ControllerTrait;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @required
     */
    public function autowireContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }
}
