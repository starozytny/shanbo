<?php

namespace App\Controller\Api;

use App\Entity\Changelog;
use App\Entity\User;
use App\Service\ApiResponse;
use App\Service\Data\DataChangelog;
use App\Service\Data\DataService;
use App\Service\ValidatorService;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

#[Route(path: '/api/changelogs', name: 'api_changelogs_')]
#[Security("is_granted('ROLE_ADMIN')")]
class ChangelogController extends AbstractController
{
    public function __construct(private readonly ManagerRegistry $doctrine)
    {
    }

    public function submitForm($type, Changelog $obj, Request $request, ApiResponse $apiResponse,
                               ValidatorService $validator, DataChangelog $dataEntity): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $data = json_decode($request->getContent());

        if ($data === null) {
            return $apiResponse->apiJsonResponseBadRequest('Les données sont vides.');
        }

        $obj = $dataEntity->setData($obj, $data);

        $noErrors = $validator->validate($obj);
        if ($noErrors !== true) {
            return $apiResponse->apiJsonResponseValidationFailed($noErrors);
        }

        $em->persist($obj);
        $em->flush();

        return $apiResponse->apiJsonResponse($obj, User::USER_READ);
    }

    /**
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a new object"
     * )
     *
     * @OA\Response(
     *     response=400,
     *     description="JSON empty or missing data or validation failed",
     * )
     *
     * @OA\Tag(name="Changelogs")
     * @return JsonResponse
     */
    #[Route(path: '/', name: 'create', options: ['expose' => true], methods: ['POST'])]
    public function create(Request $request, ValidatorService $validator, ApiResponse $apiResponse, DataChangelog $dataEntity): JsonResponse
    {
        return $this->submitForm("create", new Changelog(), $request, $apiResponse, $validator, $dataEntity);
    }

    /**
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns an object"
     * )
     * @OA\Response(
     *     response=403,
     *     description="Forbidden for not good role or user",
     * )
     * @OA\Response(
     *     response=400,
     *     description="Validation failed",
     * )
     *
     * @OA\Tag(name="Changelogs")
     * @return JsonResponse
     */
    #[Route(path: '/{id}', name: 'update', options: ['expose' => true], methods: ['POST'])]
    public function update(Request $request, ValidatorService $validator,  ApiResponse $apiResponse, Changelog $obj, DataChangelog $dataEntity): JsonResponse
    {
        return $this->submitForm("update", $obj, $request, $apiResponse, $validator, $dataEntity);
    }

    /**
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns object",
     * )
     *
     * @OA\Tag(name="Contact")
     * @return JsonResponse
     */
    #[Route(path: '/{id}/is-published', name: 'switch_isPublished', options: ['expose' => true], methods: ['POST'])]
    public function switchIsPublished(Changelog $obj, DataService $dataService): JsonResponse
    {
        return $dataService->switchIsPublished($obj, User::USER_READ);
    }

    /**
     *
     * @OA\Response(
     *     response=200,
     *     description="Return message successful",
     * )
     * @OA\Response(
     *     response=403,
     *     description="Forbidden for not good role or user",
     * )
     *
     * @OA\Tag(name="Changelogs")
     * @return JsonResponse
     */
    #[Route(path: '/{id}', name: 'delete', options: ['expose' => true], methods: ['DELETE'])]
    public function delete(Changelog $obj, DataService $dataService): JsonResponse
    {
        return $dataService->delete($obj);
    }

    /**
     *
     * @OA\Response(
     *     response=200,
     *     description="Return message successful",
     * )
     * @OA\Response(
     *     response=403,
     *     description="Forbidden for not good role or user",
     * )
     *
     * @OA\Response(
     *     response=400,
     *     description="Cannot delete me",
     * )
     *
     * @OA\Tag(name="Changelogs")
     * @return JsonResponse
     */
    #[Route(path: '/', name: 'delete_group', options: ['expose' => true], methods: ['DELETE'])]
    public function deleteGroup(Request $request, DataService $dataService): JsonResponse
    {
        return $dataService->deleteSelected(Changelog::class, json_decode($request->getContent()));
    }
}
