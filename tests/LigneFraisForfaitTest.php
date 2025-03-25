<?php

namespace App\Tests;

use App\Entity\FicheFrais;
use App\Entity\FraisForfait;
use App\Entity\LigneFraisForfait;
use PHPUnit\Framework\TestCase;

class LigneFraisForfaitTest extends TestCase
{
    public function testGetMontantLigneFraisForfait()
    {
        $ficheFrais = new FicheFrais();
        $fraisForfait = new FraisForfait();
        $fraisForfait->setMontant(20);

        $ligneFraisForfait = new LigneFraisForfait();


        $expectedMontant = 3 * 20;

        $this->assertEquals($expectedMontant, $ligneFraisForfait->getMontantTotalFraisForfait());
    }
}
