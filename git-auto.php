<?php

$tabFusion = glob(__DIR__. "/../fusion-code");
foreach($tabFusion as $dirFusion)
{
    if (is_dir($dirFusion))
    {
        $message = date("H-i-s");
        $tabCode = [
            "cd $dirFusion",
            "git add -A",
            "git commit -a -m \"commit $message\"",
            "git push",
        ];
        foreach($tabCode as $code)
        {
            // echo "($dirFusion)($code)";
            echo "($code)";
            // echo passthru($code);
            echo shell_exec($code);
    }
/*        
        $code = "cd $dirFusion; git add -A; git commit -a -m \"commit $message\"; git push";
<<<CODESHELL   
cd $dirFusion; git add -A; git commit -a -m "commit $message"; git push
CODESHELL;
*/
        // echo "($dirFusion)($code)";
        // echo exec($code);
        //system($code);
    }
}