<?php

// tests/Application/Web/Controller/MovieControllerTest.php

declare(strict_types=1);

namespace App\Tests\Application\Web\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MovieControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    public function testIndexPageLoads(): void
    {
        $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.navbar');
        $this->assertSelectorExists('.movies-grid');
        $this->assertSelectorExists('.genres-list');
    }

    public function testGenrePageLoads(): void
    {
        $this->client->request('GET', '/genre/28');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.movies-grid');
    }

    public function testHomePageShowsPopularMovies(): void
    {
        $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.featured-movie');
        $this->assertSelectorTextContains('h1', 'We Movies');
    }

    public function testGenreFilterIsPresent(): void
    {
        $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.genres-list');
        $this->assertSelectorExists('.genres-list li');
    }
}
