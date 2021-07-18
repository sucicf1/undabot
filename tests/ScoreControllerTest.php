<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ScoreControllerTest extends WebTestCase
{
    public function testScore(): void
    {
        $client = static::createClient();
        $request = $client->request('GET', '/score/php');
        $response = $client->getResponse();

        $this->assertResponseIsSuccessful();
        $data = json_decode($response->getContent(), true);
        $score = $data['data']['attributes']['score'];
        $this->assertLessThanOrEqual(7, $score);
    }
}
