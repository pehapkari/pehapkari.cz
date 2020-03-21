<?php

declare(strict_types=1);

namespace Pehapkari\Doctrine\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Pehapkari\BetterEasyAdmin\Entity\UploadableImageTrait;
use Pehapkari\Contract\Doctrine\Entity\UploadDestinationAwareInterface;

final class SetUploadDestinationOnPostLoadEventSubscriber implements EventSubscriber
{
    private string $uploadDestination;

    private string $relativeUploadDestination;

    public function __construct(string $uploadDestination, string $relativeUploadDestination)
    {
        $this->uploadDestination = $uploadDestination;
        $this->relativeUploadDestination = $relativeUploadDestination;
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

        // for attaching files
        if ($entity instanceof UploadDestinationAwareInterface) {
            $entity->setUploadDestination($this->uploadDestination);
        }

        // for public rendering
        $traitsInClass = class_uses($entity);
        if (isset($traitsInClass[UploadableImageTrait::class])) {
            /** @var UploadableImageTrait $entity */
            $entity->setRelativeUploadDestination($this->relativeUploadDestination);
        }
    }
}
