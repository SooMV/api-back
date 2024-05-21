<?php

namespace App\Controller;

use App\Entity\Film;
use App\Repository\FilmRepository;
use App\Repository\LikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommentaireRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Loader\Configurator\serializer;

#[Route('/api')]
class FilmController extends AbstractController
{
    private $filmRepo;
    private $commentRepo;
    private $likeRepo;
    private $em;

    public function __construct(FilmRepository $filmRepo,CommentaireRepository $commentRepo,LikeRepository $likeRepo,EntityManagerInterface $em){
        $this->filmRepo = $filmRepo;
        $this->commentRepo = $commentRepo;
        $this->likeRepo = $likeRepo;
        $this->em = $em;
    }

       #[Route('/movies', name: 'get_movies', methods: ['GET'])]
        public function getAllMovies(SerializerInterface $serializer): Response
        {
            $movies = $this->filmRepo->findAll();

            $serializedMovies = $serializer->serialize($movies, 'json', ['groups' => ['filmGroup', 'commentGroup'] ]);

            return new Response($serializedMovies, 200, ['Content-Type' => 'application/json']);
        }

        #[Route('/film/{id}', name: 'show_film', methods:['GET'])]
        public function getFilm(SerializerInterface $serializer, $id): JsonResponse
        {
            $film = $this->filmRepo->find($id);
           
            if(!$film){
                return $this->json(['message' => 'Film not found'], Response::HTTP_NOT_FOUND);
            }
        
            $comments = $film->getCommentaires(); 
            $likes = $film->getLikes(); 
        
            $filmJson = $serializer->serialize($film, 'json', ['groups' => ['filmGroup', 'commentGroup']]);
        
            return new JsonResponse($filmJson, Response::HTTP_OK, [], true);
        }

    
        #[Route('/add_film', name: 'create_film', methods:['POST'])]
        public function createFilm(Request $request): JsonResponse
        {
            $data = json_decode($request->getContent(), true);
            
            if (!isset($data['title'], $data['duration'], $data['realisateur_first_name'], $data['realisateur_last_name'], $data['release_year'])) {
                return $this->json(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
            }
            
            if (empty($data['title'])) {
                $missingFields[] = 'title';
            }
            if (empty($data['duration'])) {
                $missingFields[] = 'duration';
            }
            if (empty($data['realisateur_first_name'])) {
                $missingFields[] = 'realisateur_first_name';
            }
            if (empty($data['realisateur_last_name'])) {
                $missingFields[] = 'realisateur_last_name';
            }
            if (empty($data['release_year'])) {
                $missingFields[] = 'release_year';
            }

            if (!empty($missingFields)) {
                return $this->json([
                    'error' => 'Missing required fields',
                    'missing_fields' => $missingFields
                ], Response::HTTP_BAD_REQUEST);
            }

            $film = new Film();
            $film->setTitle($data['title']);
            $film->setRealisateurFirstName($data['realisateur_first_name']);
            $film->setRealisateurLastName($data['realisateur_last_name']);
            $film->setDuration($data['duration']);
            $film->setReleaseYear($data['release_year']);
        
            $this->em->persist($film);
            $this->em->flush();

            return $this->json($film);
        }
        

        #[Route('/film/{id}', name: 'update_film', methods:['PUT'])]
        public function updateFilm($id, Request $request, SerializerInterface $serializer): JsonResponse
        {
            $data = json_decode($request->getContent(),true);

            $film = $this->filmRepo->find($id);
            
            if(!$film){
                return $this->json(['message' => 'Film not found'], Response::HTTP_NOT_FOUND);
            }

            $film->setTitle($data['title'] ?? $film->getTitle());
            $film->setDuration($data['duration'] ?? $film->getDuration());
            $film->setRealisateurFirstName($data['director_FistName'] ?? $film->getRealisateurFirstName());
            $film->setRealisateurLastName($data['director_LastName'] ?? $film->getRealisateurLastName());
            $film->setReleaseYear($data['release_year'] ?? $film->getReleaseYear());
            $film->setDescription($data['description'] ?? $film->getDescription());

            $this->em->persist($film);
            $this->em->flush();

            $jsonContent = $serializer->serialize($film, 'json', [
                AbstractNormalizer::GROUPS => ['filmUpdate']
            ]);
            
            return new JsonResponse ($jsonContent, Response::HTTP_OK, [],true);
        }
        #[Route('/film/{id}', name: 'delete_film', methods: ['DELETE'])]
        public function deleteFilm($id): JsonResponse
        {
            $film = $this->filmRepo->find($id);

            if (!$film) {
                return $this->json(['message' => 'Film not found'], Response::HTTP_NOT_FOUND);
            }
            
            foreach ($film->getCommentaires() as $commentaire) {
                $this->em->remove($commentaire);
            }

            $this->em->remove($film);
            $this->em->flush();

            return new JsonResponse(['message' => 'Film et commentaires associés supprimés'], Response::HTTP_OK);
        }
}
