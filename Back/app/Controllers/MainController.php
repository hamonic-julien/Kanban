<?php
//import des classes externs au namespace courant
use oKanban\Utils\Database;
use oKanban\Models\CardModel;
//use \oKanban\Models\ListModel;
//use oKanban\Models\CardModel;

namespace oKanban\Controllers;

class MainController extends CoreController {

    public function home() {
        $this->show('home');
    }

    //Méthode pour tester nos CRUD sur les Models
    public function test() {
        $listModel = new \oKanban\Models\ListModel();
        $lists = $listModel->findAll();
        dump($lists);
        $firstList = $lists[0];
        dump($firstList);
        //$firstList->delete();
        
        //utilisation d'une méthode static
        $cards = \oKanban\Models\CardModel::findAll();
        dump($cards);
        $card = \oKanban\Models\CardModel::find(2);
        dump($card);
        $lists = \oKanban\Models\ListModel::findAll();
        dump($lists);
        $list = \oKanban\Models\ListModel::find(2);
        dump($list);
        $labels = \oKanban\Models\LabelModel::findAll();
        dump($labels);
        $label = \oKanban\Models\LabelModel::find(2);
        dump($label);

        /*
        $insert = new \oKanban\Models\CardModel;
        $insert->setTitle('titretest');
        $insert->setList_order(5);
        $insert->setList_id(3);
        echo '<h3> avant insertion</h3>';
        dump($insert);
        $insert->insert();
        echo '<h3> après insertion</h3>';
        dump($insert);*/

        /*//Je veux modifier la carte #4
        //Je commence par récupérer le model pour l'id4
        $cardModelId4 = \oKanban\Models\CardModel::find(4);//méthode static
        echo '<h3> récupération</h3>';
        dump($cardModelId4);
        //Je modifie l'objet directement
        $cardModelId4->setList_order(99);
        echo '<h3> Modification</h3>';
        dump($cardModelId4);
        //j'update la BDD
        $cardModelId4->update();
        echo '<h3> Update</h3>';
        dump($cardModelId4);*/

    }

}