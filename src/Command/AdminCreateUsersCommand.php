<?php

namespace App\Command;

use App\Entity\User;
use App\Service\DatabaseService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminCreateUsersCommand extends Command
{
    protected static $defaultName = 'admin:create:users';
    protected $passwordEncoder;
    protected $em;
    private $databaseService;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager, DatabaseService $databaseService)
    {
        parent::__construct();

        $this->passwordEncoder = $passwordEncoder;
        $this->em = $entityManager;
        $this->databaseService = $databaseService;
    }

    protected function configure()
    {
        $this
            ->setDescription('Create an user and an admin.')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option shit values')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Reset des tables');
        $this->databaseService->resetTable($io,'user');

        $users = array(
            [
                'username' => 'shanbo',
                'email' => 'chanbora.chhun@outlook.fr',
                'roles' => ['ROLE_USER','ROLE_ADMIN', 'ROLE_SUPER_ADMIN']
            ],
            [
                'username' => 'staro',
                'email' => 'starozytny@hotmail.fr',
                'roles' => ['ROLE_USER','ROLE_ADMIN']
            ],
            [
                'username' => 'shanks',
                'email' => 'shanks@hotmail.fr',
                'roles' => ['ROLE_USER']
            ]
        );

        $io->title('Création des utilisateurs');
        foreach ($users as $user) {
            $new = (new User())
                ->setUsername($user['username'])
                ->setEmail($user['email'])
                ->setRoles($user['roles'])
            ;

            $new->setPassword($this->passwordEncoder->encodePassword(
                $new, 'azerty'
            ));

            $this->em->persist($new);
            $io->text('USER : ' . $user['username'] . ' créé' );
        }

        if ($input->getOption('option')) {
            $io->title('Création de 110 utilisateurs lambdas');
            for($i=0; $i<110 ; $i++) {
                $new = (new User())
                    ->setUsername("Utilisateur " . $i)
                    ->setEmail("utilisateur".$i."@utilisateur.fr")
                    ->setRoles(['ROLE_USER'])
                ;

                $new->setPassword($this->passwordEncoder->encodePassword(
                    $new, 'azerty'
                ));

                $this->em->persist($new);
            }
            $io->text('USER : Utilisateurs fake créés' );
        }


        $this->em->flush();

        $io->comment('--- [FIN DE LA COMMANDE] ---');
        return 0;
    }
}