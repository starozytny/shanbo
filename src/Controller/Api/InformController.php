<?php

namespace App\Controller\Api;

use App\Entity\Inform;
use App\Service\Data\DataService;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

#[Route(path: '/api/inform', name: 'api_inform_')]
class InformController extends AbstractController
{
    /**
     * Admin - Delete a message contact
     *
     *
     *
     * @OA\Response(
     *     response=200,
     *     description="Return message successful",
     * )
     *
     * @OA\Tag(name="Inform")
     *
     * @return JsonResponse
     */
    #[IsGranted('ROLE_ADMIN')]
    #[Route(path: '/{id}', name: 'delete', options: ['expose' => true], methods: ['DELETE'])]
    public function delete(Inform $obj, DataService $dataService): JsonResponse
    {
        return $dataService->delete($obj);
    }

    /**
     * Admin - Delete a group of message contact
     *
     *
     *
     * @OA\Response(
     *     response=200,
     *     description="Return message successful",
     * )
     *
     * @OA\Tag(name="Inform")
     *
     * @return JsonResponse
     */
    #[IsGranted('ROLE_ADMIN')]
    #[Route(path: '/', name: 'delete_group', options: ['expose' => true], methods: ['DELETE'])]
    public function deleteSelected(Request $request, DataService $dataService): JsonResponse
    {
        return $dataService->deleteSelected(Inform::class, json_decode($request->getContent()));
    }
}
