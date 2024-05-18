<?php

namespace App\Controller;

use App\Entity\Administrateur;
use App\Form\AdministrateurType;
use App\Repository\AdministrateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
#[Route('/administrateur')]
/**
 * @Route("/admin")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class AdministrateurController extends AbstractController
{
    #[Route('/', name: 'app_administrateur_index', methods: ['GET'])]
    public function index(AdministrateurRepository $administrateurRepository,HttpClientInterface $client,SerializerInterface $serializer ): Response
    {
        try
            {$data = $administrateurRepository->findAll();
            $response = $serializer->serialize($data, 'json');
            return new JsonResponse($response, 200, [], true);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/new', name: 'app_administrateur_new', methods: [ 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,
    UserPasswordHasherInterface $passwordHasher): Response
    {
        try{
            $data = json_decode($request->getContent(), true);
            $user = new Administrateur();
            $user->setNom($data['nom']);
            $user->setPrenom($data['prenom']);
            $user->setEmail($data['email']);
            $user->setNumtel($data['numtel']);
            $user->setPassword($data['password']);
            $user->setType('administrateur');
            $user->setRoles(['ROLE_ADMIN']);
            $user->setRole($data['role']);

            $entityManager->persist($user);
            $entityManager->flush();
            return new JsonResponse(['status' => 'User created'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'app_administrateur_show', methods: ['GET'])]
    public function show($id,AdministrateurRepository $administrateurRepository,
    SerializerInterface $serializer): Response
    {
        try {
            $user = $administrateurRepository->find($id);
            if ($user === null) {
                return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
            }
            $response = $serializer->serialize($user, 'json');
            return new JsonResponse($response, 200, [], true);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}/edit', name: 'app_administrateur_edit', methods: ['PUT'])]
    public function edit(Request $request, Administrateur $administrateur, EntityManagerInterface $entityManager): Response
    {
        try{
            $data = json_decode($request->getContent(), true);
            $administrateur->setNom($data['nom']);
            $administrateur->setPrenom($data['prenom']);
            $administrateur->setNumtel($data['numtel']);
            $administrateur->setRole($data['role']);
            $entityManager->flush();
            return new JsonResponse(['status' => 'Administrateur updated'], Response::HTTP_OK);
        }
        catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
    }

    #[Route('/{id}', name: 'app_administrateur_delete', methods: ['DELETE'])]
    public function delete($id,Request $request, AdministrateurRepository $administrateurRepository, EntityManagerInterface $entityManager): Response
    {
        try {
            $administrateur = $administrateurRepository->find($id);
            if ($administrateur === null) {
                throw new \Exception('User not found');
            }
            $entityManager->remove($administrateur);
            $entityManager->flush();
            return new JsonResponse(['status' => 'User deleted'], Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
