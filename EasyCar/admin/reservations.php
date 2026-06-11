<?php
// Démarrage de la session pour la gestion des données administrateur
session_start();
// Inclusion du fichier de configuration pour l'accès à la base de données via l'instance PDO
require_once '../config.php';
// Initialisation de la variable de message de notification
$msg = "";

// 1. Action : Mise à jour du statut d'une réservation (Formulaire POST)
if (isset($_POST['action_reservation'])) {
    // Préparation de la requête de mise à jour sécurisée du statut
    $stmt = $pdo->prepare("UPDATE Reservation SET statut_reservation = :statut WHERE id_reservation = :id");
    $stmt->execute(['statut' => $_POST['statut'], 'id' => $_POST['id_reservation']]);
    $msg = "Réservation #" . $_POST['id_reservation'] . " mise à jour.";
}

// 2. Action : Suppression d'une réservation (Paramètre GET)
if (isset($_GET['supprimer'])) {
    // Requête de suppression définitive basée sur l'ID fourni
    $stmt = $pdo->prepare("DELETE FROM Reservation WHERE id_reservation = :id");
    $stmt->execute(['id' => $_GET['supprimer']]);
    // Redirection pour éviter la répétition de l'action au rafraîchissement et passer un indicateur de succès
    header("Location: reservations.php?deleted=1");
    exit();
}
// Récupération de l'indicateur de suppression pour alimenter le message de succès
if (isset($_GET['deleted'])) $msg = "Réservation supprimée avec succès.";

