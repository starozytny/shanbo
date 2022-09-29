<?php

namespace App\Controller\Api\User;

use App\Entity\Photo;
use App\Service\ApiResponse;
use App\Service\FileUploader;
use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;
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
     * @throws Exception
     */
    public function create(Request $request, ApiResponse $apiResponse, FileUploader $fileUploader): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $dates = json_decode($request->get('dates'));

        $files = $request->files->get('photos');
        if ($files) {
            $i = 0;
            foreach ($files as $file) {
                $filename = $fileUploader->upload($file, Photo::FOLDER_PHOTOS);

                $date = new DateTime();
                if($timestamp = $dates[$i] ?? null){
                    $date->setTimestamp($timestamp);
                }

                $obj = (new Photo())
                    ->setFilename($filename)
                    ->setDateAt($date)
                ;

                $fileUploader->createThumb(
                    'public',
                    Photo::FOLDER_PHOTOS . "/" . $filename, Photo::FOLDER_THUMBS,
                    517, 755
                );

                $em->persist($obj);
                $i++;
            }
        }

        $em->flush();
        return $apiResponse->apiJsonResponseSuccessful("ok");
    }
}
