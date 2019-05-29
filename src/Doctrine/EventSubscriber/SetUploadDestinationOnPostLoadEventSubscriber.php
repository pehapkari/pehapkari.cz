<?php declare(strict_types=1);

namespace Pehapkari\Doctrine\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Pehapkari\Contract\Doctrine\Entity\UploadDestinationAwareInterface;

final class SetUploadDestinationOnPostLoadEventSubscriber implements EventSubscriber
{
    /**
     * @var string
     */
    private $uploadDestination;

    public function __construct(string $uploadDestination)
    {
        $this->uploadDestination = $uploadDestination;
    }

    /**
     * @return string[]
     */
    public function getSubscribedEvents(): array
    {
        return [Events::postLoad];
    }

    public function postLoad(LifecycleEventArgs $lifecycleEventArgs): void
    {
        $entity = $lifecycleEventArgs->getEntity();
        if (! $entity instanceof UploadDestinationAwareInterface) {
            return;
        }

        $entity->setUploadDestination($this->uploadDestination);
    }
}
