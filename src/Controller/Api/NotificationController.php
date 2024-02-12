<?php

namespace App\Controller\Api;

use App\Entity\Notification;
use App\Entity\User;
use App\Repository\NotificationRepository;
use App\Service\ApiResponse;
use App\Service\Data\DataService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

#[Route(path: '/api/notifications', name: 'api_notifications_')]
class NotificationController extends AbstractController
{
    public function __construct(private readonly ManagerRegistry $doctrine)
    {
    }

    /**
     * Get array of notifications
     *
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns array of notifications",
     * )
     * @OA\Tag(name="Notification")
     * @return JsonResponse
     */
    #[Route(path: '/', name: 'index', options: ['expose' => true], methods: ['GET'])]
    public function index(NotificationRepository $repository, ApiResponse $apiResponse): JsonResponse
    {
        $objs = $repository->findAll();
        return $apiResponse->apiJsonResponse($objs, User::ADMIN_READ);
    }

    /**
     * Change isSeen to true
     *
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns contact object",
     * )
     *
     * @OA\Tag(name="Notification")
     * @return JsonResponse
     */
    #[Route(path: '/{id}/is-seen', name: 'isSeen', options: ['expose' => true], methods: ['POST'])]
    public function isSeen(Notification $obj, DataService $dataService): JsonResponse
    {
        return $dataService->isSeenToTrue($obj);
    }

    /**
     * Set all to seen
     *
     *
     * @OA\Response(
     *     response=200,
     *     description="Return message successful",
     * )
     *
     * @OA\Tag(name="Notification")
     * @return JsonResponse
     */
    #[Route(path: '/all/seen', name: 'isSeen_all', options: ['expose' => true], methods: ['POST'])]
    public function allSeen(NotificationRepository $notificationRepository, ApiResponse $apiResponse): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $objs = $notificationRepository->findBy(['isSeen' => false]);

        foreach($objs as $obj){
            $obj->setIsSeen(true);
        }

        $objs = $notificationRepository->findAll();

        $em->flush();
        return $apiResponse->apiJsonResponse($objs, User::ADMIN_READ);
    }

    /**
     * Delete a notification
     *
     *
     * @OA\Response(
     *     response=200,
     *     description="Return message successful",
     * )
     *
     * @OA\Tag(name="Notification")
     * @return JsonResponse
     */
    #[Route(path: '/{id}', name: 'delete', options: ['expose' => true], methods: ['DELETE'])]
    public function delete(Notification $obj, DataService $dataService): JsonResponse
    {
        return $dataService->delete($obj);
    }

    /**
     * Delete a group of message notification
     *
     *
     * @OA\Response(
     *     response=200,
     *     description="Return message successful",
     * )
     *
     * @OA\Tag(name="Notification")
     * @return JsonResponse
     */
    #[Route(path: '/', name: 'delete_group', options: ['expose' => true], methods: ['DELETE'])]
    public function deleteSelected(Request $request, DataService $dataService): JsonResponse
    {
        return $dataService->deleteSelected(Notification::class, json_decode($request->getContent()));
    }
}
