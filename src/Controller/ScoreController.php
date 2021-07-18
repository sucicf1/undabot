<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\QueryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Score;
use OpenApi\Annotations as OA;

class ScoreController extends AbstractController
{   
    private function isCacheTimeValid($cachedScore) : bool
    {
        $timeNow = new \DateTime('@' . time());
        $maxDiff = $this->getParameter('app.time.cache.valid');
        if (!is_null($cachedScore) && 
            ($timeNow <=
            ($cachedScore->getLastQueryTime()->modify('+' . $maxDiff . ' second'))))
        {
            return true;
        }
        return false;
    }

    /**
     * @Route("/score/{term}", name="score", methods={"GET"})
     * @OA\Parameter(
     *     name = "term",
     *     in = "path",
     *     required = true,
     *     description="The term for which to calculate score"
     * )
     * @OA\Response(
     *     response = "200",
     *     description = "OK",
     *     @OA\MediaType(
     *         mediaType="application/vnd.api+json",
     *         @OA\Schema(
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="type",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="id",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="attributes",
     *                     type="object",
     *                     @OA\Property(
     *                         property="term",
     *                         type="string",
     *                         example="php"
     *                     ),
     *                     @OA\Property(
     *                         property="score",
     *                         description="Calculated score",
     *                         example=4,
     *                         type="number"
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     * @OA\Response(
     *     response = "400 or 500",
     *     description = "Client or server error",
     *     @OA\MediaType(
     *         mediaType="application/vnd.api+json",
     *         @OA\Schema(
     *             @OA\Property(
     *                 property="error",
     *                 type="object",
     *                 @OA\Property(
     *                     property="status",
     *                     type="number"
     *                 ),
     *                 @OA\Property(
     *                     property="code",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="title",
     *                     type="string"
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function score(string $term="", Request $request, QueryService $queryService): Response
    {
        if (empty($term))
        {
            $response = new Response();
            $response->setContent(json_encode([
                "errors" => [[
                    "status" => 400,
                    "code" => 0,
                    "title" => "Missing term parameter"
            ]]]));
            $response->headers->set('Content-Type', 'application/vnd.api+json');
            return $response;
        }
        $entityManager = $this->getDoctrine()->getManager();

        try 
        {
            $cachedScore = $this->getDoctrine()
                            ->getRepository(Score::class)
                            ->findOneBy(['term' => strtolower($term)]);
            if (!empty ($cachedScore))
            {
                $numPositive = $cachedScore->getNumPositive();
                $numNeg = $cachedScore->getNumNeg();
            }
            
            if (is_null($cachedScore) || !$this->isCacheTimeValid($cachedScore))
            {
                if (is_null($cachedScore))
                {
                    $cachedScore = new Score();
                    $cachedScore->setTerm(strtolower($term));
                    $entityManager->persist($cachedScore);
                }
                $numPositive = $queryService->getNumPositive($term);
                $numNeg = $queryService->getNumNeg($term);
                $cachedScore->setNumPositive($numPositive);
                $cachedScore->setNumNeg($numNeg);
                $entityManager->flush();
            }
            $score = ((float) $numPositive) / ($numPositive + $numNeg) * 10;
            $response = new Response();
            $response->setContent(json_encode([
                'data' => [
                    "type" => "score",
                    "id" => $cachedScore->getId(),
                    "attributes" => [
                        "term" => $term,
                        "score" => $score
            ]]]));
        }
        catch (\Exception $e)
        {
            $response = new Response();
            $response->setContent(json_encode([
                "errors" => [[
                    "status" => 500,
                    "code" => $e->getCode(),
                    "title" => $e->getMessage()
            ]]]));
        }
        $response->headers->set('Content-Type', 'application/vnd.api+json');

        return $response;
    }
}
