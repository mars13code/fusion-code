<?php 

// for editor
$tabLang = [
    "js"    => "javascript",
    "md"    => "markdown",
    ];
// main info
$action         = $this->filtrer("action");

// session management
$recordDir      = Core::get("projet/private")."/media";
$ajaxSession    = $this->filtrer("ajaxSession");

// fixme: security with session
$userLevel = 0;
$sessionFile = "";
if ($ajaxSession)
{
    $sessionFile = "$recordDir/session-$ajaxSession.log";
    if (is_file($sessionFile)) {
        $userLevel = 1;
    }
}


$tabReponse = [];
$tabReponse["dateResponse"] = date("H:i:s");
$tabReponse["action"]       = $action;

$filename   = $this->filtrer("filename");
$firstFound = "";
// SECURITY: ONLY TEXT FILES
$extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
$rootDir0 = Core::get("rootdir0");
$searchDir0 = $rootDir0;

if (in_array($extension, [ "html", "htm", "css", "js", "php", "md", "txt", "json", "sql", "csv", "sh", "log" ]))
{
    $filename = str_replace("..", "", $filename);
    $tabFound = glob($searchDir0.$filename);
    if (count($tabFound) > 0)
    {
        $firstFound = $tabFound[0];
    }
}
else
{
    $tabReponse["error"] = "$extension";
}

