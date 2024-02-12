<?php

namespace App\Command\Fake;

use App\Entity\Contact;
use App\Entity\Notification;
use App\Service\DatabaseService;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'fake:contact:create',
    description: 'Create fake contacts',
)]
class FakeContactCreateCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $em,
                                private readonly DatabaseService $databaseService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Reset des tables');
        $this->databaseService->resetTable($io, [Notification::class, Contact::class]);

        $io->title('Création de 30 contacts fake');
        $fake = Factory::create();
        for($i=0; $i<110 ; $i++) {
            $new = (new Contact())
                ->setName($fake->streetName)
                ->setEmail($fake->freeEmail)
                ->setMessage($fake->text)
            ;

            $this->em->persist($new);
        }
        $io->text('CONTACT : Contacts fake créés' );

        $this->em->flush();

        $io->newLine();
        $io->comment('--- [FIN DE LA COMMANDE] ---');
        return Command::SUCCESS;
    }
}
