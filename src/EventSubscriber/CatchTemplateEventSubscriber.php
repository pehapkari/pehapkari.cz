<?php declare(strict_types=1);

namespace Pehapkari\EventSubscriber;

use Nette\Utils\FileSystem;
use Pehapkari\NodeVisitor\DetectRenderArgumentNodeVisitor;
use PhpParser\NodeTraverser;
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
        $controllerClass = get_class($controllerEvent->getController()[0]);
        $controllerReflection = new ReflectionClass($controllerClass);

        $controllerFileName = $controllerReflection->getFileName();

        $phpParser = (new ParserFactory())->create(ParserFactory::ONLY_PHP7);
        $controllerNodes = $phpParser->parse(FileSystem::read($controllerFileName));

        // extract $this->render('$value')
        /** @var string $controllerMethod */
        $controllerMethod = $controllerEvent->getController()[1];

        $nodeTraverser = new NodeTraverser();

        $this->detectRenderArgumentNodeVisitor->setMethodName($controllerMethod);

        $nodeTraverser->addVisitor($this->detectRenderArgumentNodeVisitor);
        $nodeTraverser->traverse($controllerNodes);
    }
}
