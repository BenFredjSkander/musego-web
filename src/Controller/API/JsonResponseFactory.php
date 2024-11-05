<?php

namespace App\Controller\API;

use App\Dto\ApiResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class JsonResponseFactory
{
    public function __construct(private readonly SerializerInterface $serializer)
    {
    }

    public function create(ApiResponse $response): JsonResponse
    {
        return new JsonResponse(
            $this->serializer->serialize($response, JsonEncoder::FORMAT, [DateTimeNormalizer::FORMAT_KEY => 'c']),
            $response->status,
            [],
            true
        );
    }
}
