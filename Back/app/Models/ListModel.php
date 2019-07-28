<?php

namespace oKanban\Models;

//j'importe la classe Database et PDO
use oKanban\Utils\Database;
use PDO;

class ListModel extends CoreModel {
    
    private $name;
    private $page_order;

    //On crée un propriété statique => propriété liée à la classe
    //Elle contient le nom de la table lié à la classe ListModel
    protected static $table = 'list';

    //Comme on implemente JSONSerializable, on doit déclarer une méthode jsonSerialize dans chaque Model
    //elle retourne un tableau asso représentant les données à encoder en JSON
    // jsonSerialize = 'GETTER' spécial json encodé 
    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'page_order' => $this->page_order
        ];
    }

    /**
     * Méthodes CRUD  
     */
    
    /* TRANSFERT VERS CoreModel
    //Méthode permettant de retourner toutes les listes
    public static function findAll() {//ici, static signifie que la méthode est liée à la classe et non à l'objet (donc on l'appel avec CardModel::findAll())
        //SELECT * FROM list
        $sql = "SELECT * FROM " . self::$table; //j'écris ma requète avec une variable qui est une proriété statique de ma classe
        $pdoStatement = DataBase::getPDO()->query($sql);
        return $pdoStatement->fetchAll(PDO::FETCH_CLASS, 'oKanBan\Models\ListModel');//attention : tjs spécifier le FQCN de la classe
    }*/

    //Pour accéder à une propriété/méthode lié à un objet :
        //$object->property;
        //$object->method();
    //Pour accéder à une propriété/méthode lié à une classe :
        //maClasse::property;
        //maClasse::method();

    /* TRANSFERT VERS CoreModel
    //Méthode permettent de retourner les infos d'une liste pour l'id fourni
    public static function find(int $id){
        //SELECT * FROM list WHERE id = $id;
        $sql = "SELECT * FROM list WHERE id = :id";
        //On a une variable dans la requete donc on utilise "prepare"
        $pdoStatement = DataBase::getPDO()->prepare($sql);
        //je défini une valeur pour chaque jeton/token/placeholder
        $pdoStatement->bindValue(':id', $id, PDO::PARAM_INT);
        //Une fois la valeur affectée, on peut exécuter la requete
        $pdoStatement->execute();
        //Et ensuite récupérer le résultat sous forme d'objet
        return $pdoStatement->fetchObject(self::class);
    }*/

    //Méthode permettant de mettre à jour une liste dans la BDD
    protected function update() {
        $sql = "UPDATE `list` 
        SET name = :name, page_order = :pageOrder, updated_at = NOW() 
        WHERE id = :id";
        $pdoStatement = DataBase::getPDO()->prepare($sql);
        $pdoStatement->bindValue(':id', $this->id, PDO::PARAM_INT);
        $pdoStatement->bindValue(':name', $this->name, PDO::PARAM_STR);
        $pdoStatement->bindValue(':pageOrder', $this->page_order, PDO::PARAM_INT);
        $pdoStatement->execute();
        $updatedRows = $pdoStatement->rowCount();
        return ($updatedRows > 0) ;
    }

    /* TRANSFERT VERS CoreModel
    //Méthode permettant de supprimer une liste dans la BDD
    public function delete() {
        //DELETE FROM list WHERE id = ...
        //On a une variable dans la requete donc on utilise "prepare" plutot que exec pour se protèger des injections sql 
        $sql = 'DELETE FROM list WHERE id = :id';
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

    //Méthode permettant d'insérer une liste en BDD
    protected function insert(){
        // écriture de la requête avec des tokens
        // à la création d'une liste il faut indiquer : name et page_order
        $sql = 'INSERT INTO `list` (name, page_order) VALUES (:name, :page_order);';
        // préparation de la requête
        $pdoStatement = Database::getPDO()->prepare($sql);
        // affectation de valeurs aux tokens
        $pdoStatement->bindValue(':name', $this->name, PDO::PARAM_STR);
        $pdoStatement->bindValue(':page_order', $this->page_order, PDO::PARAM_INT);
        // exécution de la requête
        $pdoStatement->execute();
        // récupération du nombre de ligne affectées
        $addedRows = $pdoStatement->rowCount();
        // on retourne true si addedRows > 0
        if ($addedRows > 0) {
            //je complète l'objet courant par l'id généré par MySQL
            //ici je demande à PDO de demander à MySQL de nous retourner l'id crée
            $this->id = Database::getPDO()->lastInsertId();
            return false;
        }
        else {
            return false;
        };;
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
     * @return  string
     */ 
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of page_order
     */ 
    public function getPage_order() : int
    {
        return $this->page_order;
    }

    /**
     * Set the value of page_order
     *
     * @return  int
     */ 
    public function setPage_order(int $page_order)
    {
        $this->page_order = $page_order;

        return $this;
    }
}
