<?php

if (Core::Request("isAjax"))
{
    require_once("template-ajax.php");
}
else
{
    require_once("template-editor.php");
}
