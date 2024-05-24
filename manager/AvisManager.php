<?php
include 'models/Avis.php';
class AvisManager {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    /**
     * Méthode pour récupérer les avis avec les informations de l'utilisateur par identifiant de propriété.
     * 
     * Cette méthode exécute une requête SQL pour obtenir les avis modérés associés à une propriété spécifique.
     * Les informations récupérées incluent le nom et le prénom de l'utilisateur, la note, le commentaire et la date de l'avis.
     * 
     * @param int $idPropriete L'identifiant de la propriété pour laquelle récupérer les avis.
     * @return array|null Un tableau associatif contenant les avis et les informations de l'utilisateur,
     *                    ou null en cas d'erreur de base de données.
     */
    public function getAvisByProprieteId($idPropriete) {
        try {
            //preparation de la requête
            $sql = "SELECT utilisateurs.nom, utilisateurs.prenom, avis_moderation.note, avis_moderation.commentaire, avis_moderation.date 
                    FROM avis_moderation 
                    INNER JOIN utilisateurs ON avis_moderation.idUtilisateur = utilisateurs.idUtilisateur
                    WHERE avis_moderation.idProprio = :id && avis_moderation.modere= 1 ";
            //execution de la requête
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $idPropriete, PDO::PARAM_INT);
            $stmt->execute();
            //recuperation de tout les avis 
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            // Gérer les erreurs de base de données
            echo "Erreur lors de la récupération des avis par identifiant de propriété: " . $e->getMessage();
            return null;
        }
    }

    /**
     * Méthode pour récupérer les avis non modérés avec les informations de l'utilisateur.
     * 
     * Cette méthode exécute une requête SQL pour obtenir les avis non modérés. 
     * Les informations récupérées incluent toutes les colonnes de la table avis_moderation ainsi que le nom et le prénom de l'utilisateur.
     * 
     * @return array|null Un tableau associatif contenant les avis non modérés et les informations de l'utilisateur,
     *                    ou null en cas d'erreur de base de données.
     */


    public function getAvisNonModere(){
        try{
            //preparation de la requête
            $sql = "SELECT avis_moderation.*, utilisateurs.nom AS nom_utilisateur, utilisateurs.prenom AS prenom_utilisateur
            FROM avis_moderation 
            INNER JOIN utilisateurs ON avis_moderation.idUtilisateur = utilisateurs.idUtilisateur
            WHERE avis_moderation.modere = 0";
            //execution de la requête
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            //recuperation de tout les avis non modérés
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }
        catch(PDOException $e){
            // Gérer les erreurs de base de données
            echo "Erreur lors de la récupération des avis non modérés: ". $e->getMessage();
            return null;
        }
    }


    /**
     * Méthode pour accepter un avis modéré.
     * 
     * Cette méthode met à jour l'état d'un avis dans la base de données pour indiquer qu'il a été modéré (accepté).
     * 
     * @param int $idAvis L'identifiant de l'avis à accepter.
     * @return bool Retourne true si la mise à jour a été réussie, ou false en cas d'erreur de base de données.
     */
    public function accepterAvis($idAvis) {
        try {
            //Preparation de la requête
            $sql = "UPDATE avis_moderation SET modere = 1 WHERE idAvis = :idAvis";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':idAvis', $idAvis, PDO::PARAM_INT);
            //execution de la requête
            $stmt->execute();
            
            // Retourne true si la mise à jour a été réussie
            return true;
        } catch(PDOException $e) {
            // Gérer les erreurs de base de données
            echo "Erreur lors de l'acceptation de l'avis: ". $e->getMessage();
            // Retourne false en cas d'erreur
            return false;
        }
    }


    /**
     * Méthode pour refuser (supprimer) un avis.
     * 
     * Cette méthode supprime un avis de la base de données en fonction de son identifiant.
     * 
     * @param int $idAvis L'identifiant de l'avis à supprimer.
     * @return bool Retourne true si la suppression a été réussie, ou false en cas d'erreur de base de données.
     */
    public function refuserAvis($idAvis) {
        try {
            //preparation de la requête
            $sql = "DELETE FROM avis_moderation WHERE idAvis = :idAvis";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':idAvis', $idAvis, PDO::PARAM_INT);
            //execution de la requête
            $stmt->execute();
            // Retourne true si la suppression a été réussie
            return true;
        } catch(PDOException $e) {
            // Gérer les erreurs de base de données
            echo "Erreur lors du refus de l'avis: ". $e->getMessage();
            // Retourne false en cas d'erreur
            return false;
        }
    }


    /**
     * Méthode pour ajouter un avis.
     * 
     * Cette méthode insère un nouvel avis dans la base de données avec les détails fournis.
     * 
     * @param Avis $avis Un objet de type Avis contenant les informations de l'avis à ajouter.
     * @return bool Retourne true si l'insertion a été réussie, ou false en cas d'échec.
     */
    public function ajouterAvis($avis) {
        //récuperation des informations pour les avis 
        $idPropriete = $avis->getIdProprio();
        $idUtilisateur = $avis->getIdUtilisateur();
        $commentaire = $avis->getCommentaire();
        $note = $avis->getNote();
        $date = $avis->getDate();
        $modere = $avis->getModere();
        //preparation de la requete
        $query = "INSERT INTO avis_moderation (idProprio, idUtilisateur, commentaire, note, date, modere) 
                  VALUES (:id_propriete, :id_utilisateur, :commentaire, :note, :date, :modere)";
        //liaison avec les paramètres
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id_propriete", $idPropriete);
        $stmt->bindParam(":id_utilisateur", $idUtilisateur);
        $stmt->bindParam(":commentaire", $commentaire);
        $stmt->bindParam(":note", $note);
        $stmt->bindParam(":date", $date);
        $stmt->bindParam(":modere", $modere);
        //execution de  la requête
        if ($stmt->execute()) {
            return true; 
        } else {
            return false; 
        }
    }


    /**
     * Méthode pour obtenir le nombre d'avis d'un utilisateur sur une propriété spécifique.
     * 
     * Cette méthode récupère le nombre d'avis d'un utilisateur donné sur une propriété spécifique dans la base de données.
     * 
     * @param int $idUtilisateur L'identifiant de l'utilisateur.
     * @param int $idPropriete L'identifiant de la propriété.
     * @return int|false Le nombre d'avis de l'utilisateur sur la propriété spécifiée, ou false en cas d'échec.
     */
    public function getNombreAvisUtilisateurSurPropriete($idUtilisateur, $idPropriete) {
        $query = "SELECT COUNT(*) AS nombre_avis FROM avis_moderation WHERE idUtilisateur = :id_utilisateur AND idProprio = :id_propriete";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id_utilisateur", $idUtilisateur);
        $stmt->bindParam(":id_propriete", $idPropriete);

        if ($stmt->execute()) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['nombre_avis'];
        } else {
            return false;
        }
    } 
}

    

?>
