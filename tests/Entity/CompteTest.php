<?php


namespace App\Tests\Entity;

use App\Entity\Client;
use App\Entity\Compte;
use App\Exceptions\InvalidAmountFormat;
use App\Exceptions\InvalidStringFormat;
use PHPUnit\Framework\TestCase;

class CompteTest extends TestCase
{
    public function testSetNumeroOk():void{
        $compte = new Compte();
        $compte->setNumero("FR1231231231");
        $this->assertEquals("FR1231231231", $compte->getNumero());
    }

    public function testSetNumeroPasOk():void{
        $this->expectException(InvalidStringFormat::class);
        $compte = new Compte();
        $compte->setNumero('INVALID');
    }

    public function testDecouvert():void{
        $compte = new Compte();
        $this->assertFalse($compte->getDecouvertStatus());
        $compte->setDecouvertStatut(true);
        $this->assertTrue($compte->getDecouvertStatus());
    }

    public function testSetDecouvertMontantOk():void{
        $compte = new Compte();
        $compte->setDecouvertStatut(true);
        $compte->setSolde(300);
        $compte->setMontantDecouvert(150);
        $this->assertEquals(-150, $compte->getMontantDecouvert());
    }

    public function testCrediter():void{
        $compte = new Compte();
        $compte->SetSolde(300);
        $compte->crediter(100);
        $this->assertEquals(400, $compte->getSolde());
    }

    public function testCrediterNegatif():void{
        $this->expectException(InvalidAmountFormat::class);
        $compte = new Compte();
        $compte->crediter(-300);
    }

    public function testDebiter():void{
        $compte= new Compte();
        $compte->SetSolde(300);
        $compte->debiter(100);
        $this->assertEquals(200, $compte->getSolde());
    }

    public function testDebiterSommeNegative():void{
        $this->expectException(InvalidAmountFormat::class);
        $compte= new Compte();
        $compte->debiter(-400);
    }

    public function testDebiterSuperieurDecouvert():void{
        $this->expectException(InvalidAmountFormat::class);
        $compte= new Compte();
        $compte->SetSolde(300);
        $compte->setDecouvertStatut(true);
        $compte->setMontantDecouvert(50);
        $compte->debiter(400);
    }

    public function testOwner(): void
    {
        $client = new Client();
        $compte = new Compte();

        $compte->setOwner($client);
        $this->assertSame($client, $compte->getOwner());
    }

}
