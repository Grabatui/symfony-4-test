<?php

namespace App\Entity;

use App\Repository\LikeNotificationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LikeNotificationRepository::class)
 */
class LikeNotification extends Notification
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\MicroPost")
     */
    private ?MicroPost $microPost;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private ?User $likedBy;

    /**
     * @return MicroPost|null
     */
    public function getMicroPost(): ?MicroPost
    {
        return $this->microPost;
    }

    /**
     * @param MicroPost|null $microPost
     */
    public function setMicroPost(?MicroPost $microPost): void
    {
        $this->microPost = $microPost;
    }

    /**
     * @return User|null
     */
    public function getLikedBy(): ?User
    {
        return $this->likedBy;
    }

    /**
     * @param User|null $likedBy
     */
    public function setLikedBy(?User $likedBy): void
    {
        $this->likedBy = $likedBy;
    }
}
