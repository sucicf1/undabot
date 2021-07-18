<?php
namespace App\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GithubQueryTest extends KernelTestCase
{
    public function testQueryResults()
    {
        self::bootKernel(['debug' => true]);
        $container = static::getContainer();
        $service = $container->get('github_query_test');

        $numPositive = $service->getNumPositive("php");
        $numNeg = $service->getNumNeg("php");

        $this->assertLessThanOrEqual(3000, $numPositive);
        $this->assertLessThanOrEqual(5000, $numNeg);
    }
}