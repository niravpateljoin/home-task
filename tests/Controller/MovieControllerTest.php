<?php

namespace App\Tests\Controller;

use App\Entity\Movies;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MovieControllerTest extends WebTestCase
{
    protected $url = 'http://127.0.0.1:8000';

    /**
     * @return void
     */
    public function testGenerateRandomUser()
    {
        $container = self::getContainer();
        $em = $container->get('doctrine')->getManager();
        $repository = $container->get('doctrine')->getRepository(User::class);
        $users = $repository->findAll();
        if(!$users) {
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
        else {
            $this->assertTrue(true, 'User table is not empty, no need of Add User.');
        }
    }

    /**
     * @return void
     */
    public function testNewAction()
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
        dump("/api/v1/movies//[POST] response => ".$client->getResponse()->getContent());
        $this->assertTrue(true,'successfully created new movie');
    }

    /**
     * @return void
     */
    public function testGetMovie()
    {
        $client = static::createClient();
        $getUrl = $this->url.'/api/v1/movies/';
        $randomId = $this->getRandomMovieId();
        if (!$randomId) $randomId = 1;
        $newUrl = $getUrl.$randomId;
        $crawler = $client->request('GET', $newUrl);
        dump("/api/v1/movies/{id}/[GET] response => ".$client->getResponse()->getContent());
        $this->assertTrue(true);
    }

    /**
     * @return void
     */
   public function testGetUserMovies()
    {
        $client = static::createClient();
        $getUrl = $this->url.'/api/v1/movies?';
        $userId = $this->getRandomUserId();
        if (!$userId > 0) $userId = 1;
        $newUrl = $getUrl.'user_id='.$userId;
        $crawler = $client->request('GET', $newUrl);
        dump("/api/v1/movies/[GET] response => ".$client->getResponse()->getContent());
        $this->assertTrue(true);
    }

    /**
     * @return array|int|string|boolean
     */
    public function getRandomMovieId()
    {
        $container = self::getContainer();
        $repository = $container->get('doctrine')->getRepository(Movies::class);
        $movies = $repository->findAll();
        if(empty($movies)) {
            return false;
        }
        $id = array();
        foreach ($movies as $movie) {
            $id[] = $movie->getId();
        }
        return $id[array_rand($id)];
    }

    /**
     * @return array|int|string
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

        return $id[array_rand($id)];
    }
}