<?php

namespace App\Controller;

use App\Security\AppRoles;
use App\Enum\UserTypes;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository, HttpClientInterface $client, SerializerInterface $serializer): Response
    {
        try {
            $data = $userRepository->findAll();
            $response = $serializer->serialize($data, 'json');
            return new JsonResponse($response, 200, [], true);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/new', name: 'app_user_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        try {
            $data = json_decode($request->getContent(), true);

            // Validate required fields
            if (empty($data['nom']) || empty($data['prenom']) || empty($data['email']) || empty($data['password']) || empty($data['numtel']) || empty($data['type'])) {
                return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
            }

            $user = new User();
            $user->setNom($data['nom']);
            $user->setPrenom($data['prenom']);
            $user->setEmail($data['email']);
            $user->setNumtel($data['numtel']);
            $user->setPassword($data['password']); // No hashing
            $user->setType($data['type']);

            $entityManager->persist($user);
            $entityManager->flush();

            return new JsonResponse(['status' => 'User created'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show($id, UserRepository $userRepository, SerializerInterface $serializer): Response
    {
        try {
            $user = $userRepository->find($id);
            if ($user === null) {
                return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
            }
            $response = $serializer->serialize($user, 'json');
            return new JsonResponse($response, 200, [], true);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['DELETE'])]
    public function delete($id, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        try {
            $user = $userRepository->find($id);
            if ($user === null) {
                throw new \Exception('User not found');
            }
            $entityManager->remove($user);
            $entityManager->flush();
            return new JsonResponse(['status' => 'User deleted'], Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/email', name: 'app_user_get_by_email', methods: ['GET'])]
    public function getUserByEmail(Request $request, UserRepository $userRepository, SerializerInterface $serializer): Response
    {
        try {
            $email = $request->query->get('email');
            $user = $userRepository->findOneBy(['email' => $email]);
            if ($user === null) {
                return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
            }
            $response = $serializer->serialize($user, 'json');
            return new JsonResponse($response, 200, [], true);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
<<<<<<< Updated upstream

=======
    
>>>>>>> Stashed changes
}
