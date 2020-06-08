<?php


namespace App\Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AppControllerTest extends WebTestCase
{
    private const METHOD_GET = "GET";
    private const METHOD_HEAD = "HEAD";

    private const URI_BOOKS = "/books";
    private const URI_BOOK = "/books/{id}";// TODO PUT method
    private const URI_NEW_BOOK = "/books/new";// TODO
    private const URI_AUTHORS = "/authors";
    private const URI_AUTHOR = "/authors/{id}";

    public function testBooksIsUp() {
        $client = static::createClient();
        $client->request(self::METHOD_GET, self::URI_BOOKS);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testBooksIsUpWithHeadMethod() {
        $client = static::createClient();
        $client->request(self::METHOD_HEAD, self::URI_BOOKS);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testBookIsUp() {
        $uri = str_replace("{id}", 1, self::URI_BOOK);

        $client = static::createClient();
        $client->request(self::METHOD_GET, $uri);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testBookIsUpWithHeadMethod() {
        $uri = str_replace("{id}", 1, self::URI_BOOK);

        $client = static::createClient();
        $client->request(self::METHOD_HEAD, $uri);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testAuthorsIsUp() {
        $client = static::createClient();
        $client->request(self::METHOD_GET, self::URI_AUTHORS);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testAuthorsIsUpWithHeadMethod() {
        $client = static::createClient();
        $client->request(self::METHOD_HEAD, self::URI_AUTHORS);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testAuthorIsUp() {
        $uri = str_replace("{id}", 1, self::URI_AUTHOR);

        $client = static::createClient();
        $client->request(self::METHOD_GET, $uri);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testAuthorIsUpWithHeadMethod() {
        $uri = str_replace("{id}", 1, self::URI_AUTHOR);

        $client = static::createClient();
        $client->request(self::METHOD_HEAD, $uri);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}