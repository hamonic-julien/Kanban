<?php

namespace oKanban\Models;

//j'importe la classe Database et PDO
use oKanban\Utils\Database;
use PDO;

class CardModel extends CoreModel {

    private $title;
    private $list_order;
    private $list_id;
    protected static $table = 'card';


    //Comme on implemente JSONSerializable, on doit déclarer une méthode jsonSerialize dans chaque Model
    //elle retourne un tableau asso représentant les données à encoder en JSON
    // jsonSerialize = 'GETTER' spécial json encodé 
    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'list_order' => $this->list_order,
            'list_id' => $this->list_id
        ];
    }

    //Métode permettant de récupérer les cartes pour une liste donnée
    public static function findByListId(int $id) {
        $sql = 'SELECT * FROM '. self::$table . ' WHERE list_id = :id ORDER BY list_order ASC;';
        $pdoStatement = DataBase::getPDO()->prepare($sql);
        $pdoStatement->bindValue(':id', $id, PDO::PARAM_INT);
        $pdoStatement->execute();
        $results = $pdoStatement->fetchAll(PDO::FETCH_CLASS, self::class);
        return $results;
    }


    /*TRANSFERT VERS CodeModel
    //Méthode permettant de retourner toutes les cartes
    public static function findAll() {//ici, static signifie que la méthode est liée à la classe et non à l'objet (donc on l'appel avec CardModel::findAll())
        //SELECT * FROM card
        $sql = "SELECT * FROM card";
        $pdoStatement = DataBase::getPDO()->query($sql);
        return $pdoStatement->fetchAll(PDO::FETCH_CLASS, self::class);//attention : tjs spécifier le FQCN de la classe
    }*/

    //Pour accéder à une propriété/méthode lié à un objet :
        //$object->property;
        //$object->method();
    //Pour accéder à une propriété/méthode lié à une classe :
        //maClasse::property;
        //maClasse::method();

    /* TRANSFERT VERS CoreModel
    //Méthode permettent de retourner les infos d'une carte pour l'id fourni
    public static function find(int $id){
        //SELECT * FROM card WHERE id = $id;
        $sql = "SELECT * FROM card WHERE id = :id";
        //On a une variable dans la requete donc on utilise "prepare"
        $pdoStatement = DataBase::getPDO()->prepare($sql);
        //je défini une valeur pour chaque jeton/token/placeholder
        $pdoStatement->bindValue(':id', $id, PDO::PARAM_INT);
        //Une fois la valeur affectée, on peut exécuter la requete
        $pdoStatement->execute();
        //Identifieur self signifie moi-même = la classe
        //dump(self);
        //On utilise directement le nom de la classe comme Identifieur
        echo ' FQCN de la classe: ';
        dump(CardModel::class); // = dump(self::class);
        //class est donc une propriété liée à la classe et crée par php pour stocker le FQCN de la classe
        //Identifieur $this signifie moi même = l'objet (l'instance)
        //dump($this);
        // => $this est une instance de self
        //Et ensuite récupérer le résultat sous forme d'objet
        return $pdoStatement->fetchObject(self::class);
    }*/

    //Méthode permettant de mettre à jour une carte dans la BDD
    protected function update() {
        $sql = "UPDATE `card` 
        SET title = :title, list_order = :listOrder, list_id = :listId, updated_at = NOW() 
        WHERE id = :id";
        $pdoStatement = DataBase::getPDO()->prepare($sql);
        $pdoStatement->bindValue(':id', $this->id, PDO::PARAM_INT);
        $pdoStatement->bindValue(':title', $this->title, PDO::PARAM_STR);
        $pdoStatement->bindValue(':listOrder', $this->list_order, PDO::PARAM_INT);
        $pdoStatement->bindValue(':listId', $this->list_id, PDO::PARAM_INT);
        $pdoStatement->execute();
        $updatedRows = $pdoStatement->rowCount();
        return ($updatedRows > 0) ;
    }

    /* TRANSFERT VERS CoreModel
    //Méthode permettant de supprimer une carte dans la BDD
    public function delete() {
        //DELETE FROM card WHERE id = ...
        //On a une variable dans la requete donc on utilise "prepare" plutot que exec pour se protèger des injections sql 
        $sql = 'DELETE FROM card WHERE id = :id';
        //je prépare ma requete à une future exécution
        $pdoStatement = Database::getPDO()->prepare($sql);
        //je défini une valeur pour chaque jeton/token/placeholder
        //Si numérique PDO::PARAM_INT
        //Sinon PDO::PARAM_STR
        $pdoStatement->bindValue(':id', $this->id, PDO::PARAM_INT);
        //Une fois la valeur affectée, on peut exécuter la requete
        $pdoStatement->execute();
        //Je veux récupérer le nombre de lignes affectées (supprimées)
        $deletedRows = $pdoStatement->rowCount();        
        //on retourne vrai si au moins une ligne a été affectée
        return ($deletedRows > 0);//bool
    }*/

    //Méthode permettant d'insérer une carte en BDD
    protected function insert(){
        // écriture de la requête avec des tokens
        // à la création d'une carte il faut indiquer : titre, place dans la liste, liste à laquelle elle appartient
        $sql = 'INSERT INTO `card` (title, list_order, list_id) VALUES (:title, :list_order, :list_id);';
        // préparation de la requête
        $pdoStatement = Database::getPDO()->prepare($sql);
        // affectation de valeurs aux tokens
        $pdoStatement->bindValue(':title', $this->title, PDO::PARAM_STR);
        $pdoStatement->bindValue(':list_order', $this->list_order, PDO::PARAM_INT);
        $pdoStatement->bindValue(':list_id', $this->list_id, PDO::PARAM_INT);
        // exécution de la requête
        $pdoStatement->execute();
        // récupération du nombre de ligne affectées
        $addedRows = $pdoStatement->rowCount();
        // on retourne true si addedRows > 0 et on récupère l'id généré par l'AI
        if ($addedRows > 0) {
            //je complète l'objet courant par l'id généré par MySQL
            $this->id = Database::getPDO()->lastInsertId();
            return true;
        }
        else {
            return false;
        };;
    }



    /**
     * Get the value of title
     */ 
    public function getTitle() : string
    {
        return $this->title;
    }

    /**
     * Set the value of title
     *
     * @return  string
     */ 
    public function setTitle(string $title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of list_order
     */ 
    public function getList_order() : int
    {
        return $this->list_order;
    }

    /**
     * Set the value of list_order
     *
     * @return  int
     */ 
    public function setList_order(int $list_order)
    {
        $this->list_order = $list_order;

        return $this;
    }

    /**
     * Get the value of list_id
     */ 
    public function getList_id() : int
    {
        return $this->list_id;
    }

    /**
     * Set the value of list_id
     *
     * @return  int
     */ 
    public function setList_id(int $list_id)
    {
        $this->list_id = $list_id;

        return $this;
    }


}