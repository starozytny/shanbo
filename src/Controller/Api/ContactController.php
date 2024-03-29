<?php

namespace App\Controller\Api;

use App\Entity\Contact;
use App\Entity\User;
use App\Repository\ContactRepository;
use App\Service\ApiResponse;
use App\Service\Data\DataService;
use App\Service\MailerService;
use App\Service\NotificationService;
use App\Service\SanitizeData;
use App\Service\SettingsService;
use App\Service\ValidatorService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/contact', name: 'api_contact_')]
class ContactController extends AbstractController
{
    public const ICON = "chat-2";

    public function __construct(private readonly ManagerRegistry $doctrine)
    {
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route(path: '/', name: 'index', options: ['expose' => true], methods: ['GET'])]
    public function index(Request $request, ContactRepository $repository, ApiResponse $apiResponse): JsonResponse
    {
        $order = $request->query->get('order') ?: 'ASC';
        $objs = $repository->findBy([], ['createdAt' => $order]);
        return $apiResponse->apiJsonResponse($objs, User::ADMIN_READ);
    }

    #[Route(path: '/', name: 'create', options: ['expose' => true], methods: ['POST'])]
    public function create(Request $request, ValidatorService $validator, ApiResponse $apiResponse, NotificationService $notificationService,
                           MailerService $mailerService, SettingsService $settingsService, SanitizeData $sanitizeData): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $data = json_decode($request->getContent());

        if ($data === null) {
            return $apiResponse->apiJsonResponseBadRequest('Les données sont vides.');
        }

        if (!isset($data->name) || !isset($data->email) || !isset($data->message)) {
            return $apiResponse->apiJsonResponseBadRequest('Il manque des données.');
        }

        $obj = (new Contact())
            ->setName($sanitizeData->sanitizeString($data->name))
            ->setEmail($data->email)
            ->setMessage($sanitizeData->sanitizeString($data->message))
        ;

        $noErrors = $validator->validate($obj);
        if ($noErrors !== true) {
            return $apiResponse->apiJsonResponseValidationFailed($noErrors);
        }

        if(!$mailerService->sendMail(
            $settingsService->getEmailContact(),
            "[" . $settingsService->getWebsiteName() . "] Demande de contact",
            "Demande de contact réalisé à partir de " . $settingsService->getWebsiteName(),
            'app/email/contact/contact.html.twig',
            ['contact' => $obj, 'settings' => $settingsService->getSettings()]
        ))
        {
            return $apiResponse->apiJsonResponseValidationFailed([[
                'name' => 'message',
                'message' => "Le message n\'a pas pu être délivré. Veuillez contacter le support."
            ]]);
        }

        $em->persist($obj);
        $em->flush();

        $notificationService->createNotification(
            "Demande de contact",
            self::ICON,
            $this->getUser(),
            $this->generateUrl('admin_contact_index', ['search' => $obj->getId()])
        );

        return $apiResponse->apiJsonResponseSuccessful("Message envoyé.");
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route(path: '/{id}/is-seen', name: 'isSeen', options: ['expose' => true], methods: ['POST'])]
    public function isSeen(Contact $obj, DataService $dataService): JsonResponse
    {
        return $dataService->isSeenToTrue($obj);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route(path: '/{id}', name: 'delete', options: ['expose' => true], methods: ['DELETE'])]
    public function delete(Contact $obj, DataService $dataService): JsonResponse
    {
        return $dataService->delete($obj, true);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route(path: '/', name: 'delete_group', options: ['expose' => true], methods: ['DELETE'])]
    public function deleteSelected(Request $request, DataService $dataService): JsonResponse
    {
        return $dataService->deleteSelected(Contact::class, json_decode($request->getContent()));
    }
}
