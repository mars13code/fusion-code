<?php

// file:        Controller.php
// creation:    2018-05-27 20:58:29
// licence:     MIT
// author:      mars13.fr

class Controller
{
    // PROPERTIES
    
    // METHODS
    
    // constructor
    function __construct ()
    {
        // test to check versions and screen sharing
        // and is it on real time?
        // and how is it now?
        // so there is no real time possible?
    }

    function ecrireOption($cle, $valeur)
    {
        global $tabOption;
        $tabOption ?? $tabOption = [];
        $tabOption[$cle]         = $valeur;
    }

    function traiterForm(...$tabGoal)
    {
        global $tabCheminController;
        global $tabErreur; // obligatoire pour les fichiers de traitement...
    
        static $feedback = "";
        static $formGoal = "";
    
        if (empty($tabGoal)) {
            // CONTROLLER
            $formGoal = trim(strip_tags($_REQUEST["--formGoal"] ?? ""));
    
            if ($formGoal != "") {
                // http://php.net/manual/fr/function.ob-start.php
                ob_start();
    
                // ON NE PREND PAS EN COMPTE LE SUFFIXE
                // POUR PERMETTRE DE RASSEMBLER PLUSIEURS TRAITEMENTS DANS UN SEUL FICHIER
                // exemple: CRUD
    
                // http://php.net/manual/fr/function.pathinfo.php
                $formName = pathinfo($formGoal, PATHINFO_FILENAME);
                // sécurité: enlever les caractères spéciaux
                // http://php.net/manual/fr/function.preg-replace.php
                $formName = preg_replace("/[^a-zA-Z0-9-_]/i", "", $formName);
    
                foreach ($tabCheminController ?? [] as $cheminController) {
                    // http://php.net/manual/fr/function.glob.php
                    $tabFichier = glob("$cheminController/form-$formName.php");
                    foreach ($tabFichier as $fichier) {
                        require_once $fichier;
                    }
                }
                // http://php.net/manual/fr/function.ob-get-clean.php
                $feedback = ob_get_clean();
            }
        } elseif (in_array($formGoal, $tabGoal)) {
            // VIEW
            // http://php.net/manual/fr/function.in-array.php
            echo $feedback;
        }
    }

    /**
     * 
     */
    function extraireUri($rootDir)
    {
        $uri = $_SERVER["REQUEST_URI"];
        // http://php.net/manual/fr/function.parse-url.php
        $path = parse_url($uri, PHP_URL_PATH);
        $path = str_replace($rootDir, "", $path);
    
        $result = $path;
        // cas spécial pour apache => "/" utilise "/index.php"
        if ($result == "/") {
            $result = "/index.php";
        }
    
        // enlever le suffixe
        // http://php.net/manual/fr/function.pathinfo.php
        $filename  = pathinfo($result, PATHINFO_FILENAME);
        $extension = pathinfo($result, PATHINFO_EXTENSION);
    
        // memoriser pour les autres
        $this->ecrireOption("cms.path", $result);
        $this->ecrireOption("cms.filename", $filename);
        $this->ecrireOption("cms.extension", $extension);
    
        return $filename;
    }

    function ecrireSession($tabLigne)
    {
        // http://php.net/manual/fr/function.session-start.php
        $_SESSION ?? session_start();
        foreach ($tabLigne as $cle => $valeur) {
            $_SESSION[$cle] = $valeur;
        }
    }
    
    function lireSession($cle, $defaut = "")
    {
        // http://php.net/manual/fr/function.session-start.php
        $_SESSION ?? session_start();
        return $_SESSION[$cle] ?? $defaut;
    }
    
    
    function ajouterErreur($message)
    {
        global $tabErreur;
        //$tabErreur ?? $tabErreur = []; // FAIT PAR PHP
        $tabErreur[] = "$message";
    
        return count($tabErreur);
    }
    
    /**
     * WARNING: keep external code! PHP can be dangerous!
     */
    function filtrerCode($name, $defaut = "")
    {
        $result = trim($_REQUEST["$name"] ?? $defaut);
        return $result;
    }

    function filtrer($name, $defaut = "")
    {
        $result = trim(strip_tags($_REQUEST["$name"] ?? $defaut));
        return $result;
    }
    
