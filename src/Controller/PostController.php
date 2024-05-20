<?php

namespace App\Controller;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use App\Entity\Post;
use App\Entity\File;
use App\Entity\User;

use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
#[Route('/post')]
class PostController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository, HttpClientInterface $client,SerializerInterface $serializer ): JsonResponse
    {
        try
            {$data = $postRepository->findAll();
            $response = $serializer->serialize($data, 'json');
            return new JsonResponse($response, 200, [], true);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


// #[Route('/new', name: 'app_post_new', methods: ['GET', 'POST'])]
// public function new(Request $request): Response
// {
//     $post = new Post();
//     $form = $this->createForm(PostType::class, $post);
//     $form->handleRequest($request);

//     if ($form->isSubmitted() && $form->isValid()) {
//         // Handle file upload
//         $uploadedFiles = $request->files->get('post')['files'];
//         $isPublic = $form->get('estPublie')->getData();
//         $selectedUser = $form->get('user')->getData();
//         $post->setUser($selectedUser);
//         // Set 'estPublie' property based on checkbox value
//         $post->setEstPublie($isPublic);
//         foreach ($uploadedFiles as $uploadedFile) {
//             // Create a new File entity for each uploaded file
//             $file = new File();
//             // Handle file upload logic and set properties of File entity
//             // For example:
//             $fileName = md5(uniqid()) . '.' . $uploadedFile->guessExtension();
//             $uploadedFile->move($this->getParameter('uploads_directory'), $fileName);
//             $file->setFileName($fileName);
//             // Add the file to the Post entity
//             $post->addFile($file);

//             // Persist the new File entity
//             $this->entityManager->persist($file);
//         }

//         // Persist the Post entity
//         $this->entityManager->persist($post);
//         $this->entityManager->flush();

//         return $this->redirectToRoute('app_post_index');
//     }

//     return $this->render('post/new.html.twig', [
//         'post' => $post,
//         'form' => $form->createView(),
//     ]);
// }



#[Route('/new', name: 'app_post_new', methods: ['POST'])]
public function new(Request $request, UserRepository $userRepository): JsonResponse
    {
        try {
            $post = new Post();
            $post->setContenu($request->get('contenu'));
            $post->setPublished(new \DateTime());
            $post->setEstPublie($request->get('estPublie'));

            $user = $userRepository->find($request->get('user'));
            if (!$user) {
                throw new \Exception('User not found');
            }
            $post->setUser($user);
            $post->setFile($request->get('file'));
            // $uploadedFiles = $request->files->get('files');
            // if ($uploadedFiles) {
            //     foreach ($uploadedFiles as $uploadedFile) {
            //         if ($uploadedFile instanceof UploadedFile) {
            //             $file = new File();
            //             $fileName = md5(uniqid()) . '.' . $uploadedFile->guessExtension();

            //             // Save to Symfony directory
            //             $symfonyUploadsDir = $this->getParameter('symfony_uploads_directory');
            //             $uploadedFile->move($symfonyUploadsDir, $fileName);

            //             // Copy to Angular directory
            //             $angularAssetsDir = $this->getParameter('angular_assets_directory');
            //             copy($symfonyUploadsDir . '/' . $fileName, $angularAssetsDir . '/' . $fileName);

            //             $file->setFileName($fileName);
            //             $post->addFile($file);
            //             $this->entityManager->persist($file);
            //         }
            //     }
            // }


            $this->entityManager->persist($post);
            $this->entityManager->flush();

            return new JsonResponse(['status' => 'Post created'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'app_post_show', methods: ['GET'])]
    public function show($id, PostRepository $postRepository, SerializerInterface $serializer): JsonResponse
    {
        try {
            $post = $postRepository->find($id);
            if ($post === null) {
                return $this->json(['error' => 'Post not found'], Response::HTTP_NOT_FOUND);
            }
            $response = $serializer->serialize($post, 'json');
            return new JsonResponse($response, Response::HTTP_OK, [], true);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['PUT'])]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $post->setContenu($data['contenu']);
            $post->setEstPublie($data['estPublie']);
            $entityManager->flush();
            return $this->json(['status' => 'Post updated'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'app_post_delete', methods: ['DELETE'])]
    public function delete($id, Request $request, PostRepository $postRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $post = $postRepository->find($id);
            if ($post === null) {
                throw new \Exception('Post not found');
            }
            $entityManager->remove($post);
            $entityManager->flush();
            return new JsonResponse(['status' => 'Post deleted'], Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // #[Route('/{postId}/comments', name: 'app_post_comments', methods: ['GET'])]
    // public function comments(int $postId, CommentRepository $commentRepository): JsonResponse
    // {
    //     try {
    //         $post = $this->getDoctrine()->getRepository(Post::class)->find($postId);
    //         if (!$post) {
    //             return new JsonResponse(['error' => 'Post not found'], Response::HTTP_NOT_FOUND);
    //         }

    //         $comments = $post->getComments();

    //         $data = [];
    //         foreach ($comments as $comment) {
    //             $data[] = [
    //                 'id' => $comment->getId(),
    //                 'content' => $comment->getContent(),
    //                 // Add other properties as needed
    //             ];
    //         }

    //         return new JsonResponse($data, Response::HTTP_OK);
    //     } catch (\Exception $e) {
    //         return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    //     }
    // }
}