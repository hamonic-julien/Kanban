<?php

namespace oKanban\Models;

//j'importe la classe Database et PDO
use oKanban\Utils\Database;
use PDO;

abstract class CoreModel implements \JSONSerializable {
    
    /**
     *@var int
     */
    protected $id;

     /**
     *@var string
     */
    protected $created_at;

    /**
     *@var string
     */
    protected $updated_at;


    //Méthode permettant de retourner toutes les listes
    public static function findAll($order=null) {//ici, static signifie que la méthode est liée à la classe et non à l'objet (donc on l'appel avec CardModel::findAll())
        //SELECT * FROM list
        //j'écris ma requète avec une variable qui est une proriété statique de ma classe
        /**
         * self::$table
         * => self => classe actuelle : CoreModel
         * => $table => propriété statique (sur la classe)
         * static::$table
         * => static = la classe depuis laquelle on a appelé la méthode statique
         * => $table => propriété statique (sur la classe)
         * **self fait ici référence à CoreModel, static fera référence au Model depuis lequel il est utilisé**
         * Exemples : 
         * $lists = ListModel::findAll() : identifieur static = ListModel
         * $label = LabelModel::findAll() : identifieur static = LabelModel
         * $card = CardModel::findAll() : identifieur static = CardModel
         * 
         * */
        $sql = "SELECT * FROM " . static::$table; 
        if ($order != '') {
            $sql .= ' ORDER BY ' . $order;
        }
        //pour visualiser la différence self et static suivant la classe
        //echo "<p>self = ".self::class . "</p>";
        //echo "<p>static = ".static::class . "</p>";
        $pdoStatement = DataBase::getPDO()->query($sql);
        return $pdoStatement->fetchAll(PDO::FETCH_CLASS, static::class);//static remplace là aussi self
    }

    //Méthode permettent de retourner les infos d'un label pour l'id fourni
    public static function find(int $id){
        //SELECT * FROM label WHERE id = $id;
        $sql = "SELECT * FROM " . static::$table . " WHERE id = :id";
        //On a une variable dans la requete donc on utilise "prepare"
        $pdoStatement = DataBase::getPDO()->prepare($sql);
        //je défini une valeur pour chaque jeton/token/placeholder
        $pdoStatement->bindValue(':id', $id, PDO::PARAM_INT);
        //Une fois la valeur affectée, on peut exécuter la requete
        $pdoStatement->execute();
        //Et ensuite récupérer le résultat sous forme d'objet
        return $pdoStatement->fetchObject(static::class);
    }

    //Méthode permettant de supprimer une liste dans la BDD
    //même si la méthode n'est pas static, je peux accéder à des propriétés statiques de la classe
    public function delete() {
        //DELETE FROM list WHERE id = ...
        //On a une variable dans la requete donc on utilise "prepare" plutot que exec pour se protèger des injections sql 
        $sql = 'DELETE FROM ' . static::$table . 'WHERE id = :id';
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
    }

    //Méthode permettant de sauvegarder en BDD l'objet courant
    //Elle va insérer ou mettre à jour selon le besoin
    public function save() {
        //si l'id est défini
        if($this->id > 0) {
            //alors on met à jour
            return $this->update();
        }
        //Sinon, l'id n'existe pas donc la ligne n'existe pas en BDD
        else {
            //Alors on insère la ligne
            return $this->insert();
        }
    }
    //Comme CoreModel est abstract, elle peut forcer tous ses enfatns à déclarer certaines méthodes
    //Ici on force la déclaration des méthodes insert & update
    protected abstract function insert();
    protected abstract function update();


/****** ON NE PREND QUE LE GETTERS CAR PAS ENVI DE POUVOR MODIFIER CES PARAMETRES DE LA BDD *****/

    /**
     * Get the value of id
     */ 
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * Get the value of created_at
     */ 
    public function getCreated_at() : string
    {
        return $this->created_at;
    }

    /**
     * Get the value of updated_at
     */ 
    public function getUpdated_at() : string
    {
        return $this->updated_at;
    }

}