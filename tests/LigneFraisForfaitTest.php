<?php

namespace App\Tests;

use App\Entity\FraisForfait;
use App\Entity\LigneFraisForfait;
use PHPUnit\Framework\TestCase;

class LigneFraisForfaitTest extends TestCase
{
    public function testGetMontantLigneFraisForfait()
    {
        $fraisForfait = new FraisForfait();
        $fraisForfait->setMontant(20);

        $ligneFraisForfait = new LigneFraisForfait();
        $ligneFraisForfait->setQuantite(3);
        $ligneFraisForfait->setFraisForfaits($fraisForfait);

        $expectedMontant = 3 * 20;

        $this->assertEquals($expectedMontant, $ligneFraisForfait->getMontantTotalFraisForfait());
    }
}
