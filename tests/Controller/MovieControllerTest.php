<?php

namespace App\Tests\Controller;

use App\Entity\Movies;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MovieControllerTest extends WebTestCase
{
    protected $url = 'http://127.0.0.1:8000';
    /**
     * @return void
     */
    public function testNewAction(): void
    {
        $client = static::createClient();
        $createUrl = $this->url.'/api/v1/movies';

        $objMovies = new Movies();
        $objMovies->setName('test '.rand(1,100));
        $objMovies->setDirector('director '.rand(1,100));
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
        $request = $client->post('/api/programmers', null, json_encode($movies));
        $response = $request->send();
    print_r($response);exit();
        $objMovies = $client->request('GET', $createUrl);
        $crawler = $client->request('POST', $createUrl, array(
            'movies' => array(
                'name' => '',
                'director' => '',
                'release_date' => '',
                'ratings' => '',
                'casts' => ''
            )),
            array(),
            array(
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
            )
        );

        if(302 == $client->getResponse()->getStatusCode())
        {
            $this->fail('colleague already registered.');
        }
        $this->assertTrue(true,'successfully registered new colleague');
    }
    /**
     * @return void
     */
   /* public function testUpdate(): void
    {
        $client = static::createClient();
        $container = self::getContainer();
        $em = $container->get('doctrine')->getManager();
        $repository = $container->get('doctrine')->getRepository(Colleague::class);
        $randomColleague = $this->getRandomColleague();
        $colleagueUpdate = $repository->findOneById($randomColleague);
        $updateUrl = $this->url.'/'.$randomColleague.'/edit';
        $colleagueUpdate->setName('test Update'.rand(1,100));
        $colleagueUpdate->setEmail('test'.rand(1,100).'Update@gmail.com');
        $colleagueUpdate->setNotes('implemented by Web Test Update');
        $crawler = $client->request('GET', $updateUrl);
        $token = $crawler->filter('[name="colleague[_token]"]')->attr("value");
        $crawler = $client->request('POST', $updateUrl, array(
            'colleague' => array(
                '_token' => $token,
                'name' => $colleagueUpdate->getName(),
                'email' => $colleagueUpdate->getEmail(),
                'notes' => $colleagueUpdate->getNotes()
            )),
            array(),
            array(
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
            )
        );
        $this->assertTrue(303 == $client->getResponse()->getStatusCode(),'successfully Updated colleague');
    }*/
    /**
     * @return void
     */
   /* public function testSendGreetings()
    {
        $client = static::createClient();
        $randomColleague = $this->getRandomColleague();
        $sendMailUrl = $this->url.'/send-greetings-email/'.$randomColleague;
        $crawler = $client->request('POST', $sendMailUrl );
        $this->assertTrue(303 == $client->getResponse()->getStatusCode(), "successfully send greetings!");
    }*/
    /**
     * @return array|int|string
     */
    /*public function getRandomColleague()
    {
        $container = self::getContainer();
        $repository = $container->get('doctrine')->getRepository(Colleague::class);
        $colleagues = $repository->findAll();
        $id = array();
        foreach ($colleagues as $colleague) {
            $id[] = $colleague->getId();
        }
        return array_rand($id);
    }*/
}