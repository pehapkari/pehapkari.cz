<?php declare(strict_types=1);

namespace Pehapkari\User\EventSubscriber;

use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use Pehapkari\User\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @see https://symfony.com/doc/master/bundles/EasyAdminBundle/book/complex-dynamic-backends.html#event-subscriber-example
 */
final class UserEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    /**
     * @return string[][]
     */
    public static function getSubscribedEvents(): iterable
    {
        yield EasyAdminEvents::PRE_UPDATE => ['hashUserPassword'];
    }

    public function hashUserPassword(GenericEvent $genericEvent): void
    {
        $entity = $genericEvent->getSubject();
        if (! $entity instanceof User) {
            return;
        }

        $password = $genericEvent->getArgument('entity')->getPassword();
        $hashedPassword = $this->userPasswordEncoder->encodePassword($entity, $password);

        $entity->setPassword($hashedPassword);

        $genericEvent['entity'] = $entity;
    }
}
