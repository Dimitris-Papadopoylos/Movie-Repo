<?php

namespace App\Controller;



use App\Entity\Movie;
use App\Form\MovieFromType;

;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Expr\New_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
//use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MoviesController extends AbstractController
{
    private $em;
    private $movieRepository;
    public function __construct(MovieRepository $movieRepository, EntityManagerInterface $em)
    {
        $this->movieRepository = $movieRepository;
        $this->em = $em;
    }

    #[Route('/movies/create', methods: ['GET','POST'], name: 'create_movies')]
    public function create(Request $request): Response
    {
        $movie = new Movie();
        $form = $this->createForm(MovieFromType::class, $movie);

        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $newMovie = $form->getData();
           
            $imagePath = $form->get('imagePath')->getData();
            if($imagePath){
                $newFileName = uniqid() . '.' . $imagePath->guessExtension();
                try{
                    $imagePath->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads',
                        $newFileName
                    );
                }catch(FileException $e){
                    return new Response($e->getMessage());
                }

                $newMovie->setImagePath('/uploads/' . $newFileName);
            }

            $this->em->persist($newMovie);
            $this->em->flush();

            $this->redirectToRoute('app_movies');

        }

        return $this->render('movies/create.html.twig',[
            'form' => $form->createView()
        ]);
    } 


    #[Route('/movies/edit/{id}', name:'edit_movie')]
    public function edit($id,Request $request):Response
    {   
        $movie = $this->movieRepository->find($id);
        $form = $this->createForm(MovieFromType::class, $movie);

        $form->handleRequest($request);
        $imagePath = $form->get('imagePath')->getData();

        if($form->isSubmitted() && $form->isValid()){
            if($imagePath){
                if($movie->getImagePath() !== null){
                    if(file_exists($this->getParameter('kernel.project_dir') . $movie->getImagePath())){
                        $this->GetParameter('kernel.project_dir') . $movie->getImagePath();
                        $newFileName = uniqid() . '.' . $imagePath->guessExtension();
                        try{
                            $imagePath->move(
                                $this->getParameter('kernel.project_dir') . '/public/uploads',
                                $newFileName
                            );
                        }catch(FileException $e){
                            return new Response($e->getMessage());
                        }
                       
                        $movie->setImagePath('/uploads/' . $newFileName);
                        $this->em->flush();

                        return $this->redirectToRoute('app_movies');
                    }
                }
            }else{
                $movie->setTitle($form->get('title')->getData());
                $movie->setReleaseYear($form->get('releaseYear')->getData());
                $movie->setDescription($form->get('Description')->getData());

                $this->em->flush();
                return $this->redirectToRoute('app_movies');
            }
        }
        return $this->render('movies/edit.html.twig', [
            'movie' => $movie,
            'form' => $form->createView()
        ]);
    }

    #[Route('/movies/delete/{id}', methods:['GET','DELETE'], name:'delete_movie')]
    public function delete($id):Response
    {
        $movie = $this->movieRepository->find($id);
        $this->em->remove($movie);
        $this->em->flush();

        return $this->redirectToRoute('app_movies');
    }

    #[Route('/movies', methods: ['GET'], name: 'app_movies')]
    public function index(): Response
    {
        $movies = $this->movieRepository->findAll();
    
        return $this->render('movies/index.html.twig', [
            'movies' => $movies
        ]);
    }
    
    #[Route('/movies/{id}', methods: ['GET'] ,name: 'show_movies')]
    public function show($id): Response
    {
        $movie = $this->movieRepository->find($id);
        $released = $movie->getReleaseYear();

        $SimilarMovies =  $this->movieRepository->findSimilarMovies($released, $id);

        return $this->render('movies/show.html.twig', [
            'movies' => $movie,
            'similars' => $SimilarMovies
        ]);
    }

}
    

// findAll()- Select * From movies

        // find()- Select * From movies Where id=5
        // findBy()- Select * From movies ORDER BY id DESC
        // findOneBy()- Select * From movies WHERE id=5 AND title='The Dark Knigh' ORDER BY DESC