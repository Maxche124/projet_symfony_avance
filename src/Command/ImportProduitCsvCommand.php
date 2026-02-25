<?php

namespace App\Command;

use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:produit:import',
    description: 'Importe un fichier CSV de produits depuis le dossier public',
)]
class ImportProduitCsvCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private string $projectDir
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('filename', InputArgument::REQUIRED, 'Nom du fichier CSV dans /public');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $filename = $input->getArgument('filename');
        $filePath = $this->projectDir . '/public/' . $filename;

        if (!file_exists($filePath)) {
            $io->error("Fichier introuvable : $filePath");
            return Command::FAILURE;
        }

        if (($handle = fopen($filePath, 'r')) === false) {
            $io->error('Impossible d’ouvrir le fichier.');
            return Command::FAILURE;
        }

        $header = fgetcsv($handle);

        if ($header !== ['name', 'description', 'price']) {
            $io->error('En-tête invalide. Colonnes attendues : name, description, price');
            fclose($handle);
            return Command::FAILURE;
        }

        $count = 0;
        $lineNumber = 1;

        while (($data = fgetcsv($handle)) !== false) {
            $lineNumber++;

            if (count($data) !== 3) {
                $io->warning("Ligne $lineNumber ignorée (colonnes invalides)");
                continue;
            }

            [$name, $description, $price] = $data;

            if (empty($name) || empty($description) || !is_numeric($price)) {
                $io->warning("Ligne $lineNumber invalide (données incorrectes)");
                continue;
            }

            $produit = new Produit();
            $produit->setName($name);
            $produit->setDescription($description);
            $produit->setPrice($price);

            $this->entityManager->persist($produit);
            $count++;
        }

        fclose($handle);

        $this->entityManager->flush();

        $io->success("$count produit(s) importé(s) avec succès.");

        return Command::SUCCESS;
    }
}