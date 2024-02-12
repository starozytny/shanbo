<?php

namespace App\Controller\Api\User;

use App\Entity\Album;
use App\Service\ApiResponse;
use App\Service\Data\DataAlbum;
use App\Service\FileUploader;
use App\Service\ValidatorService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route(path: '/api/members/albums', name: 'api_members_albums_')]
class AlbumController extends AbstractController
{
    public function __construct(private readonly ManagerRegistry $doctrine)
    {
    }

    public function submitForm($type, Album $obj, Request $request, ApiResponse $apiResponse,
                               ValidatorService $validator, DataAlbum $dataEntity, FileUploader $fileUploader): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $data = json_decode((string) $request->get('data'));

        if ($data === null) {
            return $apiResponse->apiJsonResponseBadRequest('Les données sont vides.');
        }

        $obj = $dataEntity->setDataAlbum($obj, $data);

        if($type == "create"){
            $obj->setUser($this->getUser());
        }

        $file = $request->files->get('cover');
        if($file){
            if($obj->getCover()){
                $obj->setCover($fileUploader->replaceFile($file, $obj->getCover(),Album::FOLDER_ALBUMS));
            }else{
                $obj->setCover($fileUploader->upload($file, Album::FOLDER_ALBUMS));
            }
        }

        $noErrors = $validator->validate($obj);
        if ($noErrors !== true) {
            return $apiResponse->apiJsonResponseValidationFailed($noErrors);
        }

        $em->persist($obj);
        $em->flush();

        return $apiResponse->apiJsonResponseCustom([
            'url' => $this->generateUrl('user_albums_index', [], UrlGeneratorInterface::ABSOLUTE_URL)
        ]);
    }

    #[Route(path: '/', name: 'create', options: ['expose' => true], methods: ['POST'])]
    public function create(Request $request, ValidatorService $validator, ApiResponse $apiResponse,
                           FileUploader $fileUploader, DataAlbum $dataEntity): JsonResponse
    {
        return $this->submitForm("create", new Album(), $request, $apiResponse, $validator, $dataEntity, $fileUploader);
    }

    #[Route(path: '/{id}', name: 'update', options: ['expose' => true], methods: ['POST'])]
    public function update(Request $request, ValidatorService $validator, ApiResponse $apiResponse, Album $obj,
                           FileUploader $fileUploader, DataAlbum $dataEntity): JsonResponse
    {
        return $this->submitForm("update", $obj, $request, $apiResponse, $validator, $dataEntity, $fileUploader);
    }

    #[Route(path: '/{id}', name: 'delete', options: ['expose' => true], methods: ['DELETE'])]
    public function delete(ApiResponse $apiResponse, Album $obj, FileUploader $fileUploader): JsonResponse
    {
        $em = $this->doctrine->getManager();

        $cover = $obj->getCover();

        $em->remove($obj);
        $em->flush();

        $fileUploader->deleteFile($cover, Album::FOLDER_ALBUMS);
        return $apiResponse->apiJsonResponseSuccessful("Suppression réussie !");
    }
}
