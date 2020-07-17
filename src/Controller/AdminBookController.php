<?php


namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

        return $this->render('admin/admin_books.html.twig', [
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
     * @Route("/admin/book/insert", name="admin_book_insert")
     */
    public function AdminInsertBook(
        Request $request,
        EntityManagerInterface $entityManager
    )
    {
        // je créé une nouvelle instance de l'entité Book
        $book = new Book();

        // je récupère le gabarit de formulaire de
        // l'entité Book, créé avec la commande make:form
        // et je le stocke dans une variable $bookForm
        $bookForm = $this->createForm(BookType::class, $book);

        // on prend les données de la requête (classe Request)
        //et on les "envoie" à notre formulaire
        $bookForm->handleRequest($request);

        // si le formulaire a été envoyé et que les données sont valides
        // par rapport à celles attendues alors je persiste le livre
        if ($bookForm->isSubmitted() && $bookForm->isValid() ) {
            $entityManager->persist($book);
            $entityManager->flush();

            return $this->redirectToRoute('admin_books');
        }

        // je retourne mon fichier twig, en lui envoyant
        // la vue du formulaire, générée avec la méthode createView()
        return $this->render('admin/admin_book_insert.html.twig', [
            'bookForm' => $bookForm->createView()
        ]);
    }


    /**
     * @Route("/admin/book/update/{id}", name="admin_book_update")
     */
    public function AdminUpdateBook(
        BookRepository $bookRepository,
        Request $request,
        EntityManagerInterface $entityManager,
        $id
    )
    {
        $book = $bookRepository->find($id);

        $bookForm = $this->createForm(BookType::class, $book);

        $bookForm->handleRequest($request);

        if ($bookForm->isSubmitted() && $bookForm->isValid()) {
            $entityManager->persist($book);
            $entityManager->flush();

            return $this->redirectToRoute('admin_books');
        }

        return $this->render('admin/admin_book_update.html.twig', [
            'bookForm' => $bookForm->createView()
        ]);
    }

}
