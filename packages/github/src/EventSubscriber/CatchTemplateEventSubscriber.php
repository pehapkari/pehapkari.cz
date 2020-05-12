<?php

declare(strict_types=1);

namespace Pehapkari\Github\EventSubscriber;

use Nette\Utils\FileSystem;
use Pehapkari\Github\Collector\ResolvedTemplateNameCollector;
use Pehapkari\Github\PhpParser\NodeVisitor\DetectRenderArgumentNodeVisitor;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Controller\TemplateController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class CatchTemplateEventSubscriber implements EventSubscriberInterface
{
    private DetectRenderArgumentNodeVisitor $detectRenderArgumentNodeVisitor;

    private ResolvedTemplateNameCollector $resolvedTemplateNameCollector;

    public function __construct(
        DetectRenderArgumentNodeVisitor $detectRenderArgumentNodeVisitor,
        ResolvedTemplateNameCollector $resolvedTemplateNameCollector
    ) {
        $this->detectRenderArgumentNodeVisitor = $detectRenderArgumentNodeVisitor;
        $this->resolvedTemplateNameCollector = $resolvedTemplateNameCollector;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'controller',
            KernelEvents::CONTROLLER_ARGUMENTS => 'controllerArguments',
        ];
    }

    public function controller(ControllerEvent $controllerEvent): void
    {
        $controllerClass = $this->resolveControllerClass($controllerEvent);

        $controllerReflection = new ReflectionClass($controllerClass);

        /** @var string $controllerFileName */
        $controllerFileName = $controllerReflection->getFileName();

        $phpParser = $this->getPhpParser();

        /** @var Node[] $controllerNodes */
        $controllerNodes = $phpParser->parse(FileSystem::read($controllerFileName));

        // the template name is not in the controller
        if ($controllerEvent->getController() instanceof TemplateController) {
            return;
        }

        // extract $this->render('$value')
        $nodeTraverser = new NodeTraverser();

        $controllerMethod = $this->resolveControllerMethod($controllerEvent);
        $this->detectRenderArgumentNodeVisitor->setMethodName($controllerMethod);

        $nodeTraverser->addVisitor($this->detectRenderArgumentNodeVisitor);
        $nodeTraverser->traverse($controllerNodes);
    }

    /**
     * Special case for @see TemplateController
     */
    public function controllerArguments(ControllerArgumentsEvent $controllerArgumentsEvent): void
    {
        if (! $controllerArgumentsEvent->getController() instanceof TemplateController) {
            return;
        }

        $templateName = $controllerArgumentsEvent->getArguments()[0];
        $this->resolvedTemplateNameCollector->setValue($templateName);
    }

    private function resolveControllerClass(ControllerEvent $controllerEvent): string
    {
        /** @var string[]|object[]|object $controllerCallable */
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
        /** @var string[]|object[]|object $controllerCallable */
        $controllerCallable = $controllerEvent->getController();

        if (is_array($controllerCallable)) {
            return $controllerCallable[1];
        }

        // single action controller
        return '__invoke';
    }
}
