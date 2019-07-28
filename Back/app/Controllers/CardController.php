<?php

namespace oKanban\Controllers;

class CardController extends CoreController {
    //méthode pour le endpoint /lists/[id]/cards
    public function cards($id) {
        //Récuperer toutes les cartes pour un id de liste donnée 
        /*$cards = \oKanban\Models\CardModel::findAll();
        foreach ($cards as $cardModel) {
            if($cardModel->getList_id() == $id) {
                $result [] = $cardModel;
            };
        };
        //Utilisation de la méthode showJson pour encoder les données en JSON
        //dump($cards);
        //dump($result);
        //dump($id);
        $this->showJson($result);*/
    
    
        //2eme façon
        $listCards = \oKanban\Models\CardModel::findByListId($id);
        $this->showJson($listCards);
    }
    
    //Méthode permettant l'ajout d'une carte
    public function add ($id) {
        //On récupère les données en POST
        $name = isset($_POST['cardName']) ? trim($_POST['cardName']) : '';
        //Si le nom n'est pas vide 
        if(!empty($name)) {
            //je crée un model
            $card = new \oKanban\Models\CardModel();
            //je défini le titre
            $card->setTitle($name);
            //je défini le list_order
            $card->setList_order(99);
            //je défini le list_id
            $card->setList_id($id);
            //je save enBDD
            $card->save();
            //j'affiche le json
            $this->showJson([
                'code' => 1,
                'model' => $card
            ]);
        }
        //Sinon, j'affiche un json d'erreur
        else {
            $this->showJson([
                'code' => 2,
                'error' => 'le nom de la carte ne peut être vide'
            ]);  
        }
    }

    //Méthode permettant la suppression d'une carte
    public function delete($id) {
        $card = \oKanban\Models\CardModel::find($id);
        $card->delete();
    }

    //Méthode pour le endpoint cards/[id]/update
    public function update($cardId) {
        //Je convertis $cardId en int
        $cardId = intval($cardId);
        //Récupération des données en POST
        $title = isset($_POST['cardName']) ? intval($_POST['cardName']) : '';
        $listId = isset($_POST['listId']) ? intval($_POST['listId']) : 0;
        $listOrder = isset($_POST['listOrder']) ? intval($_POST['listOrder']) : 0;
        //On veut modifier la carte si au moins une donnée à été fournie
        if(!empty($title) || !empty($listId) || !empty($listOrder)) {
            //On récupère alors le cardModel
            $cardModel = \oKanban\Models\CardModel::find($cardId);
            //Si l'id existe dans la table
            if(!empty($cardModel)) {
                //Alors je veux modifier le model selon les données fournies
                if(!empty($title)) {
                    $cardModel->setName($title);
                }
                if(!empty($listId)) {
                    $cardModel->setList_id($listId);
                }
                if(!empty($listOrder)) {
                    $cardModel->setList_order($listOrder);
                }
                //On save()
                $cardModel->save();
                //On affiche la réponse en json
                $this->showJson([
                    'code' => 1,
                    'model' => $cardModel
                ]);
            }
            else {
                $this->showJson([
                    'code' => 2,
                    'error' => 'Aucun post-it trouvé poyur l\id #' . $cardId
                ]);
            }
        }
        else {
            $this->showJson([
                'code' => 3,
                'error' => 'Aucune donnée fournie'
            ]);
        }
    }
}