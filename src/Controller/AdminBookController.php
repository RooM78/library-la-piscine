<?php


namespace App\Controller;

use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminBookController extends AbstractController
{

    /**
     * @Route("/admin/books", name="admin_books")
     */
    public function AdminBooks(BookRepository $bookRepository)
    {
        $books = $bookRepository->findAll();

        return $this->render('admin_books.html.twig', [
           'books' => $books
        ]);
    }

    /**
     * @Route("/admin/books/delete/{id}", name="admin_book_delete")
     */
    public function AdminBookDelete(
        BookRepository $bookRepository,
        EntityManagerInterface $entityManager,
        $id
    )
    {
        $book = $bookRepository->find($id);

        $entityManager->remove($book);
        $entityManager->flush();

        return $this->redirectToRoute('admin_books');
    }

    /**
     * @Route("/admin/books/insert", name="admin_books_insert")
     */
    public function AdminInsertBook()
    {
        // je récupère le gabarit de formulaire de
        // l'entité Book, créé avec la commande make:form
        // et je le stocke dans une variable $bookForm
        $bookForm = $this->createForm(BookType::class);

        // je retourne mon fichier twig, en lui envoyant
        // la vue du formulaire, générée avec la méthode createView()
        return $this->render('admin_books_insert.html.twig', [
            'bookForm' => $bookForm->createView()
        ]);
    }

}
