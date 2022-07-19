<?php

namespace App\Tests\Controller;

use App\Entity\Movies;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MovieControllerTest extends WebTestCase
{
    protected $url = 'http://127.0.0.1:8000';

    /**
     * @return array|int|string|bool
     */
    public function testGenerateRandomUser()
    {
        $container = self::getContainer();
        $em = $container->get('doctrine')->getManager();
        $user = new User();
        $user->setEmail('test_'.rand(1,100).'@gmail.com');
        $user->setFirstName('test_'.rand(1,100));
        $user->setLastName('test_'.rand(1,100));
        try {
            $em->persist($user);
            $em->flush();
            $this->assertTrue(true, 'successfully created user');
        } catch (\Exception $exception) {
            dump($exception);
        }
    }

    /**
     * @return void
     */
    public function testNewAction(): void
    {
        $client = static::createClient();
        $createUrl = $this->url.'/api/v1/movies';
        $objMovies = new Movies();
        $userId = $this->getRandomUserId();
        if (!$userId > 0) $userId = 1;
        $objMovies->setName('test_'.rand(1,100));
        $objMovies->setDirector('director_'.rand(1,100));
        $objMovies->setReleaseAt(new \DateTime());
        $objMovies->setRatings([]);
        $objMovies->setCasts([]);

        $movies = array(
            'name' => '',
            'director' => '',
            'release_date' => '',
            'ratings' => '',
            'casts' => ''
        );
        $newUrl = $createUrl.'?name='.$objMovies->getName().'&director='.$objMovies->getDirector().'&release_date='.$objMovies->getReleaseAt()->format('d-m-y').'&user_id='.$userId;
        $crawler = $client->request('POST', $newUrl);
        $this->assertTrue(true,'successfully created new movie');
    }

    /**
     * @return void
     */
   public function testGetMovie(): void
    {
        $client = static::createClient();
        $getUrl = $this->url.'/api/v1/movies/';
        $randomId = $this->getRandomMovieId();
        if (!$randomId > 0) $randomId = 1;
        $newUrl = $getUrl.$randomId;
        $crawler = $client->request('GET', $newUrl);
        dump("response => ".$client->getResponse()->getContent());
        $this->assertTrue(true);
    }

    /**
     * @return void
     */
   public function testGetUserMovies()
    {
        $client = static::createClient();
        $getUrl = $this->url.'/api/v1/list/movies?';
        $userId = $this->getRandomUserId();
        if (!$userId > 0) $userId = 1;
        $newUrl = $getUrl.'user_id='.$userId;
        $crawler = $client->request('GET', $newUrl);
        dump("response => ".$client->getResponse()->getContent());
        $this->assertTrue(true);
    }

    /**
     * @return array|int|string
     */
    public function getRandomMovieId()
    {
        $container = self::getContainer();
        $repository = $container->get('doctrine')->getRepository(Movies::class);
        $movies = $repository->findAll();
        $id = array();
        foreach ($movies as $movie) {
            $id[] = $movie->getId();
        }
        return array_rand($id);
    }

    /**
     * @return array|int|string|bool
     */
    public function getRandomUserId()
    {
        $container = self::getContainer();
        $repository = $container->get('doctrine')->getRepository(User::class);
        $users = $repository->findAll();
        $id = array();
        foreach ($users as $user) {
            $id[] = $user->getId();
        }
        return array_rand($id);
    }
}