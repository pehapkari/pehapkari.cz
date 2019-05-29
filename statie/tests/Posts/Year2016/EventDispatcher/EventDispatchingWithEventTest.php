<?php declare(strict_types=1);

namespace Pehapkari\Statie\Tests\Posts\Year2016\EventDispatcher;

use Pehapkari\Statie\Posts\Year2016\EventDispatcher\Event\YoutuberNameEvent;
use Pehapkari\Statie\Posts\Year2016\EventDispatcher\EventSubscriber\EventAwareNotifyMeOnVideoPublishedEventSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

final class EventDispatchingWithEventTest extends TestCase
{
    public function test(): void
    {
        $eventDispatcher = new EventDispatcher;
        $eventAwareNotifyMeOnVideoPublishedEventSubscriber = new EventAwareNotifyMeOnVideoPublishedEventSubscriber;
        $eventDispatcher->addSubscriber($eventAwareNotifyMeOnVideoPublishedEventSubscriber);

        $this->assertSame('', $eventAwareNotifyMeOnVideoPublishedEventSubscriber->getYoutuberUserName());

        $youtuberNameEvent = new YoutuberNameEvent('Jirka Král');
        $eventDispatcher->dispatch($youtuberNameEvent);

        $this->assertSame(
            'Jirka Král',
            $eventAwareNotifyMeOnVideoPublishedEventSubscriber->getYoutuberUserName()
        );
    }
}
