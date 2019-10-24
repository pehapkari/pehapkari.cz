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
        /** @var string[] $controllerCallable */
        $controllerCallable = $controllerEvent->getController();

        $controllerClass = $controllerCallable[0];
        $controllerReflection = new ReflectionClass($controllerClass);

        /** @var string $controllerFileName */
        $controllerFileName = $controllerReflection->getFileName();

        $phpParser = $this->getPhpParser();
        $controllerNodes = $phpParser->parse(FileSystem::read($controllerFileName));

        // extract $this->render('$value')
        /** @var string $controllerMethod */
        $controllerMethod = $controllerCallable[1];

        $nodeTraverser = new NodeTraverser();

        $this->detectRenderArgumentNodeVisitor->setMethodName($controllerMethod);

        $nodeTraverser->addVisitor($this->detectRenderArgumentNodeVisitor);
        $nodeTraverser->traverse($controllerNodes);
    }

    private function getPhpParser(): Parser
    {
        return (new ParserFactory())->create(ParserFactory::ONLY_PHP7);
    }
}
