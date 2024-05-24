<?php
include 'models/Propriete.php';
class ProprieteManager {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Méthode pour récupérer toutes les propriétés depuis la base de données.
     * 
     * Cette méthode permet de récupérer toutes les propriétés enregistrées dans la base de données.
     * 
     * @return array Un tableau contenant toutes les propriétés récupérées depuis la base de données.
     */
    public function getAllProprietes() {
        $proprietes = array();

        $sql = "SELECT * FROM propriete";
        $stmt = $this->db->executer($sql);

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $propriete = new Propriete($row['idProprio'], $row['nom'], $row['description'],$row['adresse'],$row['nombreChambre'],$row['tarif']);
            $proprietes[] = $propriete;
        }

        return $proprietes;
    }



    /**
     * Récupère l'image principale d'une propriété.
     *
     * Cette méthode récupère l'image principale d'une propriété à partir de la base de données.
     *
     * @param int $idProprio L'identifiant de la propriété pour laquelle récupérer l'image.
     * @return string|null L'image principale de la propriété ou null si aucune image n'est trouvée.
     */
    public function getProprieteImage($idProprio){
        try {
            $sql = "SELECT image FROM photo WHERE idProprio = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $idProprio);
            $stmt->execute();
    
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && isset($result['image'])) {
                return $result['image'];
            } else {
                return null;
            }
        } catch (PDOException $e) {
            // Gérer les erreurs de base de données
            echo "Erreur lors de la récupération de la photo du coach: " . $e->getMessage();
            return null;
        }
    }



    /**
     * Récupère l'identifiant de l'image principale d'une propriété.
     *
     * Cette méthode récupère l'identifiant de l'image principale associée à une propriété à partir de la base de données.
     *
     * @param int $idProprio L'identifiant de la propriété pour laquelle récupérer l'identifiant de l'image.
     * @return int|null L'identifiant de l'image principale de la propriété ou null si aucun identifiant n'est trouvé.
     */
    public function getIdImagePropriete($idProprio) {
        try {
            $query = "SELECT idPhoto FROM photo WHERE idProprio = :idProprio";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":idProprio", $idProprio);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
            // Vérifier si la requête a retourné des résultats
            if ($result !== false && isset($result['idPhoto'])) {
                return $result['idPhoto'];
            } else {
                return null; // Aucun résultat trouvé
            }
        } catch (PDOException $e) {
            // Gérer l'erreur de récupération de l'ID de l'image
            echo "Erreur lors de la récupération de l'ID de l'image : " . $e->getMessage();
            return false;
        }
    }
    

    /**
     * Récupère l'image associée à un utilisateur pour une propriété spécifique.
     *
     * Cette méthode récupère l'image associée à un utilisateur pour une propriété spécifique à partir de la base de données.
     *
     * @param int $idProprio L'identifiant de la propriété à laquelle appartient l'image.
     * @param int $idImage L'identifiant de l'image à récupérer.
     * @return string|null L'image associée à l'utilisateur pour la propriété spécifiée, ou null si aucune image n'est trouvée.
     */
    public function getImageByUtilisateur($idProprio, $idImage){
        try {
            $sql = "SELECT image FROM photo WHERE idProprio = :id AND idPhoto= :idPhoto";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $idProprio);
            $stmt->bindParam(':idPhoto', $idImage);
            $stmt->execute();
    
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && isset($result['image'])) {
                return $result['image'];
            } else {
                return null;
            }
        } catch (PDOException $e) {
            // Gérer les erreurs de base de données
            echo "Erreur lors de la récupération de la photo du coach: " . $e->getMessage();
            return null;
        }
    }


    /**
     * Récupère une propriété à partir de son identifiant.
     *
     * Cette méthode récupère les détails d'une propriété à partir de la base de données en fonction de son identifiant.
     *
     * @param int $id L'identifiant de la propriété à récupérer.
     * @return Propriete|null Un objet Propriete contenant les détails de la propriété récupérée, ou null si aucune propriété n'est trouvée.
     */
    public function getProprieteById($id) {
        try {
            $sql = "SELECT * FROM propriete WHERE idProprio = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
    
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                // Créez et retournez un objet Propriete avec les détails récupérés
                return new Propriete($result['idProprio'], $result['nom'], $result['description'], $result['adresse'], $result['nombreChambre'], $result['tarif']);
            } else {
                return null;
            }
        } catch (PDOException $e) {
            // Gérer les erreurs de base de données
            echo "Erreur lors de la récupération de la propriété: " . $e->getMessage();
            return null;
        }
    }



    /**
     * Récupère les propriétés appartenant à un utilisateur spécifique.
     *
     * Cette méthode récupère les détails des propriétés appartenant à un utilisateur spécifique à partir de la base de données.
     *
     * @param int $utilisateurId L'identifiant de l'utilisateur dont les propriétés doivent être récupérées.
     * @return array Un tableau d'objets Propriete contenant les détails des propriétés appartenant à l'utilisateur spécifié.
     */
    public function getProprietesByUtilisateurId($utilisateurId) {
        try {
            $sql = "SELECT * FROM propriete WHERE idUtilisateur = :utilisateurId";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':utilisateurId', $utilisateurId);
            $stmt->execute();
    
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            $proprietes = [];
    
            if ($result) {
                foreach ($result as $row) {
                    $propriete = new Propriete(
                        $row['idProprio'],
                        $row['nom'],
                        $row['description'],
                        $row['adresse'],
                        $row['nombreChambre'],
                        $row['tarif']
                    );
                    $proprietes[] = $propriete;
                }
            }
    
            return $proprietes;
        } catch (PDOException $e) {
            // Gérer les erreurs de base de données
            echo "Erreur lors de la récupération des propriétés de l'utilisateur : " . $e->getMessage();
            return [];
        }
    }
    
    /**
     * Insère une nouvelle propriété dans la base de données.
     *
     * Cette méthode insère les détails d'une nouvelle propriété dans la table 'propriete' et associe une image à cette propriété dans la table 'photo'.
     *
     * @param int $idUtilisateur L'identifiant de l'utilisateur propriétaire de la propriété.
     * @param string $nom Le nom de la propriété.
     * @param string $adresse L'adresse de la propriété.
     * @param string $description La description de la propriété.
     * @param int $nombreChambre Le nombre de chambres de la propriété.
     * @param float $tarif Le tarif de la propriété.
     * @param string $imagePath Le chemin de l'image associée à la propriété.
     * @return int|false L'identifiant de la propriété insérée s'il est inséré avec succès, sinon false.
     */
    public function insererPropriete($idUtilisateur, $nom, $adresse, $description, $nombreChambre, $tarif, $imagePath) {
        try {
            // Insérer les détails de la propriété dans la table propriete
            $sql = "INSERT INTO propriete (idUtilisateur, nom, adresse, description, nombreChambre, tarif) VALUES (:idUtilisateur, :nom, :adresse, :description, :nombreChambre, :tarif)";
            
            $stmt = $this->db->prepare($sql);
            
            $stmt->bindParam(':idUtilisateur', $idUtilisateur);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':adresse', $adresse);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':nombreChambre', $nombreChambre);
            $stmt->bindParam(':tarif', $tarif);
    
            $stmt->execute();
    
            $idPropriete = $this->db->lastInsertId();
    
            // Insérer l'image dans la table photo
            $this->insererPhoto($imagePath, $idPropriete);
    
            return $idPropriete;
        } catch (PDOException $e) {
            echo "Erreur lors de l'insertion de la propriété : " . $e->getMessage();
            return false;
        }
    }
    

    /**
     * Insère le chemin de l'image associée à une propriété dans la base de données.
     *
     * Cette méthode insère le chemin de l'image associée à une propriété dans la table 'photo'.
     *
     * @param string $imagePath Le chemin de l'image à insérer.
     * @param int $idPropriete L'identifiant de la propriété à laquelle l'image est associée.
     * @return bool Retourne true si l'insertion de l'image est réussie, sinon false.
     */
    public function insererPhoto($imagePath, $idPropriete) {
        try {
            $sql = "INSERT INTO photo (image, idProprio) VALUES (:image, :idProprio)";
            
            $stmt = $this->db->prepare($sql);
            
            $stmt->bindParam(':image', $imagePath);
            $stmt->bindParam(':idProprio', $idPropriete);
    
            $stmt->execute();
    
            return true;
        } catch (PDOException $e) {
            echo "Erreur lors de l'insertion de la photo : " . $e->getMessage();
            return false;
        }
    }


    /**
     * Modifie les détails d'une propriété dans la base de données.
     *
     * Cette méthode met à jour les détails d'une propriété, tels que le nom, la description, l'adresse,
     * le nombre de chambres et le tarif, dans la table 'propriete' de la base de données.
     *
     * @param int $id L'identifiant de la propriété à modifier.
     * @param string $nom Le nouveau nom de la propriété.
     * @param string $description La nouvelle description de la propriété.
     * @param string $adresse La nouvelle adresse de la propriété.
     * @param int $nombreChambre Le nouveau nombre de chambres de la propriété.
     * @param float $tarif Le nouveau tarif de la propriété.
     * @return bool Retourne true si la modification de la propriété est réussie, sinon false.
     */
    function modifierPropriete($id, $nom, $description, $adresse, $nombreChambre, $tarif) {
        
        try {
    
            // Mise à jour des données dans la base de données
            $sql = "UPDATE propriete SET nom = :nom, description = :description, adresse = :adresse, nombreChambre = :nombreChambre, tarif = :tarif WHERE idProprio = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':adresse', $adresse);
            $stmt->bindParam(':nombreChambre', $nombreChambre);
            $stmt->bindParam(':tarif', $tarif);
            $stmt->bindParam(':id', $id);
            $resultat = $stmt->execute();
    
            return $resultat;

        } catch (PDOException $e) {
            // Gérer les erreurs de base de données
            echo "Erreur lors de la modification de la propriété: " . $e->getMessage();
        }
    }
    


    /**
     * Supprime une propriété de la base de données.
     *
     * Cette méthode supprime une propriété et ses images associées de la base de données.
     *
     * @param int $idProprio L'identifiant de la propriété à supprimer.
     * @return bool Retourne true si la suppression de la propriété et de ses images associées est réussie, sinon false.
     */
    public function SupprimerPropriete($idProprio){
        try {
            $sql1= "DELETE FROM photo WHERE idProprio = :idProprio";
            $sql2 = "DELETE FROM propriete WHERE idProprio = :idProprio";
            $stmt1 = $this->db->prepare($sql1);
            $stmt2 = $this->db->prepare($sql2);
            $stmt1->bindParam(':idProprio', $idProprio);
            $stmt2->bindParam(':idProprio', $idProprio);
            $res1 = $stmt1->execute();
            $res2 = $stmt2->execute();

            if ($res1 && $res2) {
                return true;
            }else{
                return false;
            }

            

        } catch (PDOException $e) {
            echo "Erreur lors de la suppression de la propriété : " . $e->getMessage();
            return false;
        }
    }



    /**
     * Récupère le montant d'une propriété par son identifiant.
     *
     * Cette méthode récupère le montant d'une propriété à partir de son identifiant dans la base de données.
     *
     * @param int $idProprio L'identifiant de la propriété.
     * @return float|null Le montant de la propriété s'il est trouvé, sinon null.
     */
    public function getMontantProprieteById($idProprio) {
        try {
            // Préparation de la requête SQL
            $sql = "SELECT tarif FROM propriete WHERE idProprio = ?";
            $stmt = $this->db->prepare($sql);

            // Exécution de la requête avec l'ID de la propriété lié
            $stmt->execute([$idProprio]);

            // Récupération du montant de la propriété depuis le résultat de la requête
            $montantPropriete = $stmt->fetchColumn();

            // Retourner le montant de la propriété
            return $montantPropriete;
        } catch (PDOException $e) {
            // Gérer les erreurs PDO si nécessaire
            echo "Erreur lors de la récupération du montant de la propriété : " . $e->getMessage();
            return null;
        }
    }
    
}
?>
