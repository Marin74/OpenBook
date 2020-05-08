<?php
namespace App\Service;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Genre;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class DataUtils {

    private $translator;

    public function __construct(TranslatorInterface $translator) {
        $this->translator = $translator;
    }

    public function getAuthor(Author $author = null) {

        $data = array();

        if($author != null) {
            $data = array(
                "id" => $author->getId(),
                "name" => $author->getName()
            );

            if ($author->getBirthDate() != null) {
                $data["birthDate"] = $author->getBirthDate()->format("Y-m-d");
            }
            if ($author->getDeathDate() != null) {
                $data["deathDate"] = $author->getDeathDate()->format("Y-m-d");
            }
        }

        return $data;
    }

    public function getBook(Book $book = null) {

        $data = array();

        if($book != null) {
            $authorsData = array();
            foreach ($book->getAuthors() as $author) {
                $authorsData[] = $this->getAuthor($author);
            }

            $data = array(
                "id" => $book->getId(),
                "title" => $book->getTitle()
            );
            if (!empty($book->getSubtitle())) {
                $data["subtitle"] = $book->getSubtitle();
            }
            if(count($authorsData) > 0) {
                $data["authors"] = $authorsData;
            }
            if (!empty($book->getSummary())) {
                $data["summary"] = $book->getSummary();
            }
            if (!empty($book->getPublicationYear())) {
                $data["publicationYear"] = $book->getPublicationYear();
            }

            $genreData = $this->getGenre($book->getGenre());
            if(!empty($genreData)) {
                $data["genre"] = $genreData;
            }
        }

        return $data;
    }

    public function getGenre(Genre $genre = null) {

        if($genre != null) {
            return array(
                "id"    => $genre->getId(),
                "name"  => $this->translator->trans($genre->getCode())
            );
        }

        return null;
    }

    public function getBooks(array $books) {
        $data = array();

        foreach($books as $book) {
            $data[] = $this->getBook($book);
        }

        return $data;
    }

    public function getAuthors(array $authors) {
        $data = array();

        foreach($authors as $author) {
            $data[] = $this->getAuthor($author);
        }

        return $data;
    }

    public function getGenres(array $genres) {
        $data = array();

        foreach($genres as $genre) {
            $data[] = $this->getGenre($genre);
        }

        return $data;
    }

    public function setBookFromRequest(Request $request, Book $book, ObjectRepository $repoGenre) {

        $errors = array();

        $title = trim($request->get("title"));
        if (empty($title)) {
            $title = null;
        }
        $subtitle = trim($request->get("subtitle"));
        if (empty($subtitle)) {
            $subtitle = null;
        }
        $summary = trim($request->get("summary"));
        if (empty($summary)) {
            $summary = null;
        }
        $publicationYear = $request->get("publicationYear");
        // Check the value is an integer
        if (!empty($publicationYear)) {
            if (preg_match("/^\d+$/", $publicationYear)) {// Check it is an integer
                $publicationYear = intval($publicationYear);
            } else {
                $publicationYear = null;
            }
        }
        $genreCode = $request->get("genre");
        $genre = null;
        if (!empty($genreCode)) {
            $genre = $repoGenre->findOneByCode($genreCode);
        }

        // Create the book
        if (empty($title)) {
            $errors[] = $this->translator->trans("title_field_empty");
        } else {
            $book->setTitle($title);
            $book->setSubtitle($subtitle);
            $book->setSummary($summary);
            $book->setPublicationYear($publicationYear);
            $book->setGenre($genre);
        }

        if(count($errors) > 0) {
            return $errors;
        }

        return $book;
    }
}