<?php

namespace App\Command\App\Command;

use App\Entity\CourCrypto;
use App\Repository\CryptoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
// use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
// use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:generate-crypto-prices',
    description: 'Génère aléatoirement le cours des cryptomonnaies toutes les 10 secondes.',
)]
class GenerateCryptoPriceCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private CryptoRepository $cryptoRepository;

    public function __construct(EntityManagerInterface $entityManager, CryptoRepository $cryptoRepository)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->cryptoRepository = $cryptoRepository;
    }

    // protected function configure(): void
    // {
    //     $this
    //         ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
    //         ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
    //     ;
    // }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        while (true) {
            $cryptos = $this->cryptoRepository->findAll();
            if (!$cryptos) {
                $io->warning('Aucune crypto trouvée dans la base.');
                return Command::FAILURE;
            }

            foreach ($cryptos as $crypto) {
                $currentValue = $crypto->getCurrentValeur();

                if ($currentValue === null) {
                    // Si c'est la première valeur, on prend un prix initial entre 1 et 1000$
                    $prixAleatoire = mt_rand(100, 100000) / 100;
                } else {
                    // Générer un prix aléatoire entre -10% et +10% de la valeur actuelle
                    $variation = $currentValue * 0.10;
                    $prixAleatoire = $currentValue + (mt_rand(-$variation * 100, $variation * 100) / 100);
                }

                $courCrypto = new CourCrypto();
                $courCrypto->setInstant(new \DateTime());
                $courCrypto->setValeurDollar($prixAleatoire);
                $courCrypto->setCrypto($crypto);

                $this->entityManager->persist($courCrypto);
            }

            $this->entityManager->flush();

            $io->success('Nouveaux cours générés.');

            sleep(10); // Attendre 10 secondes avant de relancer
        }
    }
}
