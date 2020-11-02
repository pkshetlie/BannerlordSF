<?php

namespace App\Entity;

use App\Repository\RunRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RunRepository::class)
 */
class Run
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="runs")
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endDate;

    /**
     * @ORM\ManyToOne(targetEntity=Challenge::class, inversedBy="runs")
     */
    private $challenge;

    /**
     * @ORM\OneToMany(targetEntity=RunSettings::class, mappedBy="run")
     */
    private $runSettings;

    public function __construct()
    {
        $this->runSettings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getChallenge(): ?Challenge
    {
        return $this->challenge;
    }

    public function setChallenge(?Challenge $challenge): self
    {
        $this->challenge = $challenge;

        return $this;
    }

    /**
     * @return Collection|RunSettings[]
     */
    public function getRunSettings(): Collection
    {
        return $this->runSettings;
    }

    public function addRunSetting(RunSettings $runSetting): self
    {
        if (!$this->runSettings->contains($runSetting)) {
            $this->runSettings[] = $runSetting;
            $runSetting->setRun($this);
        }

        return $this;
    }

    public function removeRunSetting(RunSettings $runSetting): self
    {
        if ($this->runSettings->contains($runSetting)) {
            $this->runSettings->removeElement($runSetting);
            // set the owning side to null (unless already changed)
            if ($runSetting->getRun() === $this) {
                $runSetting->setRun(null);
            }
        }

        return $this;
    }
}