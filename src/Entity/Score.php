<?php

namespace App\Entity;

use App\Repository\ScoreRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ScoreRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Score
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $term;

    /**
     * @ORM\Column(type="integer")
     */
    private $numPositive;

    /**
     * @ORM\Column(type="integer")
     */
    private $numNeg;

    /**
     * @ORM\Column(type="datetime")
     */
    private $lastQueryTime;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTerm(): ?string
    {
        return $this->term;
    }

    public function setTerm(string $term): self
    {
        $this->term = $term;

        return $this;
    }

    public function getNumPositive(): ?int
    {
        return $this->numPositive;
    }

    public function setNumPositive(int $numPositive): self
    {
        $this->numPositive = $numPositive;

        return $this;
    }

    public function getNumNeg(): ?int
    {
        return $this->numNeg;
    }

    public function setNumNeg(int $numNeg): self
    {
        $this->numNeg = $numNeg;

        return $this;
    }

    public function getLastQueryTime(): ?\DateTimeInterface
    {
        return $this->lastQueryTime;
    }

    public function setLastQueryTime(\DateTimeInterface $lastQueryTime): self
    {
        $this->lastQueryTime = $lastQueryTime;

        return $this;
    }

    /** 
     *  @ORM\PrePersist
     *  @ORM\PreFlush
     */
    public function updateLastQueryTime()
    {
        $this->lastQueryTime = new \DateTime('@' . time());
    }
}
