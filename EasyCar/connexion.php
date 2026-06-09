<?php
session_start();

require 'config.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
  $email = $_POST['email'];
  $password = $_POST['password'];
  if(empty($_POST['email']) || empty($_POST['password'])){
    echo "Remplissez tout les champs";
    } else {
      $sql = "SELECT * FROM Utilisateur WHERE email = :email";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'email' => $email
            ]);
            $user = $stmt->fetchAll(PDO :: FETCH_ASSOC);
            if($user){
                foreach($user as $u){
                    if($u['mot_de_passe'] !== $password){
                        echo "Password incorrect!!";
                    }else{
                        // Successful login: set session data
                        $_SESSION['user'] =[
                            'id' => $u['id_utilisateur'],
                            'name' => $u['nom'],
                            'email' => $u['email']
                        ];
                        header('Location: category.php');
                        exit;
                  }

                }
            }else{
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
    <link rel="stylesheet" href="style_con.css">
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

        <button type="submit" class="btn-submit">S'inscrire</button>

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