<?php
// Inclusion du fichier de configuration pour la connexion à la base de données via PDO
require 'config.php';
$errors = [];
// Vérification si le formulaire a été soumis via la méthode POST
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
  // Récupération des données envoyées par le formulaire d'inscription
  $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $telephone = $_POST['telephone'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    // Vérification que tous les champs obligatoires ne sont pas vides
    if (empty($nom) || empty($prenom) || empty($email) || empty($password)) {
        $errors[] = "Veuillez remplir tous les champs obligatoires.";
    } else {
// Validation de la sécurité du mot de passe : au moins 8 caractères
        if (strlen($password) < 8) {
            $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
        }
        // Validation de la sécurité du mot de passe : doit contenir au moins un chiffre
        if (!preg_match("/[0-9]/", $password)) {
            $errors[] = "Le mot de passe doit contenir au moins un chiffre.";
        }
        // Validation de la sécurité du mot de passe : doit contenir au moins une majuscule
        if (!preg_match("/[A-Z]/", $password)) {
            $errors[] = "Le mot de passe doit contenir au moins une lettre majuscule.";
        }
        // Vérification en base de données si l'adresse email existe déjà
        $checkSql = "SELECT id_utilisateur FROM Utilisateur WHERE email = :email";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute(['email' => $email]);
        // Si un enregistrement est trouvé, on ajoute une erreur pour éviter les doublons
        if ($checkStmt->fetch()) {
            $errors[] = "Cette adresse email est déjà utilisée.";
        }
        // Si le tableau d'erreurs est vide, on procède à l'inscription
        if (empty($errors)) {
        // Préparation de la requête SQL d'insertion (le rôle est défini par défaut sur 'client')
            $sql = "INSERT INTO Utilisateur (nom, prenom, email, mot_de_passe, role) VALUES (:nom, :prenom, :email, :password, 'client')";
            $stmt = $pdo->prepare($sql);
            // Exécution de la requête avec protection contre les injections SQL
            $stmt->execute([
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'password' => $password
            ]);
            // Redirection vers la page de connexion après une inscription réussie
            header('Location: connexion.php');
            exit; // Interruption du script pour valider la redirection
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
    <link rel="stylesheet" href="Style CSS/style_insc.css">
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
      <p class="card-subtitle">Bonjour à nous!</p>
      <?php if (!empty($errors)): ?>
          <div class="error-box">
              <ul>
                  <?php foreach ($errors as $error): ?>
                      <li><?php echo htmlspecialchars($error); ?></li>
                  <?php endforeach; ?>
              </ul>
          </div>
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
              <input type="tel" id="tel" name="telephone" required />
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
