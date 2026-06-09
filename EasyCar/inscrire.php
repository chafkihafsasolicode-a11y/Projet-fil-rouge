<?php
session_start();
require 'config.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
  $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $telephone = $_POST['telephone'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    if(!empty($nom) && !empty($prenom) && !empty($telephone) && !empty($email) && !empty($password)){
      // Password complexity rules
            if(strlen($password)<8){
                echo "Password doit contient 8 caractéres.";
            }
            if(!preg_match("/[0-9]/",$password)){
                echo "Password doit contient au moins un chiffre.";
            }
            if(!preg_match("/[A-Z]/",$password)){
                echo "Password doit contient au moins une majuscule.";
            }
        $sql = "INSERT INTO Utilisateur (nom, prenom, email, mot_de_passe) VALUES (:nom, :prenom, :email, :password)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'password' => $password
        ]);
        
        echo "Inscription réussie ! <a href='connexion.php'>Connectez-vous</a>";
    }else{
      echo "Veuillez remplir tous les champs.";
    }
  
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style_insc.css">
</head>
<body>
    <header>
        <img src="Logo.png" alt="EasyCar">
        <nav>
              <a href="accueil.php">Accueil</a>
              <a href="category.php">Nos voitures</a>
              <a href="connexion.php">Connexion</a>
              <button>Reserver  Maintenant</button>
        </nav>
    </header>
  <!-- FORM CARD -->
  <main>
    <div class="card">
      <div class="card-title">
        <h1><span>Easy</span>Car</h1>
      </div>
      <p class="card-subtitle">Bonjour à nous!</p>

      <form method="POST" action="">
          <div class="form-group">
              <label for="nom">Nom</label>
              <input type="text" id="nom" name="nom" required />
          </div>
          <div class="form-group">
              <label for="prenom">Prénom</label>
              <input type="text" id="prenom" name="prenom" required />
          </div>
          <div class="form-group">
              <label for="tel">Telephone</label>
              <input type="number" id="tel" name="telephone" required />
          </div>
          <div class="form-group">
              <label for="email">Adresse Email</label>
              <input type="email" id="email" name="email" required />
          </div>

          <div class="form-group">
              <label for="mdp">Mot de passe</label>
              <input type="password" id="mdp" name="password" required />
          </div>

          <button type="submit" class="btn-submit">S'inscrire</button>
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
          <li><a href="#">Accueil</a></li>
          <li><a href="#">Nos Voitures</a></li>
          <li><a href="#">Connexion</a></li>
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