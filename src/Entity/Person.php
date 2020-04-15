<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PersonRepository")
 */
class Person
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $MissingID;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fullName;

    /**
     * @ORM\Column(type="date")
     */
    private $missingSince;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthdate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $residenceCity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $reisdenceState;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $imgUrl;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $imgAge;

    /**
     * @ORM\Column(type="boolean")
     */
    private $searchActive;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMissingID(): ?int
    {
        return $this->MissingID;
    }

    public function setMissingID(int $MissingID): self
    {
        $this->MissingID = $MissingID;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getMissingSince(): ?\DateTimeInterface
    {
        return $this->missingSince;
    }

    public function setMissingSince(\DateTimeInterface $missingSince): self
    {
        $this->missingSince = $missingSince;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(?\DateTimeInterface $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getResidenceCity(): ?string
    {
        return $this->residenceCity;
    }

    public function setResidenceCity(?string $residenceCity): self
    {
        $this->residenceCity = $residenceCity;

        return $this;
    }

    public function getReisdenceState(): ?string
    {
        return $this->reisdenceState;
    }

    public function setReisdenceState(?string $reisdenceState): self
    {
        $this->reisdenceState = $reisdenceState;

        return $this;
    }

    public function getImgUrl(): ?string
    {
        return $this->imgUrl;
    }

    public function setImgUrl(string $imgUrl): self
    {
        $this->imgUrl = $imgUrl;

        return $this;
    }

    public function getImgAge(): ?int
    {
        return $this->imgAge;
    }

    public function setImgAge(?int $imgAge): self
    {
        $this->imgAge = $imgAge;

        return $this;
    }

    public function getSearchActive(): ?bool
    {
        return $this->searchActive;
    }

    public function setSearchActive(bool $searchActive): self
    {
        $this->searchActive = $searchActive;

        return $this;
    }
}
