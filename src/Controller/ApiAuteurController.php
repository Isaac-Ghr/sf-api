<?php

namespace App\Controller;

use App\Entity\Auteur;
use App\Repository\AuteurRepository;
use App\Repository\NationaliteRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiAuteurController extends AbstractController
{
    #[Route('/api/auteurs', name: 'app_api_auteurs', methods:['GET'])]
    public function list(AuteurRepository $repo, SerializerInterface $ser): JsonResponse
    {
        $auteurs=$repo->findAll();
        $resultat=$ser->serialize(
            $auteurs,
            'json',
            [
                'groups'=>['AL']
            ]
        );

        return new JsonResponse($resultat, 200, [], true);
    }

    #[Route('/api/auteur/{id}', name: 'app_api_auteur', methods:['GET'])]
    public function show(?Auteur $auteur, SerializerInterface $ser): JsonResponse
    {
        if ($auteur == null) {return new JsonResponse("cet auteur n'existe pas", Response::HTTP_NOT_FOUND, [], true);}
        $resultat=$ser->serialize(
            $auteur,
            'json',
            [
                'groups'=>['AS']
            ]
        );

        return new JsonResponse($resultat, Response::HTTP_OK, [], true);
    }
    
    #[Route('/api/auteur', name: 'app_api_auteur_create', methods:['POST'])]
    public function create(Request $req, EntityManagerInterface $man, NationaliteRepository $nrepo, SerializerInterface $ser, ValidatorInterface $vld): JsonResponse
    {
        $data = $req->getContent();
        $auteur = $ser->deserialize($data, Auteur::class, 'json');

        $datab = $ser->decode($data, 'json');
        $natio = $nrepo->find($datab['nationalite']['id']);
        if ($natio == null) {return new JsonResponse("La nationalité saisie n'existe pas", Response::HTTP_NOT_ACCEPTABLE, [], true);}
        $auteur->setNationalite($natio);

        $err = $vld->validate($auteur);
        if (count($err)) {
            $errjson = $ser->serialize($err, 'json');
            return new JsonResponse($errjson, Response::HTTP_NOT_ACCEPTABLE, [], true);
        }

        $man->persist($auteur);
        $man->flush();

        return new JsonResponse(
            "auteur créé",
            Response::HTTP_CREATED,
            ["location"=>$this->generateUrl(
                'app_api_auteur',
                ['id'=>$auteur->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            )],
            true
        );
    }
    
    #[Route('/api/auteur/{id}', name: 'app_api_auteur_update', methods:['PUT'])]
    public function update(Auteur $auteur, Request $req, EntityManagerInterface $man, NationaliteRepository $nrepo, SerializerInterface $ser, ValidatorInterface $vld): JsonResponse
    {
        $data = $req->getContent();
        $auteur = $ser->deserialize($data, Auteur::class, 'json', ['object_to_populate'=>$auteur]);

        $datab = $ser->decode($data, 'json');
        $natio = $nrepo->find($datab['nationalite']['id']);
        if ($natio == null) {return new JsonResponse("La nationalité saisie n'existe pas", Response::HTTP_NOT_ACCEPTABLE, [], true);}
        $auteur->setNationalite($natio);

        $err = $vld->validate($auteur);
        if (count($err)) {
            $errjson = $ser->serialize($err, 'json');
            return new JsonResponse($errjson, Response::HTTP_NOT_ACCEPTABLE, [], true);
        }

        $man->persist($auteur);
        $man->flush();

        return new JsonResponse(
            "auteur modifié",
            Response::HTTP_OK,
            ["location"=>$this->generateUrl(
                'app_api_auteur',
                ['id'=>$auteur->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            )],
            true
        );
    }
    
    #[Route('/api/auteur/{id}', name: 'app_api_auteur_delete', methods:['DELETE'])]
    public function delete(?Auteur $auteur, EntityManagerInterface $man): JsonResponse
    {
        if ($auteur == null) {return new JsonResponse("cet auteur n'existe pas", Response::HTTP_NOT_FOUND, [], true);}
        $man->remove($auteur);
        $man->flush();

        return new JsonResponse(
            "auteur supprimé",
            Response::HTTP_OK, []
        );
    }
}
