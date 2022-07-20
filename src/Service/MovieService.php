<?php


namespace App\Service;

use App\Entity\Movies;
use App\Entity\MoviesCast;
use App\Entity\MoviesRatings;
use App\Entity\Ratings;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Exception as ExceptionAlias;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MovieService
{
    /**
     * @throws OptimisticLockException
     * @throws TransportExceptionInterface
     * @throws ORMException
     * @throws ExceptionAlias
     */
    public function addMovies(Request $request, EntityManager $em, MailerInterface $mailer, $user): array
    {
        $response = array();
        $status = '';
        $message = '';
        $isError = false;

        $movieName = $request->get('name');
        $releaseDate = $request->get('release_date');
        $casts = $request->get('casts') ?? array();
        $director = $request->get('director');
        $imdb = $request->get('imdb') ?? '';
        $rottenTomatto = $request->get('rotten_tomatto') ?? '';
        $ratings = '';

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
            $objMovies->setUser($user);
            $em->persist($objMovies);
            $em->flush();

            if(!empty($casts)) {
                foreach ($casts as $cast) {
                    $objMoviesCast = new MoviesCast();
                    $objMoviesCast->setName($cast);
                    $objMoviesCast->setMovies($objMovies);
                    $em->persist($objMoviesCast);
                }
            }
            $em->flush();

            if($imdb !== '') {
                $ratings .= 'imdb: '.$imdb. ', ';
                $objRatingsForImdb = $em->getRepository(Ratings::class)->findOneByName('imdb');
                $objMoviesRatings = new MoviesRatings();
                $objMoviesRatings->setRatings($objRatingsForImdb);
                $objMoviesRatings->setMovies($objMovies);
                $objMoviesRatings->setValue($imdb);
                $em->persist($objMoviesRatings);
            }

            if($rottenTomatto !== '') {
                $ratings .= 'rotten_tomatto: '.$rottenTomatto;
                $objRatingsForRotten = $em->getRepository(Ratings::class)->findOneByName('rotten_tomatto');
                $objMoviesRatings2 = new MoviesRatings();
                $objMoviesRatings2->setRatings($objRatingsForRotten);
                $objMoviesRatings2->setMovies($objMovies);
                $objMoviesRatings2->setValue($rottenTomatto);
                $em->persist($objMoviesRatings2);
            }
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
                    'ratings' => $ratings,
                    'casts' => implode(', ', $casts),
                    'userName' => $user->getEmail(),
                ]);
            $mailer->send($email);

            $response['status'] = 'success';
            $response['message'] = 'Movie added successfully.';
        }

        return $response;
    }

    /**
     * @param Request $request
     * @param EntityManager $em
     * @param $user
     * @return array
     */
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
                $tempArr = array(
                    'name' => $movie->getName(),
                    'casts' => [],
                    'release_data' => $movie->getReleaseAt()->format('d-m-y'),
                    'director' => $movie->getDirector(),
                    'ratings' => []
                );

                $objMoviesCasts = $em->getRepository(MoviesCast::class)->findByMovies($movie);
                if(!empty($objMoviesCasts)) {
                    $castArr = array();
                    foreach ($objMoviesCasts as $objMoviesCast) {
                        $castArr[] = $objMoviesCast->getName();
                    }
                    $tempArr['casts'] = $castArr;
                }

                $objMoviesRatings = $em->getRepository(MoviesRatings::class)->findByMovies($movie);
                if(!empty($objMoviesRatings)) {
                    $ratingArr = array();
                    foreach ($objMoviesRatings as $objMoviesRating) {
                        if($objMoviesRating->getRatings()->getName() == 'imdb'){
                            $ratingArr['imdb'] = $objMoviesRating->getValue();
                        }
                        if($objMoviesRating->getRatings()->getName() == 'rotten_tomatto'){
                            $ratingArr['rotten_tomatto'] = $objMoviesRating->getValue();
                        }
                    }
                    $tempArr['ratings'] = $ratingArr;
                }

                $moviesArr[] = $tempArr;
            }

            $response['status'] = 'success';
            $response['data'] = $moviesArr;
        }
        return $response;
    }

    /**
     * @param EntityManager $em
     * @param $id
     * @return array
     */
    public function getMovie(EntityManager $em, $id): array
    {
        $movies = $em->getRepository(Movies::class)->find($id);

        if ($movies instanceof Movies) {
            $movieArr = array(
                'name' => $movies->getName(),
                'casts' => [],
                'release_data' => $movies->getReleaseAt()->format('d-m-y'),
                'director' => $movies->getDirector(),
                'ratings' => []

            );
            $objMoviesCasts = $em->getRepository(MoviesCast::class)->findByMovies($movies);
            if(!empty($objMoviesCasts)) {
                $castArr = array();
                foreach ($objMoviesCasts as $objMoviesCast) {
                    $castArr[] = $objMoviesCast->getName();
                }
                $movieArr['casts'] = $castArr;
            }

            $objMoviesRatings = $em->getRepository(MoviesRatings::class)->findByMovies($movies);
            if(!empty($objMoviesRatings)) {
                $ratingArr = array();
                foreach ($objMoviesRatings as $objMoviesRating) {
                    if($objMoviesRating->getRatings()->getName() == 'imdb'){
                        $ratingArr['imdb'] = $objMoviesRating->getValue();
                    }
                    if($objMoviesRating->getRatings()->getName() == 'rotten_tomatto'){
                        $ratingArr['rotten_tomatto'] = $objMoviesRating->getValue();
                    }
                }
                $movieArr['ratings'] = $ratingArr;
            }
        }
        else {
            $movieArr['status'] = 'error';
            $movieArr['message'] = 'Movie object not found for given id : '. $id;
        }

        return $movieArr;
    }
}
