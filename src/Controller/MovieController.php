<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Movies;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Service\MovieService;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{
    /**
     * @Route("/api/v1/movies", name="add_movie", methods={"POST"})
     *
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Service\MovieService $movieService
     * @param \Doctrine\Persistence\ManagerRegistry $doctrine
     * @param \Symfony\Component\Mailer\MailerInterface $mailer
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function addAction(Request $request,
                              MovieService $movieService,
                              ManagerRegistry $doctrine,
                              MailerInterface $mailer): JsonResponse
    {
        $em = $doctrine->getManager();
        $user = $this->getUser();

        // call to add function from the MovieService..
        $response = $movieService->addMovies($request, $em, $mailer, $user);

        return new JsonResponse($response);
    }

    /**
     * @Route("/api/v1/movies/{id}", name="get_movie", methods={"GET"})
     *
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @return JsonResponse
     */
    public function getAction(Request $request,  ManagerRegistry $doctrine): JsonResponse
    {
        $movieArr = [];
        $id = $request->get('id');
        $em = $doctrine->getManager();
        $movies = $em->getRepository(Movies::class)->find($id);
        if ($movies instanceof Movies) {
            $movieArr = array(
                'name' => $movies->getName(),
                'casts' => $movies->getCasts(),
                'release_data' => $movies->getReleaseAt()->format('d-m-y'),
                'director' => $movies->getDirector(),
                'ratings' => $movies->getRatings()
            );
        }
        return new JsonResponse($movieArr);
    }

    /**
     * @Route("/api/v1/list/movies", name="list_movie", methods={"GET"})
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param MovieService $movieService
     * @param ManagerRegistry $doctrine
     *
     * @return JsonResponse
     */
    public function listAction(Request $request,
                               MovieService $movieService,
                               ManagerRegistry $doctrine): JsonResponse
    {
        $em = $doctrine->getManager();
        $user = $this->getUser();

        // call to list function from the MovieService..
        $response = $movieService->listMovies($request, $em, $user);

        return new JsonResponse($response);
    }
}
