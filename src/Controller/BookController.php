<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\BookRepository;
use App\Entity\Book;

class BookController extends AbstractController
{
    /**
     * @Route("/book", name="book")
     */
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }

    /**
     * @Route("/book", name="create_book")
     */
    public function createBook(string $title, string $author, string $isbn, string $imageName): Response
    {
        // you can fetch the EntityManager via $this->getDoctrine()
        // or you can add an argument to the action: createBook(EntityManagerInterface $entityManager)
        $entityManager = $this->getDoctrine()->getManager();

        $book = new Book();
        $book->setTitle($title);
        $book->setAuthor($author);
        $book->setIsbn($isbn);
        $book->setImage($imageName);


        // tell Doctrine you want to (eventually) save the book (no queries yet)
        $entityManager->persist($book);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new book with id ' . $book->getId());
    }

    /**
     * @Route("/book/{id}", name="book_show")
     */
    public function show(int $bookid): Response
    {
        $book = $this->getDoctrine()
            ->getRepository(Book::class)
            ->find($bookid);

        if (!$book) {
            throw $this->createNotFoundException(
                'No book found for bookid ' . $bookid
            );
        }

        return new Response('Check out this great book: ' . $book->getTitle());

        // or render a template
        // in the template, print things with {{ book.name }}
        // return $this->render('book/show.html.twig', ['book' => $book]);
    }

    /**
     * @Route("/books", name="show_books")
     */
    public function showAllBooks(BookRepository $booksRepository): Response
    {
        $books = $booksRepository->findAll();
        $allBooksInfo = array();
        foreach ($books as $book) {
            array_push($allBooksInfo, $book->getAllInfo());
        }

        $data = [
            "header" => "Recommended in New Yorker",
            "books" => $allBooksInfo,
        ];

        return $this->render('books.html.twig', $data);
    }
}
