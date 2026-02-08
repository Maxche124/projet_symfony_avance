<?php


namespace App\Tests\Entity;
use App\Entity\Client;
use App\Entity\User;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{

    public function testToString(): void{
        $user = new User();
        $user->setFirstName('John')->setLastName('Doe')->setEmail('johnDoe@orange.fr')->setPassword('password');
        $string=$user->toString();
        $this->assertStringContainsString('John', $string);
        $this->assertStringContainsString('Doe', $string);
        $this->assertStringContainsString('johnDoe@orange.fr', $string);
        $this->assertStringContainsString('password', $string);
    }

    public function testSetGetFirstName():void{
        $user=new User();
        $user->setFirstName('John');
        $this->assertEquals('John', $user->getFirstName());
    }

    public function testSetGetLastName():void{
        $user=new User();
        $user->setLastName('Doe');
        $this->assertEquals('Doe', $user->getLastName());
    }

    public function testSetGetAddress():void{
        $user=new User();
        $user->setAdresse('1 rue bleu');
        $this->assertEquals('1 rue bleu', $user->getAdresse());
    }

    public function testSetGetEmail():void{
        $user=new User();
        $user->setEmail('johnDoe@orange.fr');
        $this->assertEquals('johnDoe@orange.fr', $user->getEmail());
        $this->assertEquals('johnDoe@orange.fr', $user->getUserIdentifier());
    }

    public function testGenderOk():void{
        $user=new User();
        $user->setGender(User::GENDER_FEMALE);
        $this->assertEquals(User::GENDER_FEMALE, $user->getGender());
    }

    public function testGenderPasOk():void{
        $this->expectException(InvalidArgumentException::class);
        $user=new User();
        $user->setGender('invalide');
    }

    public function testPassword():void{
        $user=new User();
        $user->setPassword('password');
        $this->assertEquals('password', $user->getPassword());
    }

    public function testRoleToujoursAuMoinsUsers():void{
        $user = new User();
        $user->setRoles(['ROLE_MANAGER']);
        $roles = $user->getRoles();
        $this->assertTrue(in_array('ROLE_USER', $roles, true));
    }

    public function testSetGestionnaire():void{
        $user=new User();
        $user->setRoles(['ROLE_MANAGER']);
        $user->setGestionnaire();
        $roles=$user->getRoles();

        $this->assertContains('ROLE_GESTIONNAIRE', $roles);
        $this->assertContains('ROLE_USER', $roles);
        $this->assertContains('ROLE_MANAGER', $roles);
    }

    public function testRemoveGestionnaire():void{
        $user=new User();
        $user->setRoles(['ROLE_MANAGER']);
        $user->setGestionnaire();
        $user->removeGestionnaire();
        $roles=$user->getRoles();
        $this->assertNotContains('ROLE_GESTIONNAIRE', $roles);
        $this->assertContains('ROLE_USER', $roles);
        $this->assertContains('ROLE_MANAGER', $roles);
    }

    public function testGetIdentity():void{
        $user=new User();
        $user->setFirstName('John')->setLastName('Doe');
        $this->assertEquals('John Doe', $user->getIdentity());
    }

    public function testClient():void{
        $user=new User();
        $client=new Client();
        $user->setClient($client);
        $this->assertEquals($client, $user->getClient());
        $this->assertEquals($user,$client->getUser());
    }


}
