<?php

namespace App\Controller;

use App\Entity\Editeur;
use App\Repository\EditeurRepository;
use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiEditeurController extends AbstractController
{
    #[Route('/api/editeurs', name: 'app_api_editeurs', methods:['GET'])]
    public function list(EditeurRepository $repo, SerializerInterface $ser): JsonResponse
    {
        $editeurs=$repo->findAll();
        $resultat=$ser->serialize(
            $editeurs,
            'json',
            [
                'groups'=>['EL']
            ]
        );

        return new JsonResponse($resultat, 200, [], true);
    }

    #[Route('/api/editeur/{id}', name: 'app_api_editeur', methods:['GET'])]
    public function show(?Editeur $editeur, SerializerInterface $ser): JsonResponse
    {
        if ($editeur == null) {return new JsonResponse("cet editeur n'existe pas", Response::HTTP_NOT_FOUND, [], true);}
        $resultat=$ser->serialize(
            $editeur,
            'json',
            [
                'groups'=>['ES']
            ]
        );

        return new JsonResponse($resultat, Response::HTTP_OK, [], true);
    }
    
    #[Route('/api/editeur', name: 'app_api_editeur_create', methods:['POST'])]
    public function create(Request $req, EntityManagerInterface $man, SerializerInterface $ser, ValidatorInterface $vld): JsonResponse
    {
        $data = $req->getContent();
        $editeur = $ser->deserialize($data, Editeur::class, 'json');

        // $datab = $ser->decode($data, 'json');
        // $natio = $nrepo->find($datab['livres']['id']);
        // if ($natio == null) {return new JsonResponse("La nationalité saisie n'existe pas", Response::HTTP_NOT_ACCEPTABLE, [], true);}
        // $editeur->setLivre($natio);

        $err = $vld->validate($editeur);
        if (count($err)) {
            $errjson = $ser->serialize($err, 'json');
            return new JsonResponse($errjson, Response::HTTP_NOT_ACCEPTABLE, [], true);
        }

        $man->persist($editeur);
        $man->flush();

        return new JsonResponse(
            "editeur créé",
            Response::HTTP_CREATED,
            ["location"=>$this->generateUrl(
                'app_api_editeur',
                ['id'=>$editeur->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            )],
            true
        );
    }
    
    #[Route('/api/editeur/{id}', name: 'app_api_editeur_update', methods:['PUT'])]
    public function update(Editeur $editeur, Request $req, EntityManagerInterface $man, LivreRepository $nrepo, SerializerInterface $ser, ValidatorInterface $vld): JsonResponse
    {
        $data = $req->getContent();
        $editeur = $ser->deserialize($data, Editeur::class, 'json', ['object_to_populate'=>$editeur]);

        // $datab = $ser->decode($data, 'json');
        // $natio = $nrepo->find($datab['livres']['id']);
        // if ($natio == null) {return new JsonResponse("La nationalité saisie n'existe pas", Response::HTTP_NOT_ACCEPTABLE, [], true);}
        // $editeur->setLivre($natio);

        $err = $vld->validate($editeur);
        if (count($err)) {
            $errjson = $ser->serialize($err, 'json');
            return new JsonResponse($errjson, Response::HTTP_NOT_ACCEPTABLE, [], true);
        }

        $man->persist($editeur);
        $man->flush();

        return new JsonResponse(
            "editeur modifié",
            Response::HTTP_OK,
            ["location"=>$this->generateUrl(
                'app_api_editeur',
                ['id'=>$editeur->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            )],
            true
        );
    }
    
    #[Route('/api/editeur/{id}', name: 'app_api_editeur_delete', methods:['DELETE'])]
    public function delete(?Editeur $editeur, EntityManagerInterface $man): JsonResponse
    {
        if ($editeur == null) {return new JsonResponse("cet editeur n'existe pas", Response::HTTP_NOT_FOUND, [], true);}
        $man->remove($editeur);
        $man->flush();

        return new JsonResponse(
            "editeur supprimé",
            Response::HTTP_OK, []
        );
    }
}
