<?php
header('Content-Type: application/json');

$host = 'lcjczod468.mysql.db';
$db   = 'lcjczod468';
$user = 'lcjczod468';
$pass = 'Ndmbschl2018';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ];
$pdo = new PDO($dsn, $user, $pass, $options);

// Lire l'ID client envoyé via GET
$clientId = $_GET['client_id'] ?? null;
if (!$clientId) {
    echo json_encode(['types' => [], 'categories' => []]);
    exit;
}

// Récupérer types et catégories depuis tarifs_clients
$stmt = $pdo->prepare("SELECT DISTINCT type_colis, categorie_colis FROM tarifs_clients WHERE client_id = ?");
$stmt->execute([$clientId]);

$types = [];
$categories = [];
foreach ($stmt->fetchAll() as $row) {
    $types[] = $row['type_colis'];
    $categories[] = $row['categorie_colis'];
}

// Supprimer doublons
$types = array_unique($types);
$categories = array_unique($categories);

echo json_encode([
    'types' => $types,
    'categories' => $categories
]);