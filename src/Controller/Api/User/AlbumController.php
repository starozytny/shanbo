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
use OpenApi\Annotations as OA;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/api/members/albums", name="api_members_albums_")
 */
class AlbumController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function submitForm($type, Album $obj, Request $request, ApiResponse $apiResponse,
                               ValidatorService $validator, DataAlbum $dataEntity, FileUploader $fileUploader): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $data = json_decode($request->get('data'));

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

    /**
     * @Route("/", name="create", options={"expose"=true}, methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a new object"
     * )
     * @OA\Response(
     *     response=400,
     *     description="JSON empty or missing data or validation failed",
     * )
     *
     * @OA\Tag(name="Users")
     *
     * @param Request $request
     * @param ValidatorService $validator
     * @param ApiResponse $apiResponse
     * @param FileUploader $fileUploader
     * @param DataAlbum $dataEntity
     * @return JsonResponse
     */
    public function create(Request $request, ValidatorService $validator, ApiResponse $apiResponse,
                           FileUploader $fileUploader, DataAlbum $dataEntity): JsonResponse
    {
        return $this->submitForm("create", new Album(), $request, $apiResponse, $validator, $dataEntity, $fileUploader);
    }

    /**
     * @Route("/{id}", name="update", options={"expose"=true}, methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns an object"
     * )
     * @OA\Response(
     *     response=400,
     *     description="Validation failed",
     * )
     *
     * @OA\Tag(name="Users")
     *
     * @param Request $request
     * @param ValidatorService $validator
     * @param ApiResponse $apiResponse
     * @param Album $obj
     * @param FileUploader $fileUploader
     * @param DataAlbum $dataEntity
     * @return JsonResponse
     */
    public function update(Request $request, ValidatorService $validator, ApiResponse $apiResponse, Album $obj,
                           FileUploader $fileUploader, DataAlbum $dataEntity): JsonResponse
    {
        return $this->submitForm("update", $obj, $request, $apiResponse, $validator, $dataEntity, $fileUploader);
    }

    /**
     * @Route("/{id}", name="delete", options={"expose"=true}, methods={"DELETE"})
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
     * @OA\Tag(name="Users")
     *
     * @param ApiResponse $apiResponse
     * @param Album $obj
     * @param FileUploader $fileUploader
     * @return JsonResponse
     */
    public function delete(ApiResponse $apiResponse, Album $obj, FileUploader $fileUploader): JsonResponse
    {
        $em = $this->doctrine->getManager();

        $cover = $obj->getCover();

        $em->remove($obj);
        $em->flush();

        $fileUploader->deleteFile($cover, Album::FOLDER_ALBUMS);
        return $apiResponse->apiJsonResponseSuccessful("Supression réussie !");
    }
}
