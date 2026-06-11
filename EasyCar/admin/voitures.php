<?php
// Démarrage de la session pour maintenir la connexion de l'administrateur
session_start();
// Inclusion du fichier de configuration pour la connexion PDO (situé dans le dossier parent)
require_once '../config.php';
// 1. Processus de suppression d'une voiture
if (isset($_GET['supprimer'])) {
    $id_voit = $_GET['supprimer'];
    // Préparation de la requête de suppression sécurisée
    $sql = "DELETE FROM Voiture WHERE id_voiture = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id_voit]);
    $msg = "Voiture supprimée avec succès !";
// Redirection simple vers la même page pour nettoyer les paramètres de l'URL    header("Location: admin.php");
    exit();
}

// 2. Recherche par mot-clé ou affichage de la liste complète
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    // Nettoyage et ajout des jokers % pour une recherche partielle (Contient)
    $search = '%' . trim($_GET['search']) . '%';
    // Requête filtrée par modèle ou par marque, triée par ID du plus récent au plus ancien
    $stmt = $pdo->prepare("SELECT * FROM Voiture WHERE modele LIKE :search OR marque LIKE :search ORDER BY id_voiture DESC");
    $stmt->execute(['search' => $search]);
} else {
    // Requête par défaut sans filtre si aucune recherche n'est lancée
    $stmt = $pdo->query("SELECT * FROM Voiture ORDER BY id_voiture DESC");
}
// Récupération de tous les enregistrements sous forme de tableau associatif
$voitures = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>EasyCar - Nos Voitures</title>
    <link rel="stylesheet" href="../Style CSS/style_voitures.css">
</head>
<body>
    <!-- ══ SIDEBAR ══ -->
<aside class="sidebar">
    <div class="sidebar-logo">
        <span>EasyCar Admin</span>
        <small>Espace administrateur</small>
    </div>
    <nav>
        <a href="../accueil.php">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
            Accueil
        </a>
        <a href="admin.php">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            Dashboard
        </a>
        <a href="voitures.php" class="active">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="22" height="13" rx="2"/><path d="M16 17v2M8 17v2M2 12h20"/></svg>
            Voitures
        </a>
        <a href="reservations.php">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
            Réservations
        </a>
    </nav>
    <div class="sidebar-bottom">
        <button class="btn-add-car" onclick="openModal()">+ Ajouter une voiture</button>
        <a href="deconnexion.php" class="logout">Se déconnecter</a>
    </div>
</aside>
    
    <main>
        <div class="search">
            <form action="" method="get">
                <input type="text" placeholder="Recherche..." name="search">
                    <button>Recherche</button>
            </form>
                    
            </div>

        <div class="cards">
          <?php 
          // Boucle de parcours de la liste des voitures récupérées en base de données
          foreach($voitures as $voiture){?>
            <div class="card">
    <img src="../assets/<?php echo ($voiture['image']); ?>" alt="<?php echo ($voiture['modele']); ?>">
    
    <div class="card-header-row">
        <h3><?php echo ($voiture['marque']); ?> <?php echo ($voiture['modele']); ?></h3>
        <div class="card-price-block">
            <span class="price"><?php echo number_format($voiture['tarif_journalier'], 2); ?> DH</span>
            <span class="per-day">/ jour</span>
        </div>
    </div>

    <div class="card-divider"></div>

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

    <a href="modifier.php?id=<?= $voiture['id_voiture']; ?>">Modifier</a>
    <a href="voitures.php?supprimer=<?= $voiture['id_voiture']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette voiture ?');" style="color: red;">Supprimer</a>
</div>
            <?php }?>
        </div>
    </main>

</body>
</html>