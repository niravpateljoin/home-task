<?php

namespace App\DataFixtures;

use App\Entity\Ratings;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $ratesArr = ['imdb', 'rotten_tomatto'];
        // create 2 types of rate...
        foreach ($ratesArr as $rate) {
            $ratings = new Ratings();
            $ratings->setName($rate);
            $manager->persist($ratings);
        }
        $manager->flush();
    }
}
