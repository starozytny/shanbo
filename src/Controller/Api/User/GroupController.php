<?php

namespace App\Controller\Api\User;

use App\Entity\Album;
use App\Entity\Group;
use App\Service\ApiResponse;
use App\Service\Data\DataAlbum;
use App\Service\ValidatorService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/members/groups', name: 'api_members_groups_')]
class GroupController extends AbstractController
{
    public function __construct(private readonly ManagerRegistry $doctrine)
    {
    }

    public function submitForm($type, Group $obj, Request $request, ApiResponse $apiResponse,
                               ValidatorService $validator, DataAlbum $dataEntity): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $data = json_decode($request->getContent());

        if ($data === null) {
            return $apiResponse->apiJsonResponseBadRequest('Les données sont vides.');
        }

        $album = $em->getRepository(Album::class)->find($data->albumId);
        if(!$album){
            return $apiResponse->apiJsonResponseBadRequest('Album introuvable.');
        }

        $obj = $dataEntity->setDateGroup($obj, $data);
        $obj->setAlbum($album);

        $noErrors = $validator->validate($obj);
        if ($noErrors !== true) {
            return $apiResponse->apiJsonResponseValidationFailed($noErrors);
        }

        $em->persist($obj);
        $em->flush();

        return $apiResponse->apiJsonResponse($obj, Group::GROUP_READ);
    }

    #[Route(path: '/', name: 'create', options: ['expose' => true], methods: ['POST'])]
    public function create(Request $request, ValidatorService $validator, ApiResponse $apiResponse, DataAlbum $dataEntity): JsonResponse
    {
        return $this->submitForm("create", new Group(), $request, $apiResponse, $validator, $dataEntity);
    }

    #[Route(path: '/{id}', name: 'update', options: ['expose' => true], methods: ['PUT'])]
    public function update(Request $request, ValidatorService $validator, ApiResponse $apiResponse, Group $obj, DataAlbum $dataEntity): JsonResponse
    {
        return $this->submitForm("update", $obj, $request, $apiResponse, $validator, $dataEntity);
    }

    #[Route(path: '/{id}', name: 'delete', options: ['expose' => true], methods: ['DELETE'])]
    public function delete(ApiResponse $apiResponse, Group $obj): JsonResponse
    {
        $em = $this->doctrine->getManager();

        $em->remove($obj);
        $em->flush();

        return $apiResponse->apiJsonResponseSuccessful("Suppression réussie !");
    }
}
