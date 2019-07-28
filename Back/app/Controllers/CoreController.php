<?php

namespace oKanban\Controllers;

abstract class CoreController {

    public function notFound () {
        header("HTTP/1.0 404 Not Found");
        $this->show('404');
    }

    public function show($tpl_name, $viewVars = []) {
        //extrat crée des variables à partir des clés d'un tableau et leur attribue les valeurs du tableau
        extract($viewVars);
        
        require __DIR__.'/../views/header.tpl.php';
        require __DIR__.'/../views/'.$tpl_name.'.tpl.php';
        require __DIR__.'/../views/footer.tpl.php';
    }

    protected function showJson($data) {
        // Autorise l'accès à la ressource depuis n'importe quel autre domaine
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Credentials: true');
        // Dit au navigateur que la réponse est au format JSON
        header('Content-Type: application/json');
        // La réponse en JSON est affichée
        echo json_encode($data, JSON_PRETTY_PRINT);
    }
}