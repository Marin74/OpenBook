<?php


namespace App\Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AppControllerTest extends WebTestCase
{
    private const METHOD_GET = "GET";
    private const METHOD_HEAD = "HEAD";

    private const URI_BOOKS = "/books";
    private const URI_BOOK = "/books/{id}";// TODO Manage PUT method
    private const URI_NEW_BOOK = "/books/new";// TODO Manage this test case
    private const URI_AUTHORS = "/authors";
    private const URI_AUTHOR = "/authors/{id}";
    private const URI_GENRES = "/genres";

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

    public function testGenresIsUp() {
        $client = static::createClient();
        $client->request(self::METHOD_GET, self::URI_GENRES);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testGenresIsUpWithHeadMethod() {
        $client = static::createClient();
        $client->request(self::METHOD_HEAD, self::URI_GENRES);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testBooksFormat() {
        $client = static::createClient();
        $client->request(self::METHOD_GET, self::URI_BOOKS);

        $json = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey("books", $json);
        $jsonItems = $json["books"];
        $this->assertGreaterThan(0, count($jsonItems));
        $this->assertArrayHasKey("title", $jsonItems[0]);
    }

    public function testBookFormat() {
        $uri = str_replace("{id}", 1, self::URI_BOOK);

        $client = static::createClient();
        $client->request(self::METHOD_GET, $uri);

        $json = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey("book", $json);
        $jsonItem = $json["book"];
        $this->assertArrayHasKey("title", $jsonItem);
    }

    public function testAuthorsFormat() {
        $client = static::createClient();
        $client->request(self::METHOD_GET, self::URI_AUTHORS);

        $json = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey("authors", $json);
        $jsonItems = $json["authors"];
        $this->assertGreaterThan(0, count($jsonItems));
        $this->assertArrayHasKey("name", $jsonItems[0]);
    }

    public function testAuthorFormat() {
        $uri = str_replace("{id}", 1, self::URI_AUTHOR);

        $client = static::createClient();
        $client->request(self::METHOD_GET, $uri);

        $json = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey("author", $json);
        $jsonItem = $json["author"];
        $this->assertArrayHasKey("name", $jsonItem);
    }

    public function testGenresFormat() {
        $client = static::createClient();
        $client->request(self::METHOD_GET, self::URI_GENRES);

        $json = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey("genres", $json);
        $jsonItems = $json["genres"];
        $this->assertGreaterThan(0, count($jsonItems));
        $this->assertArrayHasKey("name", $jsonItems[0]);
    }
}