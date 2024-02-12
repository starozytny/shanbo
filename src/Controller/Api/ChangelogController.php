<?php

namespace App\Controller\Api;

use App\Entity\Changelog;
use App\Entity\User;
use App\Service\ApiResponse;
use App\Service\Data\DataChangelog;
use App\Service\Data\DataService;
use App\Service\ValidatorService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/changelogs', name: 'api_changelogs_')]
#[IsGranted('ROLE_ADMIN')]
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

    #[Route(path: '/', name: 'create', options: ['expose' => true], methods: ['POST'])]
    public function create(Request $request, ValidatorService $validator, ApiResponse $apiResponse, DataChangelog $dataEntity): JsonResponse
    {
        return $this->submitForm("create", new Changelog(), $request, $apiResponse, $validator, $dataEntity);
    }

    #[Route(path: '/{id}', name: 'update', options: ['expose' => true], methods: ['POST'])]
    public function update(Request $request, ValidatorService $validator,  ApiResponse $apiResponse, Changelog $obj, DataChangelog $dataEntity): JsonResponse
    {
        return $this->submitForm("update", $obj, $request, $apiResponse, $validator, $dataEntity);
    }

    #[Route(path: '/{id}/is-published', name: 'switch_isPublished', options: ['expose' => true], methods: ['POST'])]
    public function switchIsPublished(Changelog $obj, DataService $dataService): JsonResponse
    {
        return $dataService->switchIsPublished($obj, User::USER_READ);
    }

    #[Route(path: '/{id}', name: 'delete', options: ['expose' => true], methods: ['DELETE'])]
    public function delete(Changelog $obj, DataService $dataService): JsonResponse
    {
        return $dataService->delete($obj);
    }

    #[Route(path: '/', name: 'delete_group', options: ['expose' => true], methods: ['DELETE'])]
    public function deleteGroup(Request $request, DataService $dataService): JsonResponse
    {
        return $dataService->deleteSelected(Changelog::class, json_decode($request->getContent()));
    }
}
