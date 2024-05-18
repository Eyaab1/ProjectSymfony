<?php

namespace App\Controller;

use App\Entity\Enseignant;
use App\Form\EnseignantType;
use App\Repository\EnseignantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/enseignant')]
class EnseignantController extends AbstractController
{
    #[Route('/', name: 'app_enseignant_index', methods: ['GET'])]
    public function index(EnseignantRepository $enseignantRepository,HttpClientInterface $client,SerializerInterface $serializer ): Response
    {
        try
            {$data = $enseignantRepository->findAll();
            $response = $serializer->serialize($data, 'json');
            return new JsonResponse($response, 200, [], true);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/new', name: 'app_enseignant_new', methods: [ 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        try{
            
            $data = json_decode($request->getContent(), true);
            $user = new Enseignant();
            $user->setNom($data['nom']);
            $user->setPrenom($data['prenom']);
            $user->setEmail($data['email']);
            $user->setNumtel($data['numtel']);
            $user->setPassword($data['password']);
            $user->setType('enseignant');
            $user->setRoles(['ROLE_USER']);
            $user->setCodeENS($data['codeENS']);
            $user->setNbAnneeExp(($data['nbAnneeExp']));
            $user->setMatiere($data['matiere']);
            $entityManager->persist($user);
            $entityManager->flush();

            return new JsonResponse(['status' => 'User created'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'app_enseignant_show', methods: ['GET'])]
    public function show($id,EnseignantRepository $enseignantRepository, SerializerInterface $serializer): Response
    {
        try {
            $user = $enseignantRepository->find($id);
            if ($user === null) {
                return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
            }
            $response = $serializer->serialize($user, 'json');
            return new JsonResponse($response, 200, [], true);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}/edit', name: 'app_enseignant_edit', methods: ['PUT'])]
    public function edit(Request $request, Enseignant $enseignant, EntityManagerInterface $entityManager): Response
    {
        try{
            $data = json_decode($request->getContent(), true);
            $enseignant->setNom($data['nom']);
            $enseignant->setPrenom($data['prenom']);
            $enseignant->setNumtel($data['numtel']);
            $enseignant->setMatiere($data['matiere']);
            $enseignant->setNbAnneeExp($data['nbAnneeExp']);
            $entityManager->flush();
            return new JsonResponse(['status' => 'Etudiant updated'], Response::HTTP_OK);
        }
        catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    }}

    #[Route('/{id}', name: 'app_enseignant_delete', methods: ['DELETE'])]
    public function delete($id, EnseignantRepository $enseignantRepository, EntityManagerInterface $entityManager): Response
    {
        try {
            $etudiant = $enseignantRepository->find($id);
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
