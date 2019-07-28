<?php
// Index.php est mon point d'entrée unique
//Ce fichier est donc le FrontController !

//inclusion de l'autoload de composer
require __DIR__ .'/../vendor/autoload.php';

/**
* Grace à l'autoload du psr-4, php va charger automatiquement les classes suivant la demande,
* donc plus besoin de faire les require (Class uniquement).
* On instancie uniquement Application, qui grâce à ses méthodes fait le mappage, le match et le dispatch
* (il faut toutefois utiliser un namespace pour chaque Class: chaque namespace correspond au dossier physique
* si dossier physique = app/Application.php alors namespace: oKanban)

inclusion des classes Controllers
require __DIR__.'/../app/Controllers/CoreController.php';
require __DIR__.'/../app/Controllers/MainController.php';
require __DIR__.'/../app/Controllers/ErrorController.php';
inclusion de nos models
require __DIR__.'/../app/Models/CoreModel.php';
inclusion de Application
require __DIR__.'/../app/Application.php';
inclusion de nos utils
require __DIR__.'/../app/Utils/Database.php';
*/
//Je définis un raccourci pour la classe, pour php 'Application' signifie 'oKanban\Application'
use oKanban\Application;
//On crée une instance de la classe application
$app = new Application();
//on lance l'application
$app->run();
