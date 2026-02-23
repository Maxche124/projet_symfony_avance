<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Produit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setFirstName('Jean');
        $user->setLastName('Utilisateur');
        $user->setRoles([User::ROLE_USER]);
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, 'password123')
        );
        $manager->persist($user);

        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setFirstName('Alice');
        $admin->setLastName('Admin');
        $admin->setRoles([User::ROLE_ADMIN]);
        $admin->setPassword(
            $this->passwordHasher->hashPassword($admin, 'password123')
        );
        $manager->persist($admin);

        $managerUser = new User();
        $managerUser->setEmail('manager@example.com');
        $managerUser->setFirstName('Marc');
        $managerUser->setLastName('Manager');
        $managerUser->setRoles([User::ROLE_MANAGER]);
        $managerUser->setPassword(
            $this->passwordHasher->hashPassword($managerUser, 'password123')
        );
        $manager->persist($managerUser);

        $produit1 = new Produit();
        $produit1->setName('Ordinateur portable');
        $produit1->setDescription('Ordinateur portable performant pour le travail.');
        $produit1->setPrice('999.99');
        $manager->persist($produit1);

        $produit2 = new Produit();
        $produit2->setName('Souris sans fil');
        $produit2->setDescription('Souris ergonomique avec connexion Bluetooth.');
        $produit2->setPrice('29.90');
        $manager->persist($produit2);

        $produit3 = new Produit();
        $produit3->setName('Clavier mécanique');
        $produit3->setDescription('Clavier mécanique rétroéclairé RGB.');
        $produit3->setPrice('89.50');
        $manager->persist($produit3);


        $manager->flush();
    }
}