<?php


namespace App\Service;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

class ApiResponse
{
    public function __construct(private readonly SerializerInterface $serializer)
    {
    }

    public function apiJsonResponse($data, $groups = [], $code = 200): JsonResponse
    {
        $data = $this->serializer->serialize($data, "json", ['groups' => $groups]);

        $response = new JsonResponse();
        $response->setContent($data);
        $response->setStatusCode($code);

        return $response;
    }

    public function apiJsonResponseData($data, $code = 200): JsonResponse
    {
        return new JsonResponse(['data' => $data], $code);
    }

    public function apiJsonResponseCustom($data, $code = 200): JsonResponse
    {
        return new JsonResponse($data, $code);
    }

    public function apiJsonResponseSuccessful($message): JsonResponse
    {
        return new JsonResponse(['message' => $message], \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function apiJsonResponseBadRequest($message): JsonResponse
    {
        return new JsonResponse(['message' => $message], \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST);
    }

    public function apiJsonResponseForbidden(): JsonResponse
    {
        return new JsonResponse(['message' => 'Vous n\'êtes pas autorisé à réaliser cette action.'], \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN);
    }

    public function apiJsonResponseValidationFailed($errors): JsonResponse
    {
        return new JsonResponse($errors, \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST);
    }
}