if ($userLevel > 0)
{
    if ($action == "run2")
    {
        $code       = $this->filtrerCode("code");
        if (in_array($code, [ "/git-auto.sh", "/synchro.php", "/synchro-zip-plugin.php", "/synchro-zip-theme.php" ]))
        {
            $firstFound = realpath($rootDir0.$code);
            $filename   = file_get_contents($firstFound);
        }
    }

    if (in_array($action, [ "run", "run2" ]))
    {
        if (is_file($firstFound))
        {
            $code       = $this->filtrerCode("code");
            $fileextension = pathinfo($firstFound, PATHINFO_EXTENSION);
            if (in_array($fileextension, [ "php" ]))
            {
                ob_start();

                // http://php.net/manual/en/function.chdir.php
                echo "(PHP)($firstFound)";
                $curdir = dirname($firstFound);
                chdir($curdir);
                // debug 

                // http://php.net/manual/fr/function.shell-exec.php
                require_once($firstFound);

                $tabReponse["ajaxMessage"] = ob_get_clean();
            }

            if (in_array($fileextension, [ "sh" ]))
            {
                ob_start();

                // http://php.net/manual/en/function.chdir.php
                echo "($firstFound)";
                $curdir = dirname($firstFound);
                chdir($curdir);
                echo getcwd() . "\n";
                // debug 
                $code = file_get_contents($firstFound);
                $code = str_replace("./", "$curdir/", $code);
                
                // http://php.net/manual/fr/function.shell-exec.php
                echo shell_exec($code);
                $tabReponse["ajaxMessage"] = ob_get_clean();
            }
        }
    }

    if ($action == "actDirCreate")
    {
        $dirCreate = $this->filtrer("dirCreate");
        $targetFile    = $searchDir0.$dirCreate;
        $firstFound = realpath($targetFile);
        if (!file_exists($targetFile))
        {
            // create a new dir
            // http://php.net/manual/fr/function.mkdir.php
            mkdir($targetFile);
            $tabReponse["ajaxMessage"] = "NEW DIR ($targetFile)";
        }        
    }

    if ($action == "actFileCreate")
    {
        $fileCreate = $this->filtrer("fileCreate");
        $targetFile    = $searchDir0.$fileCreate;
        $firstFound = realpath($targetFile);
        if (!file_exists($targetFile))
        {
            Core::Model()->insererLigne("Code", [ 
                                "filename"  => $targetFile, 
                                "code"      => "",
                                "codeOld"   => "",
                                "date"      => date("Y-m-d H:i:s"),
                                "ip"        => $_SERVER["REMOTE_ADDR"],
                                ]);
            // create a new file
            file_put_contents($targetFile, "");
            $tabReponse["ajaxMessage"] = "NEW FILE ($targetFile)";
        }        
    }

    if ($action == "actDirDelete")
    {
        $fileDelete = $this->filtrer("dirDelete");
        $targetFile = $searchDir0.$fileDelete;
        $firstFound = realpath($targetFile);
        if (is_dir($firstFound))
        {
            // FIXME: add some security
            // delete dir
            // http://php.net/manual/fr/function.rmdir.php
            rmdir($targetFile);
            $tabReponse["ajaxMessage"] = "DELETED DIR ($targetFile)";
        }
        else
        {
            $tabReponse["ajaxMessage"] = "DIR NOT FOUND ($targetFile)";
        }
        
    }

    if ($action == "actFileDelete")
    {
        $fileDelete = $this->filtrer("fileDelete");
        $targetFile    = $searchDir0.$fileDelete;
        $firstFound = realpath($targetFile);
        if (is_file($firstFound))
        {
            // keep a backup
            $codeOld = file_get_contents($firstFound);
            if ($codeOld)
            {
                Core::Model()->insererLigne("Code", [ 
                                    "filename"  => $firstFound, 
                                    "code"      => "",
                                    "codeOld"   => $codeOld,
                                    "date"      => date("Y-m-d H:i:s"),
                                    "ip"        => $_SERVER["REMOTE_ADDR"],
                                    ]);
            }        

            // FIXME: add some security
            // delete file
            unlink($targetFile);
            $tabReponse["ajaxMessage"] = "DELETED FILE ($targetFile)";
        }
        else
        {
            $tabReponse["ajaxMessage"] = "FILE NOT FOUND ($targetFile)";
        }
        
    }

    if ($action == "actFileRename")
    {
        $fileRename = $this->filtrer("fileRename");
        $targetFile = $searchDir0.$fileRename;
        $firstFound = realpath($targetFile);
        $oldFile    = realpath($searchDir0.$filename);
        if (file_exists($oldFile) && !file_exists($firstFound))
        {
            // only if file...
            if (is_file($oldFile))
            {
                $codeOld = file_get_contents($oldFile);
                if ($codeOld)
                {
                    Core::Model()->insererLigne("Code", [ 
                                        "filename"  => $firstFound, 
                                        "code"      => $codeOld,
                                        "codeOld"   => "",
                                        "date"      => date("Y-m-d H:i:s"),
                                        "ip"        => $_SERVER["REMOTE_ADDR"],
                                        ]);
                }
            }
            // FIXME: add some security
            // rename file
            // http://php.net/manual/fr/function.rename.php
            rename($oldFile, $targetFile);
            $tabReponse["ajaxMessage"] = "FILE RENAMED ($oldFile) => ($targetFile)";
        }
        else
        {
            $tabReponse["ajaxMessage"] = "FILE NOT FOUND ($oldFile) => ($targetFile)";
        }
        
    }

    if ($action == "upload")
    {
        $tabUpload = $_FILES["upload"] ?? [];
        extract($tabUpload);
        $targetDir = "";
        if (is_file($firstFound))
        {
            $targetDir = dirname($firstFound);
            $tabReponse["ajaxMessage"] = "UPLOAD $name TO FILE $filename ($firstFound)";
        }
        else
        {
            $firstFound = realpath($searchDir0.$filename);
            if (is_dir($firstFound)) {
                $targetDir = $firstFound;
                $tabReponse["ajaxMessage"] = "UPLOAD $name TO DIR $filename ($firstFound)";
            }
        }
        if (is_dir($targetDir))
        {
            // FIXME: ADD SECURITY...
            move_uploaded_file($tmp_name, "$targetDir/$name");
        }
    }

    if ($action == "save")
    {
        $code       = $this->filtrerCode("code");
        if (is_file($firstFound))
        {
            $codeOld = file_get_contents($firstFound);
            if ($codeOld != $code)
            {
                Core::Model()->insererLigne("Code", [ 
                                    "filename"  => $filename, 
                                    "code"      => $code,
                                    "codeOld"   => $codeOld,
                                    "date"      => date("Y-m-d H:i:s"),
                                    "ip"        => $_SERVER["REMOTE_ADDR"],
                                    ]);
                // warning: 
                // new code could break the program
                // so better save the new code after backup in MYSQL...
                file_put_contents($firstFound, $code);
                $tabReponse["ajaxUrlReload"] = $filename;
            }        
            $tabReponse["ajaxMessage"] = "- saved $filename - " . date("H:i:s");
        }    
    }
}


if ($action == "read")
{
    if (is_file($firstFound))
    {
        $fileextension = pathinfo($firstFound, PATHINFO_EXTENSION);
        // https://github.com/Microsoft/monaco-languages
        
        $tabReponse["filefound"]        = $firstFound;
        $tabReponse["filecontent"]      = file_get_contents($firstFound);
        $tabReponse["fileextension"]    = $fileextension;
        $tabReponse["codeLanguage"]     = $tabLang[$fileextension] ?? $fileextension;
        $tabReponse["ajaxMessage"]      = "(edit)";
    }
    
}

