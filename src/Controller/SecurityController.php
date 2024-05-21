<?php

namespace App\Controller;


use App\Entity\User;
use Doctrine\ORM\EntityManager;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Guard\JWTTokenAuthenticator;


#[Route('/api')]
class SecurityController extends AbstractController
{
    private UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface $em;
    private UserRepository $userRepo;
    private JWTTokenManagerInterface $JWTManager;

    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em, UserRepository $userRepo,JWTTokenManagerInterface $JWTManager)
    {
        $this->passwordHasher = $passwordHasher;
        $this->em = $em;
        $this->userRepo = $userRepo;
        $this->JWTManager = $JWTManager;
    }
    #[Route('/register', name: 'app_register', methods:['POST'])]
    public function register(Request $request): Response
    {
        // Vérifiez les clés manquantes
        $requiredKeys = ['email', 'username', 'profileImage', 'password'];
        $data = [];
    
        if (!$this->validateRequest($request, $requiredKeys, $data)) {
            return $this->json(['error' => 'Données incomplètes'], Response::HTTP_BAD_REQUEST);
        }
    
        $user = new User();
        $user->setEmail($data['email']);
        $user->setUsername($data['username']);
        $user->setPassword($data['password']);  
    
        if ($data['profileImage']) {
            $imageFile = $data['profileImage'];
          
            $user->setImageFile($imageFile);
            $user->setImageName($imageFile->getClientOriginalName());  // Utilise le nom complet avec extension
            $user->setImageSize($imageFile->getSize());
        } else {
            return $this->json(['error' => 'Le fichier envoyé doit être une image valide.'], Response::HTTP_BAD_REQUEST);
        }
    
        // Enregistrement de l'utilisateur
        try {
            $this->em->persist($user);
            $this->em->flush();
            return $this->json(['message' => 'Utilisateur enregistré avec succès']);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Erreur du serveur', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    private function validateRequest(Request $request, array $requiredKeys, array &$data): bool
    {
        $contentType = $request->headers->get('Content-Type');
    
        if (strpos($contentType, 'application/json') !== false) {
            $data = json_decode($request->getContent(), true);
        } elseif (strpos($contentType, 'multipart/form-data') !== false) {
            $data = $request->request->all();
            if ($request->files->has('profileImage')) {
                $data['profileImage'] = $request->files->get('profileImage');
            }
        }
    
        return empty(array_diff($requiredKeys, array_keys($data)));
    }
    #[Route('/login', name: 'app_login', methods:['POST'] )]
    public function login(Request $request): JsonResponse
    {
        $data =  json_decode($request->getContent(), true);

        $user = $this->userRepo->findOneBy(['email' => $data['email']]);

        if(!$user || !$this->passwordHasher->isPasswordValid($user, $data['password'])){
            return new JsonResponse(['error' => 'Email ou mot de passe incorrect'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $token = $this->JWTManager->create($user);

        $response = new JsonResponse([
             'message' => 'Connexion réussie'
        ]);

        $response->headers->setCookie(new Cookie('BEARER', $token, time() * 3600, '/', null, true, true));
        return $response;
    }
    #[Route('/logout', name: 'app_logout', methods:['POST'] )]
    public function logout(): JsonResponse
    {
        $response = new JsonResponse([
            'message' => 'Déconnexion réussie'
       ]);

       $response->headers->clearCookie('BEARER');
       return $response;


    }
}
