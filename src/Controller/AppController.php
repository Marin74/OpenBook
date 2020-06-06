<?php
namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Genre;
use App\Util\DataUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class AppController extends AbstractController
{
    private const LIMIT = 200;

    /**
    * @Route("/books", methods={"GET","HEAD"})
    */
    public function books(Request $request, DataUtils $dataUtils): Response
    {
        $em = $this->getDoctrine()->getManager();
        $repoBook = $em->getRepository(Book::class);

        $page = $request->get("page");
        $page = intval($page);
        if(!empty($page) && $page > 0) {
            $page--;
        }

        $books = $repoBook->findBy(array(), array("title" => "ASC"), self::LIMIT, $page);

        // https://symfony.com/doc/current/components/http_foundation.html#creating-a-json-response
        $response = new JsonResponse(array(
            "books" => $dataUtils->getBooks($books)
        ));
        $response->headers->set("Access-Control-Allow-Origin", "*");
        return $response;
    }

    /**
     * @Route("/books/{id<\d+>}", methods={"GET","HEAD"})
     */
    public function book(Request $request, DataUtils $dataUtils): Response
    {
        $em = $this->getDoctrine()->getManager();
        $repoBook = $em->getRepository(Book::class);

        $book = $repoBook->find($request->get("id"));

        // https://symfony.com/doc/current/components/http_foundation.html#creating-a-json-response
        $response = new JsonResponse(array(
            "book" => $dataUtils->getBook($book)
        ));
        $response->headers->set("Access-Control-Allow-Origin", "*");
        return $response;
    }

    /**
     * @Route("/books/new", methods={"POST"})
     */
    public function createBook(Request $request, DataUtils $dataUtils): Response
    {
        $em = $this->getDoctrine()->getManager();
        $repoGenre = $em->getRepository(Genre::class);

        $errors = array();
        $book = null;

        $res = $dataUtils->setBookFromRequest($request, new Book(), $repoGenre);

        if($res instanceof Book) {
            $book = $res;
            $em->persist($book);
            $em->flush();
        }
        else {
            $errors = $res;
        }

        // https://symfony.com/doc/current/components/http_foundation.html#creating-a-json-response
        $response = null;
        if(count($errors) > 0) {
            $response = new JsonResponse(array(
                "errors"    => $errors
            ));
        }
        else {
            $response = new JsonResponse(array(
                "book" => $dataUtils->getBook($book)
            ));
        }

        $response->headers->set("Access-Control-Allow-Origin", "*");
        return $response;
    }

    /**
     * @Route("/books/{id<\d+>}", methods={"PUT"})
     */
    public function updateBook(Request $request, DataUtils $dataUtils, TranslatorInterface $translator): Response
    {
        $em = $this->getDoctrine()->getManager();
        $repoBook = $em->getRepository(Book::class);
        $repoGenre = $em->getRepository(Genre::class);

        $errors = array();

        $id = $request->get("id");
        $book = $repoBook->find($id);

        if(!$book) {
            $errors[] = $translator->trans("book_not_found");
        }
        else {

            $res = $dataUtils->setBookFromRequest($request, $book, $repoGenre);

            if($res instanceof Book) {
                $book = $res;
                $em->flush();
            }
            else {
                foreach($res as $error) {
                    $errors[] = $error;
                }
            }
        }

        // https://symfony.com/doc/current/components/http_foundation.html#creating-a-json-response
        $response = null;
        if(count($errors) > 0) {
            $response = new JsonResponse(array(
                "errors"    => $errors
            ));
        }
        else {
            $response = new JsonResponse(array(
                "book" => $dataUtils->getBook($book)
            ));
        }

        $response->headers->set("Access-Control-Allow-Origin", "*");
        return $response;
    }

    /**
     * @Route("/authors", methods={"GET","HEAD"})
     */
    public function authors(Request $request, DataUtils $dataUtils): Response
    {
        $em = $this->getDoctrine()->getManager();
        $repoAuthor = $em->getRepository(Author::class);

        $page = $request->get("page");
        $page = intval($page);
        if(!empty($page) && $page > 0) {
            $page--;
        }

        $authors = $repoAuthor->findBy(array(), array("name" => "ASC"), self::LIMIT, $page);

        // https://symfony.com/doc/current/components/http_foundation.html#creating-a-json-response
        $response = new JsonResponse(array(
            "authors" => $dataUtils->getAuthors($authors)
        ));
        $response->headers->set("Access-Control-Allow-Origin", "*");
        return $response;
    }

    /**
     * @Route("/authors/{id<\d+>}", methods={"GET","HEAD"})
     */
    public function author(Request $request, DataUtils $dataUtils): Response
    {
        $em = $this->getDoctrine()->getManager();
        $repoAuthor = $em->getRepository(Author::class);

        $author = $repoAuthor->find($request->get("id"));

        // https://symfony.com/doc/current/components/http_foundation.html#creating-a-json-response
        $dataResponse = array(
            "author" => $dataUtils->getAuthor($author),
        );
        if($author != null && count($author->getBooks()) > 0) {
            $dataResponse["books"] = $dataUtils->getBooksFromCollection($author->getBooks());
        }
        $response = new JsonResponse($dataResponse);
        $response->headers->set("Access-Control-Allow-Origin", "*");
        return $response;
    }

    /**
     * @Route("/authors/new", methods={"POST"})
     */
    public function createAuthor(Request $request, DataUtils $dataUtils): Response
    {
        $em = $this->getDoctrine()->getManager();

        $errors = array();
        $author = null;

        $res = $dataUtils->setAuthorFromRequest($request, new Author());

        if($res instanceof Author) {
            $author = $res;
            $em->persist($author);
            $em->flush();
        }
        else {
            $errors = $res;
        }

        // https://symfony.com/doc/current/components/http_foundation.html#creating-a-json-response
        $response = null;
        if(count($errors) > 0) {
            $response = new JsonResponse(array(
                "errors"    => $errors
            ));
        }
        else {
            $response = new JsonResponse(array(
                "author" => $dataUtils->getAuthor($author)
            ));
        }

        $response->headers->set("Access-Control-Allow-Origin", "*");
        return $response;
    }

    /**
     * @Route("/authors/{id<\d+>}", methods={"PUT"})
     */
    public function updateAuthor(Request $request, DataUtils $dataUtils, TranslatorInterface $translator): Response
    {
        $em = $this->getDoctrine()->getManager();
        $repoAuthor = $em->getRepository(Author::class);

        $errors = array();

        $id = $request->get("id");
        $author = $repoAuthor->find($id);

        if(!$author) {
            $errors[] = $translator->trans("author_not_found");
        }
        else {

            $res = $dataUtils->setAuthorFromRequest($request, $author);

            if($res instanceof Author) {
                $author = $res;
                $em->flush();
            }
            else {
                foreach($res as $error) {
                    $errors[] = $error;
                }
            }
        }

        // https://symfony.com/doc/current/components/http_foundation.html#creating-a-json-response
        $response = null;
        if(count($errors) > 0) {
            $response = new JsonResponse(array(
                "errors"    => $errors
            ));
        }
        else {
            $response = new JsonResponse(array(
                "author" => $dataUtils->getAuthor($author)
            ));
        }

        $response->headers->set("Access-Control-Allow-Origin", "*");
        return $response;
    }

    /**
     * @Route("/genres", methods={"GET","HEAD"})
     */
    public function genres(DataUtils $dataUtils, TranslatorInterface $translator): Response
    {
        $em = $this->getDoctrine()->getManager();
        $repoGenre = $em->getRepository(Genre::class);

        $genres = $repoGenre->findAll();

        // Sort genres by name ASC
        $dictGenres = array();
        foreach($genres as $genre) {
            $name = $translator->trans($genre->getCode());
            $dictGenres[$name] = $genre;
        }
        ksort($dictGenres);
        $genres = array();
        foreach($dictGenres as $genre) {
            $genres[] = $genre;
        }

        // https://symfony.com/doc/current/components/http_foundation.html#creating-a-json-response
        $response = new JsonResponse(array(
            "genres"    => $dataUtils->getGenres($genres)
        ));

        $response->headers->set("Access-Control-Allow-Origin", "*");
        return $response;
    }
}