<?php

namespace App\Tests\Entity;

use App\Entity\Client;
use App\Entity\Compte;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase{

    public function testGetIdentityOk():void{
        $user=$this->createMock(User::class);
        $user->method('getIdentity')->willReturn('Test User');

        $client=new Client();
        $client->setUser($user);
        $client->setNumero('1231231231');

        $this->assertEquals('Test User (1231231231)', $client->getIdentity());
    }

    public function testNumeroOk():void{
        $client=new Client();
        $client->setNumero('1231231231');

        $this->assertEquals('1231231231', $client->getNumero());
    }

    public function testNumeroInvalidNumberLength():void{
        $this->expectException(\InvalidArgumentException::class);

        $client=new Client();
        $client->setNumero('123123');
    }

    public function testNumeroInvalidWithLetter():void{
        $this->expectException(\InvalidArgumentException::class);

        $client=new Client();
        $client->setNumero('123123abcd');
    }

    public function testAddAccount():void{
        $client=new Client();
        $account=new Compte();
        $client->addAccount($account);
        $this->assertCount(1, $client->getAccounts());
        $this->assertEquals($client, $account->getOwner());
    }

    public function testAccountDoublon():void{
        $client=new Client();
        $account=new Compte();
        $client->addAccount($account);
        $client->addAccount($account);
        $this->assertCount(1, $client->getAccounts());
    }

    public function testRemoveAccount():void{
        $client=new Client();
        $account=new Compte();
        $client->addAccount($account);
        $client->removeAccount($account);
        $this->assertCount(0, $client->getAccounts());
        $this->assertNull($account->getOwner());
    }
}
