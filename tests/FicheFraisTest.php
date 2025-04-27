<?php

namespace App\Tests;

use App\Entity\FicheFrais;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class FicheFraisTest extends TestCase
{
    public function testFicheFraisProperties()
    {
        $fiche = new FicheFrais();
        $user = new User();

        $mois = new \DateTime('2025-04-01'); //  On met une date complète ici
        $fiche->setMois($mois);
        $fiche->setNbJustificatifs(3);
        $fiche->setMontantValid(250.50);
        $fiche->setDateModif(new \DateTime('2025-04-27'));
        $fiche->setUser($user);

        $this->assertEquals($mois, $fiche->getMois()); //  Comparer deux DateTime
        $this->assertEquals(3, $fiche->getNbJustificatifs());
        $this->assertEquals(250.50, $fiche->getMontantValid());
        $this->assertInstanceOf(\DateTime::class, $fiche->getDateModif());
        $this->assertSame($user, $fiche->getUser());
    }

    public function testFicheFraisMoisDate()
    {
        $fiche = new FicheFrais();
        $mois = new \DateTime('2025-04-01');
        $fiche->setMois($mois);

        $this->assertInstanceOf(\DateTime::class, $fiche->getMois());
        $this->assertEquals('2025', $fiche->getMois()->format('Y'));
        $this->assertEquals('04', $fiche->getMois()->format('m'));
    }
}
