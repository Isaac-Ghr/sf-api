<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
    public function show(?Genre $genre, SerializerInterface $ser): JsonResponse
    {
        if ($genre == null) {return new JsonResponse("ce genre n'existe pas", Response::HTTP_NOT_FOUND, [], true);}
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
    public function create(Request $req, EntityManagerInterface $man, SerializerInterface $ser, ValidatorInterface $vld): JsonResponse
    {
        $data = $req->getContent();
        $genre = $ser->deserialize($data, Genre::class, 'json');

        $err = $vld->validate($genre);
        if (count($err)) {
            $errjson = $ser->serialize($err, 'json');
            return new JsonResponse($errjson, Response::HTTP_NOT_ACCEPTABLE, [], true);
        }

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
    
    #[Route('/api/genre/{id}', name: 'app_api_genre_update', methods:['PUT'])]
    public function update(Genre $genre, Request $req, EntityManagerInterface $man, SerializerInterface $ser, ValidatorInterface $vld): JsonResponse
    {
        $data = $req->getContent();
        $genre = $ser->deserialize($data, Genre::class, 'json', ['object_to_populate'=>$genre]);

        $err = $vld->validate($genre);
        if (count($err)) {
            $errjson = $ser->serialize($err, 'json');
            return new JsonResponse($errjson, Response::HTTP_NOT_ACCEPTABLE, [], true);
        }

        $man->persist($genre);
        $man->flush();

        return new JsonResponse(
            "genre modifié",
            Response::HTTP_OK,
            ["location"=>$this->generateUrl(
                'app_api_genre',
                ['id'=>$genre->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            )],
            true
        );
    }
    
    #[Route('/api/genre/{id}', name: 'app_api_genre_delete', methods:['DELETE'])]
    public function delete(?Genre $genre, EntityManagerInterface $man): JsonResponse
    {
        if ($genre == null) {return new JsonResponse("ce genre n'existe pas", Response::HTTP_NOT_FOUND, [], true);}
        $man->remove($genre);
        $man->flush();

        return new JsonResponse(
            "genre supprimé",
            Response::HTTP_OK, [],
        );
    }
}
