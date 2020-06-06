<?php
namespace App\Service;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Genre;
use Doctrine\Common\Collections\Collection;
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
            if(!empty($author->getPicture())) {
                // TODO Get by the standard way the URL
                $data["picture"] = "https://openbook.s3.fr-par.scw.cloud/author/".$author->getPicture();
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
            if (!empty($book->getOriginalTitle())) {
                $data["originalTitle"] = $book->getOriginalTitle();
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
            if(!empty($book->getPicture())) {
                // TODO Get by the standard way the URL
                $data["picture"] = "https://openbook.s3.fr-par.scw.cloud/book/".$book->getPicture();
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

    public function getBooksFromCollection(Collection $books) {
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
        $originalTitle = trim($request->get("originalTitle"));
        if (empty($originalTitle)) {
            $originalTitle = null;
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
            if($request->query->has("subtitle")) {
                $book->setSubtitle($subtitle);
            }
            if($request->query->has("originalTitle")) {
                $book->setOriginalTitle($originalTitle);
            }
            if($request->query->has("summary")) {
                $book->setSummary($summary);
            }
            if($request->query->has("publicationYear")) {
                $book->setPublicationYear($publicationYear);
            }
            if($request->query->has("genre")) {
                $book->setGenre($genre);
            }
            $book->setUpdatedAt(new \DateTime());
        }

        if(count($errors) > 0) {
            return $errors;
        }

        return $book;
    }

    public function setAuthorFromRequest(Request $request, Author $author) {

        $errors = array();

        $name = trim($request->get("name"));
        if (empty($name)) {
            $name = null;
        }
        $birthDate = $request->get("birthDate");
        if(!empty($birthDate)) {
            $birthDate = \DateTime::createFromFormat("Y-m-d", $birthDate);
            if(is_bool($birthDate)) {
                $birthDate = null;
            }
        }
        $deathDate = $request->get("deathDate");
        if(!empty($deathDate)) {
            $deathDate = \DateTime::createFromFormat("Y-m-d", $deathDate);
            if(is_bool($deathDate)) {
                $deathDate = null;
            }
        }

        // Create the author
        if (empty($name)) {
            $errors[] = $this->translator->trans("name_field_empty");
        } else {
            $author->setName($name);
            if($request->query->has("birthDate")) {
                $author->setBirthDate($birthDate);
            }
            if($request->query->has("deathDate")) {
                $author->setDeathDate($deathDate);
            }
            $author->setUpdatedAt(new \DateTime());
        }

        if(count($errors) > 0) {
            return $errors;
        }

        return $author;
    }
}