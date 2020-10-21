<?php

namespace App\Entity;

use App\Repository\ChallengeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ChallengeRepository::class)
 */
class Challenge
{
    const TYPE_SOLO = 100;
    const TYPE_SOLO_MULTI = 150;
    const TYPE_MULTI = 200;
    const TYPE_RP = 300;

    const TypesChoices = [
        "Solo" => Challenge::TYPE_SOLO,
        "Solo & multi" => Challenge::TYPE_SOLO_MULTI,
        "Multi" => Challenge::TYPE_MULTI,
        "RolePlay" => Challenge::TYPE_RP,
    ];
    const Types = [
        Challenge::TYPE_SOLO => "Solo",
        Challenge::TYPE_SOLO_MULTI => "Solo & multi",
        Challenge::TYPE_MULTI => "Multi",
        Challenge::TYPE_RP => "RolePlay",
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * @ORM\Column(type="text")
     */
    private $banner;

    /**
     * @ORM\Column(type="integer")
     */
    private $maxChallenger;

    /**
     * @ORM\Column(type="datetime")
     */
    private $registrationOpening;

    /**
     * @ORM\Column(type="datetime")
     */
    private $registrationClosing;

    /**
     * @ORM\OneToMany(targetEntity=Participation::class, mappedBy="challenge")
     */
    private $participations;

    public function __construct()
    {
        $this->rules = new ArrayCollection();
        $this->participations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function isOpen(): bool
    {
       return $this->getRegistrationOpening() <= new \DateTime() && new \DateTime() <= $this->getRegistrationClosing();
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function getTypeStr(): ?string
    {
        return self::Types[$this->type];
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getBanner(): ?string
    {
        return $this->banner;
    }

    public function setBanner(string $banner): self
    {
        $this->banner = $banner;

        return $this;
    }

    public function getMaxChallenger(): ?int
    {
        return $this->maxChallenger;
    }

    public function setMaxChallenger(int $maxChallenger): self
    {
        $this->maxChallenger = $maxChallenger;

        return $this;
    }

    public function getRegistrationOpening(): ?\DateTimeInterface
    {
        return $this->registrationOpening;
    }

    public function setRegistrationOpening(\DateTime $registrationOpening): self
    {
        $this->registrationOpening = $registrationOpening->setTime(0,0,0);

        return $this;
    }

    public function getRegistrationClosing(): ?\DateTimeInterface
    {
        return $this->registrationClosing;
    }

    public function setRegistrationClosing(\DateTime $registrationClosing): self
    {
        $this->registrationClosing = $registrationClosing->setTime(23,59,59);
        return $this;
    }

    /**
     * @return Collection|Participation[]
     */
    public function getParticipations(): Collection
    {
        return $this->participations;
    }

    public function addParticipation(Participation $participation): self
    {
        if (!$this->participations->contains($participation)) {
            $this->participations[] = $participation;
            $participation->setChallenge($this);
        }

        return $this;
    }

    public function removeParticipation(Participation $participation): self
    {
        if ($this->participations->contains($participation)) {
            $this->participations->removeElement($participation);
            // set the owning side to null (unless already changed)
            if ($participation->getChallenge() === $this) {
                $participation->setChallenge(null);
            }
        }

        return $this;
    }
}
