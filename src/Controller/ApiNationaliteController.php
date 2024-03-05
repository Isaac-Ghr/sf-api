<?php

namespace App\Controller;

use App\Entity\Nationalite;
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

class ApiNationaliteController extends AbstractController
{
    #[Route('/api/nationalites', name: 'app_api_nationalites', methods:['GET'])]
    public function list(NationaliteRepository $repo, SerializerInterface $ser): JsonResponse
    {
        $nationalites=$repo->findAll();
        $resultat=$ser->serialize(
            $nationalites,
            'json',
            [
                'groups'=>['NL']
            ]
        );

        return new JsonResponse($resultat, 200, [], true);
    }

    #[Route('/api/nationalite/{id}', name: 'app_api_nationalite', methods:['GET'])]
    public function show(?Nationalite $nationalite, SerializerInterface $ser): JsonResponse
    {
        if ($nationalite == null) {return new JsonResponse("cet nationalite n'existe pas", Response::HTTP_NOT_FOUND, [], true);}
        $resultat=$ser->serialize(
            $nationalite,
            'json',
            [
                'groups'=>['NS']
            ]
        );

        return new JsonResponse($resultat, Response::HTTP_OK, [], true);
    }
    
    #[Route('/api/nationalite', name: 'app_api_nationalite_create', methods:['POST'])]
    public function create(Request $req, EntityManagerInterface $man, NationaliteRepository $nrepo, SerializerInterface $ser, ValidatorInterface $vld): JsonResponse
    {
        $data = $req->getContent();
        $nationalite = $ser->deserialize($data, Nationalite::class, 'json');

        // $datab = $ser->decode($data, 'json');
        // $natio = $nrepo->find($datab['nationalite']['id']);
        // if ($natio == null) {return new JsonResponse("La nationalité saisie n'existe pas", Response::HTTP_NOT_ACCEPTABLE, [], true);}
        // $nationalite->setNationalite($natio);

        $err = $vld->validate($nationalite);
        if (count($err)) {
            $errjson = $ser->serialize($err, 'json');
            return new JsonResponse($errjson, Response::HTTP_NOT_ACCEPTABLE, [], true);
        }

        $man->persist($nationalite);
        $man->flush();

        return new JsonResponse(
            "nationalite créé",
            Response::HTTP_CREATED,
            ["location"=>$this->generateUrl(
                'app_api_nationalite',
                ['id'=>$nationalite->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            )],
            true
        );
    }
    
    #[Route('/api/nationalite/{id}', name: 'app_api_nationalite_update', methods:['PUT'])]
    public function update(Nationalite $nationalite, Request $req, EntityManagerInterface $man, NationaliteRepository $nrepo, SerializerInterface $ser, ValidatorInterface $vld): JsonResponse
    {
        $data = $req->getContent();
        $nationalite = $ser->deserialize($data, Nationalite::class, 'json', ['object_to_populate'=>$nationalite]);

        // $datab = $ser->decode($data, 'json');
        // $natio = $nrepo->find($datab['nationalite']['id']);
        // if ($natio == null) {return new JsonResponse("La nationalité saisie n'existe pas", Response::HTTP_NOT_ACCEPTABLE, [], true);}
        // $nationalite->setNationalite($natio);

        $err = $vld->validate($nationalite);
        if (count($err)) {
            $errjson = $ser->serialize($err, 'json');
            return new JsonResponse($errjson, Response::HTTP_NOT_ACCEPTABLE, [], true);
        }

        $man->persist($nationalite);
        $man->flush();

        return new JsonResponse(
            "nationalite modifié",
            Response::HTTP_OK,
            ["location"=>$this->generateUrl(
                'app_api_nationalite',
                ['id'=>$nationalite->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            )],
            true
        );
    }
    
    #[Route('/api/nationalite/{id}', name: 'app_api_nationalite_delete', methods:['DELETE'])]
    public function delete(?Nationalite $nationalite, EntityManagerInterface $man): JsonResponse
    {
        if ($nationalite == null) {return new JsonResponse("cet nationalite n'existe pas", Response::HTTP_NOT_FOUND, [], true);}
        $man->remove($nationalite);
        $man->flush();

        return new JsonResponse(
            "nationalite supprimé",
            Response::HTTP_OK, []
        );
    }
}
