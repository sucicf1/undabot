<?php
namespace App\Service;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GithubQuery implements QueryService
{
    private $positiveWord, $negWord, $githubUrl, $client;

    public function __construct($positiveWord, $negWord, HttpClientInterface $client)
    {
        $this->positiveWord = $positiveWord;
        $this->negWord = $negWord;
        $this->githubUrl = "https://api.github.com/search/issues";
        $this->client = $client;
    }

    public function getNumPositive(string $word): int 
    {
        $response = $this->client->request('GET', $this->githubUrl, [
            'headers' => [
                'Accept' => 'application/vnd.github.v3+json',
            ],
            'query' => [
                'q' => $word . ' ' . $this->positiveWord . '+is:issue'
            ]
        ]);

        $data = json_decode($response->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new BadRequestHttpException('invalid json body: ' . json_last_error_msg());
        }

        return $data["total_count"];
    }

    public function getNumNeg(string $word): int 
    {
        $response = $this->client->request('GET', $this->githubUrl, [
            'headers' => [
                'Accept' => 'application/vnd.github.v3+json',
            ],
            'query' => [
                'q' => $word . ' ' . $this->negWord . '+is:issue'
            ]
        ]);

        $data = json_decode($response->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new BadRequestHttpException('invalid json body: ' . json_last_error_msg());
        }

        return $data["total_count"];
    }
}