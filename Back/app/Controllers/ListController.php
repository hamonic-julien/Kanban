<?php
//import des classes externs au namespace courant


namespace oKanban\Controllers;

class ListController extends CoreController {
    //méthode pour le endpoint /lists
    public function lists() {
        //Récuperer toutes les listes en BDD
        $lists = \oKanban\Models\ListModel::findAll('page_order ASC');
        //Utilisation de la méthode showJson pour encoder les données en JSON
        //dump($lists);
        $this->showJson($lists);
    }
    
    //méthode pour le endpoint list/[id]/update
    public function update($id) {
        //On récupère les données envoyées en POST
        $name = isset($_POST['listName']) ? trim($_POST['listName']) : '';
        //Récupérer la liste concerné avec le Model
        $listModel = \oKanban\Models\ListModel::find($id);
        //Si le modèle existe
        if (!empty($listModel)) {
            //Si le nom n'est pas vide
            if (!empty($name)) {
                //Alors je modifie et enregistre le model
                $listModel->setName($name)->save();
            }
            //j'affiche la version JSON du model modifié
            $this->showJson([
                'code' => 1,
                'model' => $listModel
                ]);
        }
        else {
            //j'affiche un message d'erreur
            $this->showJson([
                'code' => 2,
                'error' => 'Aucune liste trouvée à l\'id ' . $id
            ]);  
        }
    }

    //méthode pour le endpoint list/add
    public function add() {
        //On récupère les données en POST
        $name = isset($_POST['listName']) ? trim($_POST['listName']) : '';
        //Si le nom n'est pas vide 
        if(!empty($name)) {
            //je crée un model
            $listModel = new \oKanban\Models\ListModel();
            //je défini le nom
            $listModel->setName($name);
            //je défini le page_order
            $listModel->setPage_order(99);
            //je save enBDD
            $listModel->save();
            //j'affiche le json
            $this->showJson([
                'code' => 1,
                'model' => $listModel
            ]);
        }
        //Sinon, j'affiche un json d'erreur
        else {
            $this->showJson([
                'code' => 2,
                'error' => 'le nom ne peut être vide'
            ]);  
        }

    }
}

