<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class HomepageTest extends WebTestCase
{
    #[Test]
    public function theHomepageExists(): void
    {
        //Given
        $client = static::createClient(server: [
            'HTTPS' => 'on',
        ]);

        //When
        $client->request(Request::METHOD_GET, '/');

        //Then
        self::assertResponseIsSuccessful();
    }
}
