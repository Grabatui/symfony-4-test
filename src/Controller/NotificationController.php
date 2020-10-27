<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\User;
use App\Repository\NotificationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/notifications")
 * @IsGranted("ROLE_USER")
 */
class NotificationController extends AbstractController
{
    private NotificationRepository $notificationRepository;

    public function __construct(NotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * @Route("/unread_count", name="notification_unread")
     */
    public function unreadCount()
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->json([
            'count' => $this->notificationRepository->getUnseenCountByUser($user),
        ]);
    }

    /**
     * @Route("/unseen", name="notification_unseen")
     */
    public function unseenNotifications()
    {
        $notifications = $this->notificationRepository->findBy([
            'user' => $this->getUser(),
            'seen' => false,
        ]);

        return $this->render('notification/list.html.twig', compact('notifications'));
    }

    /**
     * @Route("/{id}/set_seen", name="notification_set_seen")
     *
     * @param Notification $notification
     * @return RedirectResponse
     */
    public function setSeen(Notification $notification)
    {
        $notification->setSeen(true);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('notification_unseen');
    }

    /**
     * @Route("/set_all_seen", name="notification_set_all_seen")
     */
    public function setAllSeen()
    {
        $this->notificationRepository->markAllAsSeenByUser($this->getUser());
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('notification_unseen');
    }
}