    function filtrerEntier($name, $min = null, $max = null)
    {
        $result = intval(filtrer($name));
        if (is_int($min) && ($result < $min)) {
            ajouterErreur("($name) trop petit");
        }
        if (is_int($max) && ($result > $max)) {
            ajouterErreur("($name) trop grand");
        }
        return $result;
    }
    
    function filtrerFloat($name, $min = null, $max = null)
    {
        // http://php.net/manual/fr/function.floatval.php
        $result = floatval(filtrer($name));
        if (is_float($min) && ($result < $min)) {
            ajouterErreur("($name) trop petit");
        }
        if (is_float($max) && ($result > $max)) {
            ajouterErreur("($name) trop grand");
        }
        return $result;
    }
    
    function filtrerTexte($name, $min = 0, $max = null)
    {
        $result = filtrer($name);
    
        if (mb_strlen($result) < intval($min)) {
            ajouterErreur("($name) trop petit");
        }
        if (is_int($max) && (mb_strlen($min) > intval($max))) {
            ajouterErreur("($name) trop grand");
        }
    
        return $result;
    }
    
    function filtrerEmail($name, $min = 0, $max = 200)
    {
        $result = filtrerTexte($name, $min, $max);
    
        // http://php.net/manual/fr/function.filter-var.php
        if (($result != "") && ($result !== filter_var($result, FILTER_VALIDATE_EMAIL))) {
            ajouterErreur("($name) email incorrect");
        }
        return $result;
    }

    // http://www.weirdog.com/blog/php/supprimer-les-accents-des-caracteres-accentues.html
    function wd_remove_accents($str, $charset='utf-8')
    {
        $str = htmlentities($str, ENT_NOQUOTES, $charset);
        
        $str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
        $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
        $str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères
        
        return $str;
    }
    
    function filtrerMultiId ($inputName)
    {
        $result = [];
        
        $tabMulti = $_REQUEST["$inputName"] ?? [];
        if (is_array($tabMulti))
        {
            foreach($tabMulti as $index => $valeur)
            {
                $result[] = intval($valeur);
            }
        }
        return $result;
    }
    
    
    function filtrerUpload($inputName)
    {
        $result = "";
    
        // todo: ameliorer le chemin du dossier... 
        $dossierUpload = $GLOBALS["dossierCMS"] . "/projet/upload";
        // http://php.net/manual/fr/function.mkdir.php
        if (!is_dir($dossierUpload)) mkdir($dossierUpload);
        
        if (isset($_FILES["$inputName"])
            && ($_FILES["$inputName"]["error"] == 0)) {
    
            extract($_FILES["$inputName"]);
    
            $extension = pathinfo($name, PATHINFO_EXTENSION);
            $extension = strtolower($extension);
            if (!in_array($extension, [ "jpg", "jpeg", "png", "gif", "mp4", "pdf", "csv", "txt", "md", "js", "css", "html", "ttf", "svg" ])) {
                ajouterErreur("(extension) $extension est interdit");
            }
            
            if ($size > 10 * 1024 * 1024) {
                ajouterErreur("(taille) $size est trop grand");
            }
    
            // peut être bloqué par une autre erreur du formulaire que l'upload
            if (empty($tabErreur)) {
        
                // securite: filtre les extensions possibles
                // (ne jamais autoriser php...)
                // http://php.net/manual/fr/function.in-array.php
                $filename = pathinfo($name, PATHINFO_FILENAME);
    
                // securite: enleve les caracteres speciaux et transforme en minuscules
                // http://php.net/manual/fr/function.preg-replace.php
                $filename = preg_replace("/[^a-zA-Z0-9-]/", "", $filename);
                $filename = strtolower($filename);
    
                //file_put_contents("$dossierUpload/toto.log", json_encode($_FILES["$inputName"]));
                move_uploaded_file($tmp_name, "$dossierUpload/$filename.$extension");
    
                $result = "$filename.$extension";
                
            }
                    
        } else {
            ajouterErreur("(echec) une erreur s'est produite durant le transfert. Un nouvel essai?");
        }
    
        return $result;
    }
    
    function filtrerAcces($cle, $valeur)
    {
        $valeurSession = lireSession($cle, 0);
        if ($valeur > $valeurSession) {
            ajouterErreur("($cle) $valeur <= $valeurSession");
        }
        return $valeurSession;
    }
    
}