// JSON METHODS
$json       = $this->filtrer("json");
if ($json)
{
    ob_start();
    $tabJson    = json_decode($json, true);
    $jsonMethod = $tabJson["method"] ?? "";
    
    print_r($tabJson);
    if ($jsonMethod == "logout")
    {
        if (is_file($sessionFile)) {
            $userLevel = 0;
            // WARNING: DANGEROUS
            unlink($sessionFile);
            $tabReponse["ajaxSession"] = "";
            $tabReponse["ajaxMessage"] = "Bye Bye";    

            // should clean also old files
            $now = time();
            $tabSessionFile = glob("$recordDir/session-*.log");
            foreach($tabSessionFile as $curSessionFile)
            {
                // http://php.net/manual/fr/function.filemtime.php
                $modifTime = filemtime($curSessionFile);
                if (($now - $modifTime) > 3600 * 24)
                {
                    unlink($curSessionFile);
                }
            }
        }
    }

    if ($jsonMethod == "login")
    {
        $loginE = $tabJson["loginE"] ?? "";
        $loginP = $tabJson["loginP"] ?? "";

        $email0    = "admin@mars13.fr";        
        $password0 = '$2y$10$WA4IMrwWrwkEuZQbAzFQFeV9OMZDEmjTaQfy8dweEJiZB3f3da2yG'; // MUST USE ''
        if ( ($loginE == $email0) 
                && password_verify($loginP, $password0) )
        {
            $today = date("Y-m-d");
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            $loginKey = "$today/$userAgent/$loginE/";
            $mySessionId = base64_encode(password_hash($loginKey, PASSWORD_DEFAULT));
            //Core::Session()->save($mySessionId);
            file_put_contents("$recordDir/session-$mySessionId.log", $loginKey);               

            $tabReponse["ajaxSession"] = $mySessionId;
            $tabReponse["ajaxMessage"] = "WELCOME $loginE";    
        }
        else {
            $tabReponse["ajaxMessage"] = password_hash($loginP, PASSWORD_DEFAULT);    
            $tabReponse["ajaxSession"] = "...";
        }


    }

    if ($jsonMethod == "replay")
    {
        $recordSession  = md5($tabJson["recordSession"] ?? "");                
        $textJSON       = file_get_contents("$recordDir/$recordSession.log");
        $tabRecord      = json_decode($textJSON, true); 

        $tabReponse["ajaxReplay"] = $tabRecord;
        // extension for editor
        $recordExtension = pathinfo($tabRecord["recordFilename"], PATHINFO_EXTENSION);
        $tabReponse["codeLanguage"]     = $tabLang[$recordExtension] ?? $recordExtension;
    }

    if ($jsonMethod == "listFile")
    {
        $tabSearchDir   = [];
        $tabSearchDir = array_merge([""], $tabJson["tabSearchDir"] ?? []);

        //print_r($tabSearchDir);
        $explorer = Core::Explorer()->buildList($tabSearchDir);
        //print_r($explorer->tabData);
        $tabReponse["ajaxListFile"] = $explorer->tabData;
    }

    if ($userLevel > 0)
    {
        if ($jsonMethod == "record")
        {
            $recordSession      = md5($tabJson["recordSession"] ?? "");                
            $recordFilename     = $tabJson["recordFilename"] ?? "";                
            $recordLine         = $tabJson["recordLine"] ?? 0;
            $recordCode         = $tabJson["recordCode"] ?? 0;
            $recordSelection    = $tabJson["recordSelection"] ?? 0;
            
            $recordDir      = Core::get("projet/private")."/record";
            
            $tabRecord = compact("recordFilename", "recordLine", "recordCode", "recordSelection");
            //print_r($tabRecord);
            file_put_contents("$recordDir/$recordSession-code.log", base64_decode($recordCode));               
            file_put_contents("$recordDir/$recordSession.log", json_encode($tabRecord));               
        }

        if ($jsonMethod == "buildScreen")
        {
            $screen = $tabJson["screen"] ?? "";
            $builder = Core::Builder()->build($screen);
            $tabReponse["codeWindow"]   = $builder->codeWindow;
            $tabReponse["tabWindow"]    = $builder->tabWindow;
        }

    }

    $tabReponse["jsonMessage"] = ob_get_clean();
    
}

echo json_encode($tabReponse);