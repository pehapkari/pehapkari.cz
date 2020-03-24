<?php

declare(strict_types=1);

namespace Pehapkari\Blog\Posts\Year2016\EventDispatcher\EventSubscriber;

use Pehapkari\Blog\Posts\Year2016\EventDispatcher\Event\YoutuberNameEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class EventAwareNotifyMeOnVideoPublishedEventSubscriber implements EventSubscriberInterface
{
    private string $youtuberUserName = '';

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [YoutuberNameEvent::class => 'notifyUserAboutVideo'];
    }

    public function notifyUserAboutVideo(YoutuberNameEvent $youtuberNameEvent): void
    {
        $this->youtuberUserName = $youtuberNameEvent->getYoutuberName();
    }

    public function getYoutuberUserName(): string
    {
        return $this->youtuberUserName;
    }
}
