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
     * @ORM\Column(type="datetime")
     */
    private $dateStart;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateEnd;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $hourStart;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $hourEnd;

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
     * @ORM\ManyToMany(targetEntity=Rule::class, mappedBy="challenges")
     */
    private $rules;

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

    public function getDateStart(): ?\DateTimeInterface
    {
        return $this->dateStart;
    }

    public function setDateStart(\DateTimeInterface $dateStart): self
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->dateEnd;
    }

    public function setDateEnd(\DateTimeInterface $dateEnd): self
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    public function getHourStart(): ?string
    {
        return $this->hourStart;
    }

    public function setHourStart(string $hourStart): self
    {
        $this->hourStart = $hourStart;

        return $this;
    }

    public function getHourEnd(): ?string
    {
        return $this->hourEnd;
    }

    public function setHourEnd(string $hourEnd): self
    {
        $this->hourEnd = $hourEnd;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
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

    public function setRegistrationOpening(\DateTimeInterface $registrationOpening): self
    {
        $this->registrationOpening = $registrationOpening;

        return $this;
    }

    public function getRegistrationClosing(): ?\DateTimeInterface
    {
        return $this->registrationClosing;
    }

    public function setRegistrationClosing(\DateTimeInterface $registrationClosing): self
    {
        $this->registrationClosing = $registrationClosing;

        return $this;
    }

    /**
     * @return Collection|Rule[]
     */
    public function getRules(): Collection
    {
        return $this->rules;
    }

    public function addRule(Rule $rule): self
    {
        if (!$this->rules->contains($rule)) {
            $this->rules[] = $rule;
            $rule->addChallenge($this);
        }

        return $this;
    }

    public function removeRule(Rule $rule): self
    {
        if ($this->rules->contains($rule)) {
            $this->rules->removeElement($rule);
            $rule->removeChallenge($this);
        }

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
