<?php
//PSR-4 : norme pour l'autoloading
// => chaque classe doit se trouver dans un namespace (dossier virtuel)
// chaque namespace correspond au dossier physique
// Si dossier physique = app/Application.php alors namespace: oKanban
namespace oKanban;

class Application {

    private $router;

    public function __construct() {
        //Instancier AltoRouter
        //Attention à cause du namespace il faut dire à PHP d'aller chercher la classe depuis la racine
        $this->router = new \AltoRouter();
        //Définir le setBasePath
        $baseUrl = isset($_SERVER['BASE_URI']) ? trim($_SERVER['BASE_URI']) : '/';
        $this->router->setBasePath($baseUrl);
        //j'appel la méthode qui map toutes les routes
        $this->mapRoutes();
    }

    public function mapRoutes(){
        //Mapper les routes 
        $this->router->map('GET', '/', [
            'controller' => '\oKanban\Controllers\MainController',
            'method' => 'home'
        ], 'home');
        $this->router->map('GET', '/lists', '\oKanban\Controllers\ListController::lists', 'list_lists');
        $this->router->map('POST', '/lists/add', '\oKanban\Controllers\ListController::add', 'list_add');
        $this->router->map('GET', '/lists/[i:id]', '\oKanban\Controllers\ListController::list', 'list_list');
        $this->router->map('POST', '/lists/[i:id]/update', '\oKanban\Controllers\ListController::update', 'list_update');
        $this->router->map('POST', '/lists/[i:id]/delete', '\oKanban\Controllers\ListController::delete', 'list_delete');
        $this->router->map('POST', '/lists/[i:id]/cards/add', '\oKanban\Controllers\CardController::add', 'card_add');
        $this->router->map('GET', '/lists/[i:id]/cards', '\oKanban\Controllers\CardController::cards', 'card_cards');
        $this->router->map('POST', '/cards/[i:id]/update', '\oKanban\Controllers\CardController::update', 'card_update');
        $this->router->map('POST', '/cards/[i:id]/delete', '\oKanban\Controllers\CardController::delete', 'card_delete');
        $this->router->map('GET', '/labels', '\oKanban\Controllers\LabelController::labels', 'label_labels');
        $this->router->map('POST', '/labels/add', '\oKanban\Controllers\LabelController::add', 'label_add');
        $this->router->map('GET', '/labels/[i:id]', '\oKanban\Controllers\LabelController::label', 'label_label');
        $this->router->map('POST', '/labels/[i:id]/update', '\oKanban\Controllers\LabelController::update', 'label_update');
        $this->router->map('POST', '/labels/[i:id]/delete', '\oKanban\Controllers\LabelController::delete', 'label_delete');
        $this->router->map('GET', '/cards/[i:id]/labels', '\oKanban\Controllers\CardController::labels', 'card_labels');
        $this->router->map('POST', '/cards/[i:id]/labels/add', '\oKanban\Controllers\CardController::addLabel', 'card_addLabel');
        $this->router->map('POST', '/cards/[i:idCard]/labels/[i:idLabel]/delete', '\oKanban\Controllers\CardController::deleteLabel', 'card_deleteLabel');


        //Exo E05
        $this->router->map('GET', '/test/', '\oKanban\Controllers\MainController::test', 'main_test');
    }

    public function run() {
        //Vérifier si l'url match
        $match = $this->router->match();
        //Attention à cause du namespace il faut dire à PHP d'aller chercher la classe depuis la racine
        $dispatcher = new \Dispatcher($match, '\oKanban\Controllers\ErrorController::notFound');
        $dispatcher->dispatch();
    }
}