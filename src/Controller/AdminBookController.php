<?php


namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

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
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
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


            // vu que le champs bookCover de mon formulaire est en mapped false
            // je gère moi même l'enregistrment de la valeur de cet input
            // https://symfony.com/doc/current/controller/upload_file.html

            // je récupère l'image uploadée
            $bookCoverFile = $bookForm->get('bookCover')->getData();

            // s'il y a bien une image uploadée dans le formulaire
            if ($bookCoverFile) {

                // je récupère le nom de l'image
                $originalCoverName = pathinfo($bookCoverFile->getClientOriginalName(), PATHINFO_FILENAME);

                // et grâce à son nom original, je gènère un nouveau qui sera unique
                // pour éviter d'avoir des doublons de noms d'image en BDD
                $safeCoverName = $slugger->slug($originalCoverName);
                $uniqueCoverName = $safeCoverName . '-' . uniqid() . '.' . $bookCoverFile->guessExtension();


                // j'utilise un bloc de try and catch
                // qui agit comme une conditions, mais si le bloc try échoue, ça
                // soulève une erreur, qu'on peut gérer avec le catch
                try {

                    // je prends l'image uploadée
                    // et je la déplace dans un dossier (dans public) + je la renomme avec
                    // le nom unique générée
                    // j'utilise un parametre (défini dans services.yaml) pour savoir
                    // dans quel dossier je la déplace
                    // un parametre = une sorte de variable globale
                    $bookCoverFile->move(
                        $this->getParameter('book_cover_directory'),
                        $uniqueCoverName
                    );
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }


                // je sauvegarde dans la colonne bookCover le nom de mon image
                $book->setBookCover($uniqueCoverName);
            }

            $entityManager->persist($book);
            $entityManager->flush();

            $this->addFlash('success', 'Votre livre a été créé !');

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


    /**
     * @Route("/admin/book/insertwithgenre", name="admin_book_insert_genre")
     */
    public function InsertBookWithGenre(
        GenreRepository $genreRepository,
        EntityManagerInterface $entityManager
    )
    {

        // je récupère un genre en bdd avec le genreRepository
        // et la méthode find()
        $genre = $genreRepository->find(2);

        $book = new Book();

        $book->setTitle('titre blabla');
        $book->setNbPages(450);
        $book->setResume('blavklbkakvk');
        // je créé la relation entre mon book et mon genre
        $book->setGenre($genre);

        $entityManager->persist($book);
        $entityManager->flush();

        return new Response('livre enregistré');
    }

}
