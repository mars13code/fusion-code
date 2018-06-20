<?php

// FIXME

Core::set("projet/private", __DIR__."/../private");

global $tabCheminController;
if (empty($tabCheminController)) $tabCheminController = [];

$tabCheminController[] = __DIR__;

// simply process form
$objController  = Core::Controller(); 
$objController->traiterForm();

// show results
$formGoal       = $objController->filtrer("--formGoal");
$objController->traiterForm($formGoal);

if (isset($tabResponse))
{
    echo json_encode($tabResponse, JSON_PRETTY_PRINT);
}

