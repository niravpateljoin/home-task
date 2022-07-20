<?php

namespace App\Entity;

use App\Repository\MoviesCastRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MoviesCastRepository::class)
 */
class MoviesCast
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Movies::class)
     */
    private $movies;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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
}
