<?php


namespace App\Service;

use App\Entity\Movies;
use App\Entity\User;
use DateTime;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MovieService
{
    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     * @throws \Doctrine\ORM\Exception\ORMException
     */
    public function addMovies(Request $request, EntityManager $em, MailerInterface $mailer, $user): array
    {
        $response = array();
        $status = '';
        $message = '';
        $isError = false;

        $movieName = $request->get('name');
        $releaseDate = $request->get('release_date');
        $casts = $request->get('casts') ? $request->get('casts') : array();
        $director = $request->get('director');
        $ratings = $request->get('ratings') ? $request->get('ratings') : array();


        if($movieName == '') {
            $isError = true;
            $status = 'error';
            $message .= 'Movie name is required. ';
        }
        if($director == '') {
            $isError = true;
            $status = 'error';
            $message .= 'Director name is required. ';
        }
        if($releaseDate == '')
        {
            $isError = true;
            $status = 'error';
            $message .= 'Release date is required. ';
        }
        if(!$user){
            $userId = $request->get('user_id');
            if($userId) {
                $user = $em->getRepository(User::class)->find($userId);
                if(!$user) {
                    $isError = true;
                    $status = 'error';
                    $message .= 'User not found, For provided userId.';
                }
            }
            else {
                $isError = true;
                $status = 'error';
                $message .= 'User not found, Please provide userId.';
            }
        }

        if($isError) {
            $response['status'] = $status;
            $response['message'] = $message;
        }
        else {
            $releaseDateTime = new DateTime($releaseDate);

            $objMovies = new Movies();
            $objMovies->setName($movieName);
            $objMovies->setDirector($director);
            $objMovies->setReleaseAt($releaseDateTime);
            $objMovies->setRatings($ratings);
            $objMovies->setCasts($casts);
            $objMovies->setUser($user);
            $em->persist($objMovies);
            $em->flush();

            $email = (new TemplatedEmail())
                ->from('admin@gmail.com')
                ->to($user->getEmail())
                ->subject('Welcome!')
                ->htmlTemplate('Movie/emailTemplate.html.twig')
                ->context([
                    'movieName' => $movieName,
                    'director' => $director,
                    'releaseDate' => $releaseDate,
                    'ratings' => implode('', $ratings),
                    'casts' => implode('', $casts),
                    'userName' => $user->getEmail(),
                ]);
            $mailer->send($email);

            $response['status'] = 'success';
            $response['message'] = 'Movie added successfully.';
        }

        return $response;
    }

    public function listMovies(Request $request, EntityManager $em, $user): array
    {
        if(!$user){
            $userId = $request->get('user_id');
            if($userId) {
                $user = $em->getRepository(User::class)->find($userId);
                if(!$user) {
                    $status = 'error';
                    $message = 'User not found, For provided userId.';
                }
            }
            else {
                $status = 'error';
                $message = 'User not found, Please provide userId.';
            }
        }

        if(!$user) {
            $response['status'] = $status;
            $response['message'] = $message;
        }
        else {
            $objMovies = $em->getRepository(Movies::class)->findByUser($user);

            $moviesArr = array();
            foreach ($objMovies as $movie) {
                $moviesArr[] = array(
                    'name' => $movie->getName(),
                    'casts' => $movie->getCasts(),
                    'release_data' => $movie->getReleaseAt()->format('d-m-y'),
                    'director' => $movie->getDirector(),
                    'ratings' => $movie->getRatings()
                );
            }

            $response['status'] = 'success';
            $response['data'] = $moviesArr;
        }
        return $response;
    }
}
