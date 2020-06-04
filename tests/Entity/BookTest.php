<?php


namespace Entity;


use App\Entity\Book;
use PHPUnit\Framework\TestCase;

class BookTest extends TestCase
{
    public function testTitle() {

        $book = new Book();
        $book->setTitle("La fortune des Rougon");

        $this->assertEquals("La fortune des Rougon", $book->getTitle());
    }
}