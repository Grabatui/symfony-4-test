<?php

namespace App\Entity;

use App\Repository\MicroPostRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=MicroPostRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class MicroPost
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=280)
     * @Assert\NotBlank()
     * @Assert\Length(min=10)
     */
    private ?string $text;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?DateTime $time;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?User $user;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="microPostsLiked")
     * @ORM\JoinTable(
     *     name="micro_post_likes",
     *     joinColumns={
     *          @ORM\JoinColumn(name="micro_post_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *          @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *     }
     * )
     */
    private Collection $likedBy;

    public function __construct()
    {
        $this->likedBy = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text): void
    {
        $this->text = $text;
    }

    /**
     * @return mixed
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param mixed $time
     */
    public function setTime($time): void
    {
        $this->time = $time;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     */
    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    /**
     * @ORM\PrePersist()
     */
    public function setTimeOnPersist(): void
    {
        $this->setTime(new DateTime());
    }

    /**
     * @return Collection|null
     */
    public function getLikedBy()
    {
        return $this->likedBy;
    }

    public function isLikedByUser(?User $user): bool
    {
        return ($user instanceof User) ? $this->getLikedBy()->contains($user) : false;
    }

    public function isOwner(User $user): bool
    {
        return $this->user->getId() === $user->getId();
    }

    public function like(User $user): void
    {
        if ($this->isLikedByUser($user)) {
            return;
        }

        $this->getLikedBy()->add($user);
    }

    public function unlike(User $user): void
    {
        if (!$this->isLikedByUser($user)) {
            return;
        }

        $this->getLikedBy()->removeElement($user);
    }
}
