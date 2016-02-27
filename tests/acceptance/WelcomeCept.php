<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('ensure that frontpage works');
$I->amOnPage('/');
$I->seeElement('.home-intro');
$I->seeElement('.home-picto');
$I->seeElement('#mosa-screen');
$I->seeElement('#publication_footer');
