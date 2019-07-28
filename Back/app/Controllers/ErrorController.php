<?php

namespace oKanban\Controllers;

class ErrorController extends CoreController {

    public function notFound () {
        //l'envoi du status 404 est géré par le dispatcher
        //header("HTTP/1.0 404 Not Found");
        //j'affiche une page 404
        $this->show('404');
    }

}