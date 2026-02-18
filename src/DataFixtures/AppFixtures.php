<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Client;
use App\Entity\Compte;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $usersData = [
            [
                'email' => 'jean.dupont@example.com',
                'firstName' => 'Jean',
                'lastName' => 'Dupont',
                'adresse' => '123 Rue de la Paix, 75000 Paris',
                'gender' => User::GENDER_MALE,
                'password' => 'password123'
            ],
            [
                'email' => 'marie.martin@example.com',
                'firstName' => 'Marie',
                'lastName' => 'Martin',
                'adresse' => '456 Avenue des Champs, 75008 Paris',
                'gender' => User::GENDER_FEMALE,
                'password' => 'password123'
            ],
            [
                'email' => 'pierre.bernard@example.com',
                'firstName' => 'Pierre',
                'lastName' => 'Bernard',
                'adresse' => '789 Boulevard Saint-Germain, 75005 Paris',
                'gender' => User::GENDER_OTHER,
                'password' => 'password123'
            ],
            [
                'email' => 'sophie.thomas@example.com',
                'firstName' => 'Sophie',
                'lastName' => 'Thomas',
                'adresse' => '321 Rue de Rivoli, 75001 Paris',
                'gender' => User::GENDER_FEMALE,
                'password' => 'password123'
            ]
        ];

        $users = [];
        foreach ($usersData as $userData) {
            $user = new User();
            $user->setEmail($userData['email']);
            $user->setFirstName($userData['firstName']);
            $user->setLastName($userData['lastName']);
            $user->setAdresse($userData['adresse']);
            $user->setGender($userData['gender']);
            $user->setRoles(['ROLE_USER']);
            
            $hashedPassword = $this->passwordHasher->hashPassword($user, $userData['password']);
            $user->setPassword($hashedPassword);
            
            $manager->persist($user);
            $users[] = $user;
        }

        $clientsData = [
            ['numero' => '0000000001', 'userIndex' => 0],
            ['numero' => '0000000002', 'userIndex' => 1],
            ['numero' => '0000000003', 'userIndex' => 2],
            ['numero' => '0000000004', 'userIndex' => 3],
        ];

        $clients = [];
        foreach ($clientsData as $clientData) {
            $client = new Client();
            $user = $users[$clientData['userIndex']];
            $client->setUser($user);
            $user->setClient($client);
            $client->setNumero($clientData['numero']);
            
            $manager->persist($client);
            $clients[] = $client;
        }

        $comptesData = [
            ['numero' => 'FR1234567890', 'clientIndex' => 0, 'solde' => 1500.50, 'decouvert' => false, 'decouvertAutorise' => null],
            ['numero' => 'FR9876543210', 'clientIndex' => 0, 'solde' => 5000.00, 'decouvert' => true, 'decouvertAutorise' => 1000.00],

            ['numero' => 'FR1111111111', 'clientIndex' => 1, 'solde' => 3200.75, 'decouvert' => false, 'decouvertAutorise' => null],

            ['numero' => 'FR2222222222', 'clientIndex' => 2, 'solde' => 750.25, 'decouvert' => true, 'decouvertAutorise' => 500.00],
            ['numero' => 'FR3333333333', 'clientIndex' => 2, 'solde' => 10000.00, 'decouvert' => false, 'decouvertAutorise' => null],

            ['numero' => 'FR4444444444', 'clientIndex' => 3, 'solde' => 2100.30, 'decouvert' => true, 'decouvertAutorise' => 2000.00],
        ];

        foreach ($comptesData as $compteData) {
            $compte = new Compte();
            $compte->setOwner($clients[$compteData['clientIndex']]);
            $compte->setNumero($compteData['numero']);
            $compte->setSolde($compteData['solde']);
            $compte->setDecouvertStatut($compteData['decouvert']);
            if ($compteData['decouvertAutorise'] !== null) {
                $compte->setMontantDecouvert($compteData['decouvertAutorise']);
            }

            $manager->persist($compte);
        }

        $manager->flush();
    }
}
