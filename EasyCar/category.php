<?php
// Démarrage de la session pour suivre l'état de connexion de l'utilisateur
session_start();
// Inclusion du fichier de connexion à la base de données via PDO
require_once 'config.php';
// Définition de la requête de base : on ne récupère que les voitures disponibles
$sql = "SELECT * FROM voiture WHERE disponibilite = 'disponible'";
// Initialisation du tableau des paramètres pour la requête préparée
$params = [];
// Vérification et ajout dynamique du filtre "Type de voiture" (catégorie) si sélectionné
if (!empty($_GET['type'])) {
    $sql .= " AND categorie = :type";
    $params['type'] = $_GET['type'];
}
// Vérification et ajout dynamique du filtre "Carburant" si sélectionné
if (!empty($_GET['carburant'])) {
    $sql .= " AND type_carburant = :carburant";
    $params['carburant'] = $_GET['carburant'];
}
// Vérification et ajout dynamique du filtre "Boîte de vitesse" si sélectionné
if (!empty($_GET['boite_vitesse'])) {
    $sql .= " AND boite_vitesse = :boite_vitesse";
    $params['boite_vitesse'] = $_GET['boite_vitesse'];
}
// Préparation de la requête SQL sécurisée contre les injections
$stmt = $pdo->prepare($sql);
// Exécution de la requête avec les paramètres de filtrage accumulés
$stmt->execute($params);
// Récupération de tous les résultats correspondants
$voitures = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="Style CSS/style_cat.css">
</head>
<body>
    <header>
        <img src="assets/Logo.png" alt="EasyCar">
        <nav>
            <a href="accueil.php">Accueil</a>
            <a href="category.php">Nos voitures</a>
            <a href="connexion.php">Connexion</a>
            <?php 
            // Vérification si un utilisateur est actuellement connecté
                if(isset($_SESSION['user'])){
                    $user = $_SESSION['user'];
                    // Affichage du nom du client connecté redirigeant vers son profil
                    echo "<a href='profil.php' class='reserve'>" .$user['name'] . "</a>";
                }else{
                  // Bouton affiché par défaut pour les visiteurs anonymes
                    echo "<a href='category.php' class='reserve'>Reserver  Maintenant</a>";
                }
            ?>       
        </nav>
    </header>
    <main>
        <div>
            <h1>Nos Voitures</h1>
            <p>Découvrez notre voitures disponibles à la location</p>
        </div>

        <form action="category.php" method="get" class="filter">
            <label for="type">Type de voiture</label>
            <select name="type" id="type">
                <option value="">-- Selectionner --</option>
                <option value="Citadines">Citadines</option>
                <option value="Berlines">Berlines</option>
                <option value="SUV">SUV</option>
                <option value="LUX">LUX</option>
            </select>
            <label for="carburant">Carburant</label>
            <select name="carburant" id="carburant">
                <option value="">-- Selectionner --</option>
                <option value="Essence">Essence</option>
                <option value="Diesel">Diesel</option>
            </select>
            <label for="boite_vitesse">Boite de vitesse</label>
            <select name="boite_vitesse" id="boite_vitesse">
                <option value="">-- Selectionner --</option>
                <option value="Automatique">Automatique</option>
                <option value="Manuelle">Manuelle</option>
            </select>
            <button>Rechercher</button>
        </form>

        <div class="cards">
          <?php 
          // Boucle itérative pour afficher chaque voiture filtrée sous forme de carte individuelle
          foreach($voitures as $voiture){?>
            <div class="card">
                <img src="assets/<?php echo ($voiture['image']); ?>" alt="<?php echo ($voiture['modele']); ?>">
                <h3><?php echo ($voiture['marque']); ?> <?php echo ($voiture['modele']); ?></h3>
                <p><?php echo ($voiture['tarif_journalier']); ?>DH/Jour</p>
                <div class="card-badges">
                    <span class="card-badge">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 22V8l9-6 9 6v14"/><path d="M10 14v8"/><path d="M3 12h18"/></svg>
                        <?php echo ($voiture['type_carburant']); ?>
                    </span>
                    <span class="card-badge">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                        <?php echo ($voiture['boite_vitesse']); ?>
                    </span>
                    <span class="card-badge">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                        <?php echo ($voiture['annee']); ?>
                    </span>
                </div>
                <a href="reserver.php?id=<?= $voiture['id_voiture']; ?>">Détails & Réserver</a>
            </div>
            <?php }?>
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