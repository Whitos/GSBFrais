<?php

namespace App\Tests;

use App\Entity\Etat;
use PHPUnit\Framework\TestCase;

class EtatTest extends TestCase
{
    public function testEtatProperties()
    {
        $etat = new Etat();
        $etat->setLibelle('Saisie en cours');

        $this->assertEquals('Saisie en cours', $etat->getLibelle());
    }
}
