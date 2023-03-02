<?php

namespace App\Command\Fake;

use App\Entity\Changelog;
use App\Service\Data\DataChangelog;
use App\Service\DatabaseService;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FakeChangelogCreateCommand extends Command
{
    protected static $defaultName = 'fake:changelog:create';
    protected static $defaultDescription = 'Create fake changelogs';
    private $em;
    private $databaseService;
    private $dataChangelog;

    public function __construct(EntityManagerInterface $entityManager, DatabaseService $databaseService, DataChangelog $dataChangelog)
    {
        parent::__construct();

        $this->em = $entityManager;
        $this->databaseService = $databaseService;
        $this->dataChangelog = $dataChangelog;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Reset des tables');
        $this->databaseService->resetTable($io, [Changelog::class]);

        $io->title('Création de 30 changelogs fake');
        $fake = Factory::create();
        for($i=0; $i<110 ; $i++) {

            $data = [
                'name' => $fake->name,
                'type' => $fake->numberBetween(0, 2),
                'content' => [
                    'html' => "<p>" . $fake->sentence ." <b>Test</b> " . $fake->streetName .". </p>"
                ]
            ];

            $data = json_decode(json_encode($data));

            $new = $this->dataChangelog->setData(new Changelog(), $data);

            $this->em->persist($new);
        }
        $io->text('CHANGELOGS : Changelogs fake créés' );

        $this->em->flush();

        $io->newLine();
        $io->comment('--- [FIN DE LA COMMANDE] ---');
        return Command::SUCCESS;
    }
}
