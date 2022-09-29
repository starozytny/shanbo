<?php

namespace App\Controller\Api\User;

use App\Entity\Photo;
use App\Service\ApiResponse;
use App\Service\FileUploader;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * @Route("/api/members/photos", name="api_members_photos_")
 */
class PhotoController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
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
     * @OA\Tag(name="Photos")
     *
     * @param Request $request
     * @param ApiResponse $apiResponse
     * @param FileUploader $fileUploader
     * @return JsonResponse
     */
    public function create(Request $request, ApiResponse $apiResponse, FileUploader $fileUploader): JsonResponse
    {
        $em = $this->doctrine->getManager();

        $files = $request->files->get('photos');
        if ($files) {
            foreach ($files as $file) {
                $filename = $fileUploader->upload($file, Photo::FOLDER_PHOTOS);

                $obj = (new Photo())
                    ->setFilename($filename)
                ;

                $fileUploader->createThumb(
                    'public',
                    Photo::FOLDER_PHOTOS . "/" . $filename, Photo::FOLDER_THUMBS,
                    517, 755
                );

                $em->persist($obj);
            }
        }

        $em->flush();
        return $apiResponse->apiJsonResponseSuccessful("ok");
    }
}
