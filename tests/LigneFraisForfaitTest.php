<?php

namespace App\Tests;

use App\Entity\FicheFrais;
use App\Entity\FraisForfait;
use App\Entity\LigneFraisForfait;
use App\Entity\LigneFraisHorsForfait;
use PHPUnit\Framework\TestCase;

class LigneFraisForfaitTest extends TestCase
{
    public function testMontantHorsForfaitPositif()
    {
        $ligne = new LigneFraisHorsForfait();
        $ligne->setMontant(150.00);
        $this->assertGreaterThan(0, $ligne->getMontant());
    }

    public function testLibelleNonVide()
    {
        $ligne = new LigneFraisHorsForfait();
        $ligne->setLibelle('Achat fournitures');
        $this->assertNotEmpty($ligne->getLibelle());
    }

    public function testDateEngagementValide()
    {
        $ligne = new LigneFraisHorsForfait();
        $date = new \DateTime('-6 months');
        $ligne->setDate($date);

        $this->assertInstanceOf(\DateTime::class, $ligne->getDate());
        $this->assertLessThanOrEqual(new \DateTime(), $ligne->getDate());
    }
}
