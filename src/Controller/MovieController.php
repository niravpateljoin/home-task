<?php

namespace App\Controller;

use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Service\MovieService;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{
    /**
     * @Route("/api/v1/movies", name="add_movie", methods={"POST"})
     *
     *
     * @param Request $request
     * @param MovieService $movieService
     * @param ManagerRegistry $doctrine
     * @param MailerInterface $mailer
     * @return JsonResponse
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransportExceptionInterface
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
     * @param MovieService $movieService
     * @return JsonResponse
     */
    public function getAction(Request $request,  ManagerRegistry $doctrine, MovieService $movieService): JsonResponse
    {
        $id = $request->get('id');
        $em = $doctrine->getManager();

        // call to getMovie function from the MovieService..
        $movieArr = $movieService->getMovie($em, $id);

        return new JsonResponse($movieArr);
    }

    /**
     * @Route("/api/v1/movies", name="list_movie", methods={"GET"})
     *
     * @param Request $request
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
