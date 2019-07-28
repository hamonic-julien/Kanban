<?php

namespace oKanban\Models;

//j'importe la classe Database et PDO
use oKanban\Utils\Database;
use PDO;

class LabelModel extends CoreModel {

    private $name;
    protected static $table = 'label';


    //Comme on implemente JSONSerializable, on doit déclarer une méthode jsonSerialize dans chaque Model
    //elle retourne un tableau asso représentant les données à encoder en JSON
    // jsonSerialize = 'GETTER' spécial json encodé 
    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }

    /* TRANSFERT VERS CoreModel
    //Méthode permettant de retourner tous les labels
    public static function findAll() {//ici, static signifie que la méthode est liée à la classe et non à l'objet (donc on l'appel avec CardModel::findAll())
        $sql = "SELECT * FROM label";
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
    //Méthode permettent de retourner les infos d'un label pour l'id fourni
    public static function find(int $id){
        //SELECT * FROM label WHERE id = $id;
        $sql = "SELECT * FROM label WHERE id = :id";
        //On a une variable dans la requete donc on utilise "prepare"
        $pdoStatement = DataBase::getPDO()->prepare($sql);
        //je défini une valeur pour chaque jeton/token/placeholder
        $pdoStatement->bindValue(':id', $id, PDO::PARAM_INT);
        //Une fois la valeur affectée, on peut exécuter la requete
        $pdoStatement->execute();
        //Et ensuite récupérer le résultat sous forme d'objet
        return $pdoStatement->fetchObject(self::class);
    }*/

    //Méthode permettant de mettre à jour un label dans la BDD
    protected function update() {
        $sql = "UPDATE `label` 
        SET name = :name, updated_at = NOW() 
        WHERE id = :id";
        $pdoStatement = DataBase::getPDO()->prepare($sql);
        $pdoStatement->bindValue(':id', $this->id, PDO::PARAM_INT);
        $pdoStatement->bindValue(':name', $this->name, PDO::PARAM_STR);
        $pdoStatement->execute();
        $updatedRows = $pdoStatement->rowCount();
        return ($updatedRows > 0) ;
    }

    /* TRANSFERT VERS CoreModel
    //Méthode permettant de supprimer un label dans la BDD
    public function delete() {
        //DELETE FROM label WHERE id = ...
        //On a une variable dans la requete donc on utilise "prepare" plutot que exec pour se protèger des injections sql 
        $sql = 'DELETE FROM label WHERE id = :id';
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

    //Méthode permettant d'insérer une label en BDD
    protected function insert(){
        // écriture de la requête avec des tokens
        // à la création d'un label il faut indiquer : name
        $sql = 'INSERT INTO `label` (name) VALUE (:name);';
        // préparation de la requête
        $pdoStatement = Database::getPDO()->prepare($sql);
        // affectation de valeurs aux tokens
        $pdoStatement->bindValue(':name', $this->name, PDO::PARAM_STR);
        // exécution de la requête
        $pdoStatement->execute();
        // récupération du nombre de ligne affectées
        $addedRows = $pdoStatement->rowCount();
        // on retourne true si addedRows > 0
        if ($addedRows > 0) {
            //je complète l'objet courant par l'id généré par MySQL
            $this->id = Database::getPDO()->lastInsertId();
            return true;
        }
        else {
            return false;
        };
    }


    /**
     * Get the value of name
     */ 
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */ 
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }
}