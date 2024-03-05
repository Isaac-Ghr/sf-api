<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Repository\LivreRepository;
use App\Repository\AuteurRepository;
use App\Repository\EditeurRepository;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiLivreController extends AbstractController
{
    #[Route('/api/livres', name: 'app_api_livres', methods:['GET'])]
    public function list(LivreRepository $repo, SerializerInterface $ser): JsonResponse
    {
        $livres=$repo->findAll();
        $resultat=$ser->serialize(
            $livres,
            'json',
            [
                'groups'=>['LL']
            ]
        );

        return new JsonResponse($resultat, 200, [], true);
    }

    #[Route('/api/livre/{id}', name: 'app_api_livre', methods:['GET'])]
    public function show(?Livre $livre, SerializerInterface $ser): JsonResponse
    {
        if ($livre == null) {return new JsonResponse("cet livre n'existe pas", Response::HTTP_NOT_FOUND, [], true);}
        $resultat=$ser->serialize(
            $livre,
            'json',
            [
                'groups'=>['LS']
            ]
        );

        return new JsonResponse($resultat, Response::HTTP_OK, [], true);
    }
    
    #[Route('/api/livre', name: 'app_api_livre_create', methods:['POST'])]
    public function create(Request $req, EntityManagerInterface $man, AuteurRepository $arepo, EditeurRepository $erepo, GenreRepository $grepo, SerializerInterface $ser, ValidatorInterface $vld): JsonResponse
    {
        $data = $req->getContent();
        $livre = $ser->deserialize($data, Livre::class, 'json');

        $datab = $ser->decode($data, 'json');

        // auteur
        $auteur = $arepo->find($datab['auteur']['id']);
        if ($auteur == null) {return new JsonResponse("L'auteur saisi n'existe pas", Response::HTTP_NOT_ACCEPTABLE, [], true);}
        $livre->setAuteur($auteur);
        // editeur
        $editeur = $erepo->find($datab['editeur']['id']);
        if ($editeur == null) {return new JsonResponse("L'editeur saisi n'existe pas", Response::HTTP_NOT_ACCEPTABLE, [], true);}
        $livre->setEditeur($editeur);
        // genre
        $genre = $grepo->find($datab['genre']['id']);
        if ($genre == null) {return new JsonResponse("Le genre saisi n'existe pas", Response::HTTP_NOT_ACCEPTABLE, [], true);}
        $livre->setGenre($genre);

        $err = $vld->validate($livre);
        if (count($err)) {
            $errjson = $ser->serialize($err, 'json');
            return new JsonResponse($errjson, Response::HTTP_NOT_ACCEPTABLE, [], true);
        }

        $man->persist($livre);
        $man->flush();

        return new JsonResponse(
            "livre créé",
            Response::HTTP_CREATED,
            ["location"=>$this->generateUrl(
                'app_api_livre',
                ['id'=>$livre->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            )],
            true
        );
    }
    
    #[Route('/api/livre/{id}', name: 'app_api_livre_update', methods:['PUT'])]
    public function update(Livre $livre, Request $req, EntityManagerInterface $man, AuteurRepository $arepo, EditeurRepository $erepo, GenreRepository $grepo, SerializerInterface $ser, ValidatorInterface $vld): JsonResponse
    {
        $data = $req->getContent();
        $livre = $ser->deserialize($data, Livre::class, 'json', ['object_to_populate'=>$livre]);

        $datab = $ser->decode($data, 'json');

        // auteur
        $auteur = $arepo->find($datab['auteur']['id']);
        if ($auteur == null) {return new JsonResponse("L'auteur saisi n'existe pas", Response::HTTP_NOT_ACCEPTABLE, [], true);}
        $livre->setAuteur($auteur);
        // editeur
        $editeur = $erepo->find($datab['editeur']['id']);
        if ($editeur == null) {return new JsonResponse("L'editeur saisi n'existe pas", Response::HTTP_NOT_ACCEPTABLE, [], true);}
        $livre->setEditeur($editeur);
        // genre
        $genre = $grepo->find($datab['genre']['id']);
        if ($genre == null) {return new JsonResponse("Le genre saisi n'existe pas", Response::HTTP_NOT_ACCEPTABLE, [], true);}
        $livre->setGenre($genre);

        $err = $vld->validate($livre);
        if (count($err)) {
            $errjson = $ser->serialize($err, 'json');
            return new JsonResponse($errjson, Response::HTTP_NOT_ACCEPTABLE, [], true);
        }

        $man->persist($livre);
        $man->flush();

        return new JsonResponse(
            "livre modifié",
            Response::HTTP_OK,
            ["location"=>$this->generateUrl(
                'app_api_livre',
                ['id'=>$livre->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            )],
            true
        );
    }
    
    #[Route('/api/livre/{id}', name: 'app_api_livre_delete', methods:['DELETE'])]
    public function delete(?Livre $livre, EntityManagerInterface $man): JsonResponse
    {
        if ($livre == null) {return new JsonResponse("cet livre n'existe pas", Response::HTTP_NOT_FOUND, [], true);}
        $man->remove($livre);
        $man->flush();

        return new JsonResponse(
            "livre supprimé",
            Response::HTTP_OK, []
        );
    }
}
