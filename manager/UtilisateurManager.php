<?php
include_once 'models/Utilisateur.php'; 

class UtilisateurManager {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }
    /**
     * Méthode pour récupérer un utilisateur par son identifiant.
     * 
     * Cette méthode exécute une requête SQL pour récupérer les informations d'un utilisateur à partir de son identifiant dans la base de données.
     * 
     * @param int $id L'identifiant de l'utilisateur à récupérer.
     * @return Utilisateur|null L'objet Utilisateur correspondant à l'identifiant spécifié, ou null si aucun utilisateur n'est trouvé.
     */
    public function getUtilisateurById($id) {
        // Préparez votre requête SQL pour récupérer un utilisateur par son ID
        $query = "SELECT * FROM utilisateurs WHERE idUtilisateur = :id";

        // Préparez la requête
        $statement = $this->db->prepare($query);

        // Liaison des paramètres
        $statement->bindParam(':id', $id, PDO::PARAM_INT);

        // Exécutez la requête
        $statement->execute();

        // Récupérer le résultat sous forme de tableau associatif
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        // Vérifiez si une ligne a été trouvée
        if ($row) {
            // Créez un objet Utilisateur à partir des données récupérées
            $utilisateur = new Utilisateur($row['idUtilisateur'], $row['nom'], $row['prenom'], $row['email'], $row['mdp'],$row['admin']);
            return $utilisateur; 
        } else {
            return null; 
        }
    }


    /**
     * Méthode pour modifier le mot de passe d'un utilisateur.
     * 
     * Cette méthode permet à un utilisateur de modifier son mot de passe en vérifiant d'abord l'ancien mot de passe,
     * puis en mettant à jour le mot de passe dans la base de données.
     * 
     * @param string $mdpActuel Le mot de passe actuel de l'utilisateur.
     * @param string $nouveauMdp Le nouveau mot de passe de l'utilisateur.
     * @param string $confirmMdp La confirmation du nouveau mot de passe de l'utilisateur.
     * @param int $idUtilisateur L'identifiant de l'utilisateur dont le mot de passe doit être modifié.
     * @return bool Retourne true si le mot de passe est modifié avec succès, sinon false.
     */
    public function modifierMDP($mdpActuel,$nouveauMdp,$confirmMdp,$idUtilisateur){

        $sqlMdpActuel = "SELECT mdp FROM utilisateurs WHERE idUtilisateur = :idUtilisateur";
    
        $requete = $this->db->prepare($sqlMdpActuel);

        $requete->bindParam(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);

        $requete->execute();

        $resultat = $requete->fetch(PDO::FETCH_ASSOC);

        if (!$resultat) {
            return false;
        }
        if (!password_verify($mdpActuel, $resultat['mdp'])) {
            return false;
        }
        if ($nouveauMdp !== $confirmMdp) {
            return false;
        }

        $nouveauMdpHash = password_hash($nouveauMdp, PASSWORD_DEFAULT);

        $sqlUpdateMdp = "UPDATE utilisateurs SET mdp = :nouveauMdp WHERE idUtilisateur = :idUtilisateur";
    
        $requeteUpdate = $this->db->prepare($sqlUpdateMdp);
    
        $requeteUpdate->bindParam(':nouveauMdp', $nouveauMdpHash);
        $requeteUpdate->bindParam(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
    
        if ($requeteUpdate->execute()) {
            return true;
        } else {
            return false;
        }


    
    }
    /**
     * Méthode pour modifier les informations d'un compte utilisateur.
     * 
     * Cette méthode permet à un utilisateur de modifier ses informations personnelles telles que le nom, le prénom et l'e-mail.
     * 
     * @param int $idUtilisateur L'identifiant de l'utilisateur dont les informations doivent être modifiées.
     * @param string $nom Le nouveau nom de l'utilisateur.
     * @param string $prenom Le nouveau prénom de l'utilisateur.
     * @param string $email Le nouvel e-mail de l'utilisateur.
     * @return bool Retourne true si les informations du compte sont modifiées avec succès, sinon false.
     */
    public function modifierInformationCompte($idUtilisateur, $nom, $prenom, $email){
        $sql = "UPDATE utilisateurs SET nom = :nom, prenom = :prenom, email = :email WHERE idUtilisateur = :idUtilisateur";
        $stmt =  $this->db->prepare($sql);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
    
        $resultat= $stmt->execute();
    
        if($resultat){
            return true;
        }else{
            return false;
        }
    }
    
}
?>
