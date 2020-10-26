<?php

namespace App\EventListener;

use App\Entity\LikeNotification;
use App\Entity\MicroPost;
use App\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\PersistentCollection;

class LikeNotificationSubscriber implements EventSubscriber
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::onFlush,
        ];
    }

    public function onFlush(OnFlushEventArgs $arguments)
    {
        $entityManager = $arguments->getEntityManager();
        $unitOfWork = $entityManager->getUnitOfWork();

        /** @var PersistentCollection $collectionUpdate */
        foreach ($unitOfWork->getScheduledCollectionUpdates() as $collectionUpdate) {
            $owner = $collectionUpdate->getOwner();

            if (!($owner instanceof MicroPost)) {
                continue;
            }

            $map = $collectionUpdate->getMapping();

            if ($map['fieldName'] !== 'likedBy') {
                continue;
            }

            $insertDifference = $collectionUpdate->getInsertDiff();

            if (empty($insertDifference)) {
                return;
            }

            $notification = $this->makeNotificationForLikedMicroPost($owner, reset($insertDifference));

            $entityManager->persist($notification);

            $unitOfWork->computeChangeSets();
        }
    }

    private function makeNotificationForLikedMicroPost(MicroPost $microPost, User $likedBy): LikeNotification
    {
        $notification = new LikeNotification();
        $notification->setUser($microPost->getUser());
        $notification->setMicroPost($microPost);
        $notification->setLikedBy($likedBy);

        return $notification;
    }
}
