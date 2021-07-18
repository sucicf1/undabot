<?php
namespace App\Service;

interface QueryService
{
    public function getNumPositive(string $word): int;
    public function getNumNeg(string $word): int;
}