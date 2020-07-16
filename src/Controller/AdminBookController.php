<?php


namespace App\Controller;

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

}
