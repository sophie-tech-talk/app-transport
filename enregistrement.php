<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Si on n’a pas les infos, on renvoie au formulaire
if (!isset($_SESSION['chauffeur_id'], $_SESSION['client'])) {
    header('Location: formulaire_multi.php');
    exit;
}

// Connexion à la BDD
$host = 'lcjczod468.mysql.db';
$db = 'lcjczod468';
$user = 'lcjczod468';
$pass = 'Ndmbschl2018';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    echo 'Erreur : ' . $e->getMessage();
    exit;
}

// Récupération des données de session
$chauffeur_id = $_SESSION['chauffeur_id'];
$date_livraison = $_SESSION['date_livraison'];
$clients = $_SESSION['client'];
$types = $_SESSION['type_colis'];
$categories = $_SESSION['categorie_colis'];
$nbs = $_SESSION['nb_colis'];

// Nom du chauffeur pour Google Sheets
$stmt = $pdo->prepare("SELECT nom FROM chauffeurs WHERE id = ?");
$stmt->execute([$chauffeur_id]);
$nom_chauffeur = $stmt->fetchColumn();

// Enregistrement de chaque ligne
$stmtInsert = $pdo->prepare("INSERT INTO livraisons (date, date_livraison, chauffeur_id, client, type_colis, categorie_colis, nb_colis, prix_unitaire, montant_total)
VALUES (CURDATE(), ?, ?, ?, ?, ?, ?, ?, ?)");

foreach ($clients as $i => $client) {
    $type = $types[$i];
    $categorie = $categories[$i];
    $nb = (int) $nbs[$i];
    $tarif = ($type === 'Carton') ? 1.80 : 2.10;
    $total = $nb * $tarif;

    $stmtInsert->execute([
        $date_livraison,
        $chauffeur_id,
        $client,
        $type,
        $categorie,
        $nb,
        $tarif,
        $total
    ]);

    // (optionnel) envoi vers Google Sheet
    $data = [
        'date' => date('Y-m-d'),
        'date_livraison' => $date_livraison,
        'nom' => $nom_chauffeur,
        'client' => $client,
        'type_colis' => $type,
        'categorie_colis' => $categorie,
        'nb_colis' => $nb,
        'prix_unitaire' => $tarif,
        'montant_total' => $total
    ];

    $webhookUrl = 'https://script.google.com/macros/s/AKfycbyrRESvhpFBYPcW5zxbx_quNBHxoRVma3jEJcISHjACf9-o9sw6pGzRpKfb_1-Zt4giAA/exec';
    $options = [
        'http' => [
            'method' => 'POST',
            'header' => 'Content-type: application/json',
            'content' => json_encode($data)
        ]
    ];
    $context = stream_context_create($options);
    @file_get_contents($webhookUrl, false, $context);
}

// ✅ Indiquer que l’enregistrement a eu lieu
$_SESSION['livraison_ok'] = true;

// 🔁 Nettoyage des variables de session sauf le flag
unset($_SESSION['chauffeur_id'], $_SESSION['client'], $_SESSION['type_colis'], $_SESSION['categorie_colis'], $_SESSION['nb_colis'], $_SESSION['date_livraison']);

// Redirection vers la page de succès
header('Location: success.php');
exit;
