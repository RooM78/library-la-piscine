<?php


namespace App\Controller;

use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{

    /**
     * @Route("/authors", name="authors_list")
     */
    // je demande à Symfony de m'instancier la classe AuthorRepository
    // avec le mécanisme d'Autowire (je passe en paramètre de la méthode
    // la classe voulue suivie d'une variable dans laquelle je veux que Symfony m'instancie ma classe
    // l'authorRepository est la classe qui permet de faire des requêtes SELECT
    // dans la table authors
    public function AuthorsList(AuthorRepository $authorRepository)
    {

        // j'utilise l'authorRepository et la méthode findAll() pour récupérer tous les éléments
        // de ma table authors
        $authors = $authorRepository->findAll();

        return $this->render('authors.html.twig', [
           'authors' => $authors
        ]);
    }

    /**
     * @Route("/author/{id}", name="author_show")
     */
    public function AuthorShow(AuthorRepository $authorRepository, $id)
    {
        // on utilise la méthode find du repository pour récupérer un
        // auteur dans la base de données en fonction de son id
        $author = $authorRepository->find($id);

        dump($author); die;
    }


    /**
     * @Route("/books", name="books_list")
     */
    // je demande à Symfony de m'instancier la classe AuthorRepository
    // avec le mécanisme d'Autowire (je passe en paramètre de la méthode
    // la classe voulue suivie d'une variable dans laquelle je veux que Symfony m'instancie ma classe
    // l'authorRepository est la classe qui permet de faire des requêtes SELECT
    // dans la table authors
    public function BooksList(BookRepository $bookRepository)
    {

        // j'utilise l'bookRepository et la méthode findAll() pour récupérer tous les éléments
        // de ma table books
        $books = $bookRepository->findAll();

        dump($books); die;

        return $this->render('books.html.twig', [
            'books' => $books
        ]);
    }

    /**
     * @Route("/book/{id}", name="book_show")
     */
    public function BookShow(BookRepository $bookRepository, $id)
    {
        // on utilise la méthode find du repository pour récupérer un
        // auteur dans la base de données en fonction de son id
        $book = $bookRepository->find($id);

        dump($book); die;
    }

    /**
     * @Route("/books/genre/{genre}", name="books_genre")
     */
    public function BooksByGenre(BookRepository $bookRepository, $genre)
    {
        // J'utilise le bookRepository et sa méthode findBy
        // pour trouver un ou plusieurs livres en BDD
        // en fonction de la valeur d'une colonne
        $books = $bookRepository->findBy(['genre' => $genre]);

        return $this->render('books_genre.html.twig', [
            'books' => $books,
            'genre' => $genre
        ]);
    }

    /**
     * @Route("/books/search/resume", name="books_search_resume")
     */
    public function BooksSearchByResume(
        BookRepository $bookRepository,
        Request $request
    )
    {
        // J'utilise la classe Request pour récupérer la valeur
        // du parametre d'url "search" (envoyé par le formulaire)
        $word = $request->query->get('search');

        // j'initilise une variable $books avec un tableau vide
        // pour ne pas avoir d'erreur si je n'ai pas de parametre d'url de recherche
        // et que donc ma méthode de répository n'est pas appelée
        $books = [];

        //  si j'ai des parametres d'url de recherche (donc que mon utilisateur
        // a fait une recherche
        if (!empty($word)) {
            // s'il a fait une recherche, je créé une requête SELECT
            // pour trouver les livres que l'utilisateur a recherché
            $books = $bookRepository->findByWordsInResume($word);
        }

        // j'appelle mon fichier twig avec les books trouvés en BDD
        return $this->render('search.html.twig', [
           'books' => $books
        ]);

    }

    /**
     * @Route("books/insert", name="books_insert")
     */
    public function insertBook(EntityManagerInterface $entityManager)
    {
        // les entités font le lien avec les tables
        // donc pour créer un enregistrement dans ma table book
        // je créé une nouvelle instance de l'entité Book
        $book = new Book();
        // je lui donne les valeurs des colonnes avec les setters
        $book->setTitle("La peau sur les os");
        $book->setGenre("horror");
        $book->setNbPages(400);
        $book->setResume('blablabla');

        // j'utilise l'entityManager pour que Doctrine
        // m'enregistre le livre créé avec la méthode persist()
        // puis je "valide" l'enregistrement avec la méthode flush()
        $entityManager->persist($book);
        $entityManager->flush();
    }

}
