<?php

$tabResponse = [];

$classForm = Core::Request()->getInput("classForm");
$methodForm = Core::Request()->getInput("methodForm");

if ($classForm != "")
{
    // DANGER: SHOULD FILTER ON PUBLIC CLASSES
    $tabResponse["$classForm@$methodForm"] = Core::getOne("Public$classForm")->$methodForm();
}
else
{
    $tabResponse["message"] = date("H:i:s") . "/$classForm/$methodForm";
}
