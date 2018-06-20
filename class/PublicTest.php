<?php

// file:        PublicTest.php
// creation:    2018-06-20 20:21:27
// licence:     MIT
// author:      mars13.fr

class PublicTest
{
    // TRAITS
    use TraitOne;
    
    // PROPERTIES
    
    // METHODS
    
    // constructor
    function __construct ()
    {
        
    }

    function message ()
    {
        return date("H:i:s");
    }


    function getFileContent ($host, $root, $uri, $username, $password)
    {
        $url = "$host$root$uri";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "$url");
        curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');

        $result = curl_exec($curl);
        curl_close($curl);


        return $result;
    }
    function saveFileContent ($content, $host, $root, $uri, $username, $password)
    {
        $url = "$host$root$uri";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "$url");
        curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');

        $result = curl_exec($curl);
        curl_close($curl);

        return $result;
    }


    function listFile ($host, $root, $uri, $username, $password)
    {
        $url = "$host$root$uri";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "$url");
        curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PROPFIND');

        $result = curl_exec($curl);
        curl_close($curl);
        
        //$xml = new SimpleXMLElement($result);
        $xml = simplexml_load_string($result);
    
        // astuce: si on a un dossier alors on a une balise collection apres href
        $tabResponse = $xml->xpath('//d:href | //d:collection');
        $nom = "";
        $type = "";
        $compte = 0;
        $prec = null;
        $tabData = [];
        foreach($tabResponse as $index => $response)
        {
            $nom = $response->__toString();
            //echo "($index:$nom)";
            $dirname = "";
            $basename = "";
            $extension = "";
            if (($nom == "") && ($prec != null))
            {
                // ajouter precedent comme dossier
                $prec = str_replace("$root", "", $prec);
                rtrim($prec, "/");
                extract(pathinfo($prec));
                $tabData[] = [ "id" => $index, "title" => $prec, "filename" => $prec, "isdir" => "D", "level" => 0, "dirname" => $dirname, "basename" => $basename, "extension" =>$extension ];
            }
            elseif ($prec != null)
            {
                // le precedent est un fichier
                $prec = str_replace("$root", "", $prec);
                extract(pathinfo($prec));
                $tabData[] = [ "id" => $index, "title" => $prec, "filename" => $prec, "isdir" => "F", "level" => 0, "dirname" => $dirname, "basename" => $basename, "extension" =>$extension ];
            }
            $prec = $nom;
        }
        
        return $tabData;
    }
    
    function listTabDir ()
    {
        // https://docs.nextcloud.com/server/13/developer_manual/client_apis/WebDAV/index.html 
        $username = Core::Request()->getInput("webdavLogin", "");
        $password = Core::Request()->getInput("webdavPassword", "");
        $host = Core::Request()->getInput("webdavHost", "");
        $root = Core::Request()->getInput("webdavRoot", "/");
        $uri  = Core::Request()->getInput("filename", "/");
        
        $tabData = $this->listFile($host, $root, $uri, $username, $password);
        return $tabData;
    }

    function getFile ()
    {
        // https://docs.nextcloud.com/server/13/developer_manual/client_apis/WebDAV/index.html 
        $username = Core::Request()->getInput("webdavLogin", "");
        $password = Core::Request()->getInput("webdavPassword", "");
        $host = Core::Request()->getInput("webdavHost", "");
        $root = Core::Request()->getInput("webdavRoot", "/");
        $uri  = Core::Request()->getInput("filename", "/");

        $tabData["filecontent"] = $this->getFileContent($host, $root, $uri, $username, $password);
        $tabData["codeLanguage"] = "txt";

        return $tabData;
    }

    function saveFile ()
    {
        // https://docs.nextcloud.com/server/13/developer_manual/client_apis/WebDAV/index.html 
        $username = Core::Request()->getInput("webdavLogin", "");
        $password = Core::Request()->getInput("webdavPassword", "");
        $host = Core::Request()->getInput("webdavHost", "");
        $root = Core::Request()->getInput("webdavRoot", "/");
        $uri  = Core::Request()->getInput("filename", "/");

        // WARNING: CAN BE DANGEROUS
        $content  = Core::Request()->getInputCode("content", "");

        $tabData["ajaxMessage"] = $this->saveFileContent($content, $host, $root, $uri, $username, $password);

        return $tabData;
    }

    //@end
    
}