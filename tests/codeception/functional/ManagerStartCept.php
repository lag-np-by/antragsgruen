<?php

use tests\codeception\_pages\ManagerStartPage;

/**
 * @var \Codeception\Scenario $scenario
 */

$I = new AntragsgruenFunctionalTester($scenario);
$I->wantTo('ensure that ManagerStartPage works');
ManagerStartPage::openBy($I);
$I->see('das Antragstool', 'h1');