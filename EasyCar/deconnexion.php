<?php
// Démarrage ou récupération de la session existante pour pouvoir la manipuler
session_start();

// Libère toutes les variables de session actuellement enregistrées en mémoire
session_unset();

// Détruit complètement toutes les données associées à la session en cours
session_destroy();

// Redirige immédiatement l'utilisateur vers la page d'accueil du site
header('Location: accueil.php');

// Interrompt définitivement l'exécution du script pour valider la redirection sécurisée
exit;
?>