// 3. Gestion des filtres et de la recherche (Paramètres GET)
$filtre_statut = $_GET['statut'] ?? '';
$filtre_search = $_GET['search'] ?? '';
// Initialisation de la clause WHERE (1=1 facilite l'ajout dynamique de conditions avec 'AND')
$where = "WHERE 1=1";
$params = [];
// Filtre par état / statut de réservation (ex: Confirmée, En attente, Annulée)
if ($filtre_statut !== '') {
    $where .= " AND r.statut_reservation = :statut";
    $params['statut'] = $filtre_statut;
}
// Filtre par recherche textuelle (Recherche multi-critères : client ou véhicule)
if ($filtre_search !== '') {
    $where .= " AND (u.nom LIKE :s OR u.prenom LIKE :s2 OR v.marque LIKE :s3 OR v.modele LIKE :s4)";
    $params['s']  = "%$filtre_search%";
    $params['s2'] = "%$filtre_search%";
    $params['s3'] = "%$filtre_search%";
    $params['s4'] = "%$filtre_search%";
}
// Construction et exécution de la requête principale avec les jointures (INNER JOIN)
$sql = "SELECT r.*, u.nom, u.prenom, u.email, v.marque, v.modele, v.tarif_journalier, v.image
        FROM Reservation r
        INNER JOIN Utilisateur u ON r.id_utilisateur = u.id_utilisateur
        INNER JOIN Voiture v ON r.id_voiture = v.id_voiture
        $where
        ORDER BY r.id_reservation DESC";// Classement par nouveauté

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 4. Récupération des compteurs globaux pour l'affichage des onglets statistiques
$all    = $pdo->query("SELECT COUNT(*) FROM Reservation")->fetchColumn();
$conf   = $pdo->query("SELECT COUNT(*) FROM Reservation WHERE statut_reservation = 'Confirmée'")->fetchColumn();
$annul  = $pdo->query("SELECT COUNT(*) FROM Reservation WHERE statut_reservation = 'Annulée'")->fetchColumn();
$attent = $pdo->query("SELECT COUNT(*) FROM Reservation WHERE statut_reservation = 'En attente'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyCar – Réservations</title>
    <link rel="stylesheet" href="../Style CSS/style_reservations.css">
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
        <a href="voitures.php">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="22" height="13" rx="2"/><path d="M16 17v2M8 17v2M2 12h20"/></svg>
            Voitures
        </a>
        <a href="reservations.php" class="active">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
            Réservations
        </a>
    </nav>
    <div class="sidebar-bottom">
        <a href="admin.php" class="btn-add-car" style="text-decoration:none;">← Dashboard</a>
        <a href="deconnexion.php" class="logout">Se déconnecter</a>
    </div>
</aside>

<!-- ══ MAIN ══ -->
<main class="main">

    <div class="res-topbar">
        <div>
            <h1 class="page-title" style="margin-bottom:4px;">Gestion des Réservations</h1>
            <p class="res-subtitle"><?= count($reservations) ?> résultat<?= count($reservations) > 1 ? 's' : '' ?> affiché<?= count($reservations) > 1 ? 's' : '' ?></p>
        </div>
    </div>

    <?php if (!empty($msg)): ?>
        <div class="alert-success">✓ <?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <!-- STAT TABS -->
    <div class="res-tabs">
        <a href="reservations.php" class="res-tab <?= $filtre_statut === '' ? 'active' : '' ?>">
            Toutes <span class="tab-count"><?= $all ?></span>
        </a>
        <a href="reservations.php?statut=Confirmée" class="res-tab confirmee <?= $filtre_statut === 'Confirmée' ? 'active' : '' ?>">
            Confirmées <span class="tab-count green"><?= $conf ?></span>
        </a>
        <a href="reservations.php?statut=En attente" class="res-tab attente <?= $filtre_statut === 'En attente' ? 'active' : '' ?>">
            En attente <span class="tab-count orange"><?= $attent ?></span>
        </a>
        <a href="reservations.php?statut=Annulée" class="res-tab annulee <?= $filtre_statut === 'Annulée' ? 'active' : '' ?>">
            Annulées <span class="tab-count red"><?= $annul ?></span>
        </a>
    </div>

    <!-- SEARCH + FILTER -->
    <div class="res-toolbar">
        <form method="GET" class="search-form">
            <?php if ($filtre_statut): ?>
                <input type="hidden" name="statut" value="<?= htmlspecialchars($filtre_statut) ?>">
            <?php endif; ?>
            <div class="search-input-wrap">
                <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" name="search" placeholder="Rechercher client, véhicule…" value="<?= htmlspecialchars($filtre_search) ?>">
            </div>
            <button type="submit" class="btn-search">Rechercher</button>
            <?php if ($filtre_search || $filtre_statut): ?>
                <a href="reservations.php" class="btn-reset">✕ Réinitialiser</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- TABLE -->
    <div class="panel" style="padding: 0; overflow: hidden;">
        <table class="admin-table res-table">
            <thead>
                <tr>
                    <th style="padding-left:20px;">ID</th>
                    <th>Client</th>
                    <th>Véhicule</th>
                    <th>Date début</th>
                    <th>Date fin</th>
                    <th>Durée</th>
                    <th>Montant</th>
                    <th>Statut</th>
                    <th style="padding-right:20px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reservations)): ?>
                <tr>
                    <td colspan="9" style="text-align:center; padding:48px; color:#a0a5b0;">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#d0d5e0" stroke-width="1.5" style="display:block;margin:0 auto 12px"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
                        Aucune réservation trouvée.
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($reservations as $res):
                    $debut   = new DateTime($res['date_debut']);
                    $fin     = new DateTime($res['date_fin']);
                    // Calcul de la différence temporelle exprimée en jours
                    $duree   = $debut->diff($fin)->days;
                    // Chiffre d'affaires estimé (Durée de location * Tarif journalier)
                    $montant = $duree * $res['tarif_journalier'];
                    // Création d'une chaîne nettoyée pour la classe CSS du badge (ex: "en-attente")
                    $slug    = strtolower(str_replace(' ', '-', $res['statut_reservation']));
                ?>
                <tr>
                    <td style="padding-left:20px; color:#a0a5b0; font-size:12px; font-weight:600;">#<?= $res['id_reservation'] ?></td>
                    <td>
                        <div class="client-cell">
                            <div class="client-avatar"><?= strtoupper(mb_substr($res['prenom'],0,1) . mb_substr($res['nom'],0,1)) ?></div>
                            <div>
                                <div class="client-name"><?= htmlspecialchars($res['prenom'] . ' ' . $res['nom']) ?></div>
                                <div class="client-email"><?= htmlspecialchars($res['email'] ?? '') ?></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="car-cell">
                            <img src="../assets/<?= htmlspecialchars($res['image']) ?>" alt="">
                            <div>
                                <div class="car-name"><?= htmlspecialchars($res['marque'] . ' ' . $res['modele']) ?></div>
                                <div class="car-sub"><?= number_format($res['tarif_journalier'], 0) ?> DH/j</div>
                            </div>
                        </div>
                    </td>
                    <td class="date-cell">
                        <?= $debut->format('d M Y') ?>
                    </td>
                    <td class="date-cell">
                        <?= $fin->format('d M Y') ?>
                    </td>
                    <td class="duree-cell"><?= $duree ?> j</td>
                    <td class="tarif-cell"><?= number_format($montant, 0) ?> DH</td>
                    <td>
                        <span class="status-badge status-<?= $slug ?>">
                            <?= htmlspecialchars($res['statut_reservation']) ?>
                        </span>
                    </td>
                    <td style="padding-right:20px;">
                        <div class="actions-cell">
                            <!-- Inline status form -->
                            <form method="POST" style="display:inline-flex; gap:5px; align-items:center;">
                                <input type="hidden" name="id_reservation" value="<?= $res['id_reservation'] ?>">
                                <select name="statut" class="status-select">
                                    <option value="Confirmée"  <?= $res['statut_reservation'] == 'Confirmée'  ? 'selected' : '' ?>>Confirmée</option>
                                    <option value="En attente" <?= $res['statut_reservation'] == 'En attente' ? 'selected' : '' ?>>En attente</option>
                                    <option value="Annulée"    <?= $res['statut_reservation'] == 'Annulée'    ? 'selected' : '' ?>>Annulée</option>
                                </select>
                                <button type="submit" name="action_reservation" class="btn-modifier">OK</button>
                            </form>
                            <a href="reservations.php?supprimer=<?= $res['id_reservation'] ?>"
                               onclick="return confirm('Supprimer la réservation #<?= $res['id_reservation'] ?> ?')"
                               class="btn-supprimer">Supprimer</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- FOOTER TOTAL -->
        <?php if (!empty($reservations)):
        // Utilisation d'une fonction anonyme interne pour calculer la somme cumulée des revenus affichés
            $total_montant = array_sum(array_map(function($r) {
                $d = (new DateTime($r['date_debut']))->diff(new DateTime($r['date_fin']))->days;
                return $d * $r['tarif_journalier'];
            }, $reservations));
        ?>
        <div class="res-table-footer">
            <span><?= count($reservations) ?> réservation<?= count($reservations) > 1 ? 's' : '' ?></span>
            <span>Total estimé : <strong><?= number_format($total_montant, 0, ',', ' ') ?> DH</strong></span>
        </div>
        <?php endif; ?>
    </div>

    <footer>
        © <?= date('Y') ?> EasyCar. Tous droits réservés.
    </footer>

</main>
</body>
</html>