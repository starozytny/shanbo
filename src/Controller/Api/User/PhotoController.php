<?php

namespace App\Controller\Api\User;

use App\Entity\Photo;
use App\Repository\PhotoRepository;
use App\Service\ApiResponse;
use App\Service\FileUploader;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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

                $fileUploader->thumbs($filename, Photo::FOLDER_PHOTOS, Photo::FOLDER_THUMBS, 517, 755);

                $em->persist($obj);
                $i++;
            }
        }

        $em->flush();
        return $apiResponse->apiJsonResponseSuccessful("ok");
    }

    /**
     * @Route("/{id}", name="delete", options={"expose"=true}, methods={"POST"})
     */
    public function delete(Photo $photo, FileUploader $fileUploader, PhotoRepository $repository): RedirectResponse
    {
        $fileUploader->deleteFile($photo->getFilename(), Photo::FOLDER_PHOTOS);
        $fileUploader->deleteFile("thumbs-" . $photo->getFilename(), Photo::FOLDER_THUMBS);

        $repository->remove($photo, true);

        return $this->redirectToRoute('user_photos_index');
    }
}
