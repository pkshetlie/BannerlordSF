<?php

namespace App\Entity;

use App\Repository\ChallengeSettingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ChallengeSettingRepository::class)
 */
class ChallengeSetting
{
    const TEXT = 100;
    const SELECT = 200;
    const CHECKBOX = 300;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $label;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $ratio = 1;

    /**
     * @ORM\ManyToOne(targetEntity=Challenge::class, inversedBy="challengeSettings")
     */
    private $challenge;

    /**
     * @ORM\Column(type="integer")
     */
    private $inputType = self::TEXT;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=0)
     */
    private $defaultValue ;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isUsedForScore = true;

    /**
     * @ORM\OneToMany(targetEntity=RunSettings::class, mappedBy="challengeSetting")
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

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getRatio(): ?string
    {
        return $this->ratio;
    }

    public function setRatio(?string $ratio): self
    {
        $this->ratio = $ratio;

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

    public function getInputType(): ?int
    {
        return $this->inputType;
    }

    public function setInputType(int $inputType): self
    {
        $this->inputType = $inputType;

        return $this;
    }

    public function getDefaultValue(): ?string
    {
        return $this->defaultValue;
    }

    public function setDefaultValue(string $defaultValue): self
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    public function getIsUsedForScore(): ?bool
    {
        return $this->isUsedForScore;
    }

    public function setIsUsedForScore(bool $isUsedForScore): self
    {
        $this->isUsedForScore = $isUsedForScore;

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
            $runSetting->setChallengeSetting($this);
        }

        return $this;
    }

    public function removeRunSetting(RunSettings $runSetting): self
    {
        if ($this->runSettings->contains($runSetting)) {
            $this->runSettings->removeElement($runSetting);
            // set the owning side to null (unless already changed)
            if ($runSetting->getChallengeSetting() === $this) {
                $runSetting->setChallengeSetting(null);
            }
        }

        return $this;
    }
}
