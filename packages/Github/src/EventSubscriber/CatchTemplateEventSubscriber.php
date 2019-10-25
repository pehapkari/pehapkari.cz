<?php

declare(strict_types=1);

namespace Pehapkari\Github\EventSubscriber;

use Nette\Utils\FileSystem;
use Pehapkari\Github\PhpParser\NodeVisitor\DetectRenderArgumentNodeVisitor;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use ReflectionClass;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class CatchTemplateEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var DetectRenderArgumentNodeVisitor
     */
    private $detectRenderArgumentNodeVisitor;

    public function __construct(DetectRenderArgumentNodeVisitor $detectRenderArgumentNodeVisitor)
    {
        $this->detectRenderArgumentNodeVisitor = $detectRenderArgumentNodeVisitor;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::CONTROLLER => 'controller'];
    }

    public function controller(ControllerEvent $controllerEvent): void
    {
        $controllerClass = $this->resolveControllerClass($controllerEvent);

        $controllerReflection = new ReflectionClass($controllerClass);

        /** @var string $controllerFileName */
        $controllerFileName = $controllerReflection->getFileName();

        $phpParser = $this->getPhpParser();
        $controllerNodes = $phpParser->parse(FileSystem::read($controllerFileName));

        // extract $this->render('$value')
        $nodeTraverser = new NodeTraverser();

        $controllerMethod = $this->resolveControllerMethod($controllerEvent);
        $this->detectRenderArgumentNodeVisitor->setMethodName($controllerMethod);

        $nodeTraverser->addVisitor($this->detectRenderArgumentNodeVisitor);
        $nodeTraverser->traverse($controllerNodes);
    }

    private function resolveControllerClass(ControllerEvent $controllerEvent): string
    {
        /** @var string[]|object $controllerCallable */
        $controllerCallable = $controllerEvent->getController();

        if (is_array($controllerCallable)) {
            return get_class($controllerCallable[0]);
        }

        return get_class($controllerCallable);
    }

    private function getPhpParser(): Parser
    {
        return (new ParserFactory())->create(ParserFactory::ONLY_PHP7);
    }

    private function resolveControllerMethod(ControllerEvent $controllerEvent): string
    {
        /** @var string[]|object $controllerCallable */
        $controllerCallable = $controllerEvent->getController();

        if (is_array($controllerCallable)) {
            return $controllerCallable[1];
        }

        // single action controller
        return '__invoke';
    }
}
