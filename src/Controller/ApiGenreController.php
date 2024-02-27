<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ApiGenreController extends AbstractController
{
    #[Route('/api/genres', name: 'app_api_genres', methods:['GET'])]
    public function list(GenreRepository $repo, SerializerInterface $ser): JsonResponse
    {
        $genres=$repo->findAll();
        $resultat=$ser->serialize(
            $genres,
            'json',
            [
                'groups'=>['listeGenreSimple']
            ]
        );
        // dd($resultat);

        return new JsonResponse($resultat, 200, [], true);
    }

    #[Route('/api/genre/{id}', name: 'app_api_genre', methods:['GET'])]
    public function show(Genre $genre, SerializerInterface $ser): JsonResponse
    {
        $resultat=$ser->serialize(
            $genre,
            'json',
            [
                'groups'=>['listeGenreSimple']
            ]
        );

        return new JsonResponse($resultat, Response::HTTP_OK, [], true);
    }
    
    #[Route('/api/genre', name: 'app_api_genre_create', methods:['POST'])]
    public function create(Request $req, EntityManagerInterface $man, SerializerInterface $ser): JsonResponse
    {
        $data = $req->getContent();
        $genre = $ser->deserialize($data, Genre::class, 'json');
        $man->persist($genre);
        $man->flush();

        return new JsonResponse(
            "genre créé",
            Response::HTTP_CREATED,
            ["location"=>$this->generateUrl(
                'app_api_genre',
                ['id'=>$genre->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            )],
            true
        );
    }
}
