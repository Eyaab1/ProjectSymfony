<?php

namespace App\Controller;

use App\Entity\Etudiant;
use App\Form\EtudiantType;
use App\Repository\EtudiantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/etudiant')]
class EtudiantController extends AbstractController
{
    #[Route('/', name: 'app_etudiant_index', methods: ['GET'])]
    public function index(EtudiantRepository $etudiantRepository, SerializerInterface $serializer): Response
    {
        try {
            $data = $etudiantRepository->findAll();
            $response = $serializer->serialize($data, 'json');
            return new JsonResponse($response, 200, [], true);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/new', name: 'app_etudiant_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,UserPasswordHasherInterface $passwordHasher): Response
    {
        $data = json_decode($request->getContent(), true);

    $etudiant = new Etudiant();
    $etudiant->setNom($data['nom']);
    $etudiant->setPrenom($data['prenom']);
    $etudiant->setEmail($data['email']);
    $etudiant->setNumtel($data['numtel']);
    $etudiant->setType('etudiant');
    $etudiant->setPassword($data['password']);
    $etudiant->setRoles(['ROLE_USER']);
    $etudiant->setSpecialite($data['specialite']);

    $entityManager->persist($etudiant);
    $entityManager->flush();

    return new JsonResponse(['status' => 'Etudiant created'], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_etudiant_show', methods: ['GET'])]
    public function show($id,EtudiantRepository $etudiantRepository, SerializerInterface $serializer): Response
    {
        try {
            $user = $etudiantRepository->find($id);
            if ($user === null) {
                throw new \Exception('User not found');
            }
            $response = $serializer->serialize($user, 'json');
            return new JsonResponse($response, 200, [], true);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}/edit', name: 'app_etudiant_edit', methods: ['PUT'])]
    public function edit(Request $request, Etudiant $etudiant, EntityManagerInterface $entityManager): Response
    {
        try{
            $data = json_decode($request->getContent(), true);
            $etudiant->setNom($data['nom']);
            $etudiant->setPrenom($data['prenom']);
            $etudiant->setNumtel($data['numtel']);
            $etudiant->setSpecialite($data['specialite']);
            $entityManager->flush();
            return new JsonResponse(['status' => 'Etudiant updated'], Response::HTTP_OK);

        }catch (\Exception $e) {
        return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
    }

    #[Route('/{id}', name: 'app_etudiant_delete', methods: ['POST'])]
    public function delete($id, EtudiantRepository $etudiantRepository, EntityManagerInterface $entityManager): Response
    {
        try {
            $etudiant = $etudiantRepository->find($id);
            if ($etudiant === null) {
                throw new \Exception('User not found');
            }
            $entityManager->remove($etudiant);
            $entityManager->flush();
            return new JsonResponse(['status' => 'User deleted'], Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
