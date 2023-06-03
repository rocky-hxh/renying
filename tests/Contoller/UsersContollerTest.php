<?php

namespace App\Tests\Contoller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UsersContollerTest extends WebTestCase
{
    public function testFilteredUsers(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/users?active=0');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
    }
}