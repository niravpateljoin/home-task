<?php

namespace App\Entity;

use App\Repository\MoviesRatingsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MoviesRatingsRepository::class)
 */
class MoviesRatings
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Ratings::class)
     */
    private $ratings;

    /**
     * @ORM\ManyToOne(targetEntity=Movies::class)
     */
    private $movies;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $value;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRatings(): ?Ratings
    {
        return $this->ratings;
    }

    public function setRatings(?Ratings $ratings): self
    {
        $this->ratings = $ratings;

        return $this;
    }

    public function getMovies(): ?Movies
    {
        return $this->movies;
    }

    public function setMovies(?Movies $movies): self
    {
        $this->movies = $movies;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }
}
