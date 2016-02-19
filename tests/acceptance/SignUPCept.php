<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('Check that one can sign up');
$I->amOnPage('/');
$I->click('//*[@id="nav-menu"]/div/div/div[5]/a');
$I->click('Sign up here!');
$I->see('Actually, I have an account.');
