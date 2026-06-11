<?php
// Démarrage de la session pour pouvoir stocker les données de l'utilisateur connecté
session_start();
// Inclusion du fichier de configuration pour se connecter à la base de données
require 'config.php';
// Vérification si le formulaire a été soumis via la méthode POST
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
  // Récupération des données saisies par l'utilisateur
  $email = $_POST['email'];
  $password = $_POST['password'];
  // Vérification si l'un des deux champs est vide
  if(empty($_POST['email']) || empty($_POST['password'])){
    echo "Remplissez tout les champs";
    } else {
      // Préparation de la requête SQL pour chercher l'utilisateur par son email (qui est UNIQUE)
        $sql = "SELECT * FROM Utilisateur WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        // Exécution de la requête avec l'email sécurisé contre les injections SQL
        $stmt->execute(['email' => $email]);
        // Récupération des données de l'utilisateur sous forme de tableau associatif
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        // Si l'utilisateur existe dans la base de données
            if($user){
              // Vérification si le mot de passe saisi correspond à celui stocké en base de données
                    if($user['mot_de_passe'] !== $password){
                        echo "Password incorrect!!";
                    }else{
// Connexion réussie : enregistrement des informations de l'utilisateur en session                        
                        $_SESSION['user'] =[
                            'id' => $user['id_utilisateur'],
                            'name' => $user['nom'],
                            'email' => $user['email']
                        ];
                        // Redirection automatique vers la page des catégories après connexion
                        header('Location: category.php');
                        exit;// Interruption du script pour valider la redirection
                  }

                }else{
                  // Message d'erreur si l'email n'existe pas du tout dans la table
              echo "Email introuvable";
            }
            }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="Style CSS/style_con.css">
</head>
<body>
    <header>
        <img src="assets/Logo.png" alt="EasyCar">
        <nav>
            <a href="accueil.php">Accueil</a>
            <a href="category.php">Nos voitures</a>
            <a href="connexion.php">Connexion</a>
            <a href="category.php" class="reserve">Reserver  Maintenant</a>
        </nav>
    </header>
  <!-- FORM CARD -->
  <main>
    <div class="card">
      <div class="card-title">
        <h1><span>Easy</span>Car</h1>
      </div>
      <p class="card-subtitle">Bon retour parmi nous !</p>

      <form method="post">


        <div class="form-group">
          <label for="email">Adresse Email</label>
          <input type="email" id="email" name="email"/>
        </div>

        <div class="form-group">
          <label for="mdp">Mot de passe</label>
          <input type="password" id="mdp" name="password"/>
        </div>

        <button type="submit" class="btn-submit">Se connecter</button>

        <div class="form-footer">
          <a href="inscrire.php">Pas encore de compte ? s’inscrire</a>
          <a href="#" class="forgot">Mot de passe oublier</a>
        </div>
      </form>
    </div>
  </main>
  <footer>
    <div class="footer-grid">

      <div class="footer-brand">
        <h2><span>Easy</span>Car</h2>
        <p>La solution numérique centralisée pour<br>gérer et réserver votre véhicule à<br>Tanger en toute sécurité et simplicité.</p>
      </div>

      <div class="footer-col">
        <h3>Liens Rapides</h3>
        <ul>
          <li><a href="accueil.php">Accueil</a></li>
          <li><a href="category.php">Nos Voitures</a></li>
          <li><a href="connexion.php">Connexion</a></li>
        </ul>
      </div>

      <div class="footer-col footer-contact">
        <h3>Contact d'agence</h3>
        <p>Avenue Mohamed V,<br>Tanger, Maroc</p>
        <p>+212 600-000-000</p>
        <p>contact@easycar.ma</p>
        <div class="social-icons">
          <div class="social-icon fb">f</div>
          <div class="social-icon wa">W</div>
        </div>
      </div>

    </div>

    <div class="footer-bottom">
      © 2026 EasyCar. Tous droits réservés.
    </div>
  </footer>

</body>
</html>