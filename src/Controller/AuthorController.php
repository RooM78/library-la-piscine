<?php


namespace App\Controller;

use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
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
    public function BooksSearchByResume(BookRepository $bookRepository)
    {
        $bookRepository->findByWordsInResume();
    }

}
