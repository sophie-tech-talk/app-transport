<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Paramètres de connexion à ta base OVH
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
    echo 'Erreur de connexion : ' . $e->getMessage();
    exit;
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date_livraison = $_POST['date_livraison'];
    $chauffeur_id = $_POST['chauffeur_id'];
    $client = $_POST['client'];
    $type = $_POST['type_colis'];
    $categorie = $_POST['categorie_colis'];
    $nb = (int) $_POST['nb_colis'];

    $tarif = ($type === 'Carton') ? 1.80 : 2.10;
    $total = $nb * $tarif;

    $stmt = $pdo->prepare("INSERT INTO livraisons (date, date_livraison, chauffeur_id, client, type_colis, categorie_colis, nb_colis, prix_unitaire, montant_total)
    VALUES (CURDATE(), ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$date_livraison, $chauffeur_id, $client, $type, $categorie, $nb, $tarif, $total]);

    echo "<p style='color:green'>Livraison enregistrée avec succès !</p>";

    // 🔁 Envoi vers Google Sheet
    $nom_chauffeur = $pdo->prepare("SELECT nom FROM chauffeurs WHERE id = ?");
    $nom_chauffeur->execute([$chauffeur_id]);
    $nom = $nom_chauffeur->fetchColumn();

    $data = [
        'date' => date('Y-m-d'),
        'date_livraison' => $date_livraison,
        'nom' => $nom,
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
            'content' => json_encode($data),
        ],
    ];
    $context = stream_context_create($options);

    $response = @file_get_contents($webhookUrl, false, $context);

    if ($response === false) {
        echo "<p style='color:red'>❌ Envoi vers Google Sheet échoué</p>";

        global $http_response_header;
        echo "<pre>";
        print_r($http_response_header);
        echo "</pre>";
    } else {
        echo "<p style='color:blue'>✅ Donnée envoyée à Google Sheet</p>";
    }
}

// Récupération des chauffeurs
$chauffeurs = $pdo->query("SELECT * FROM chauffeurs ORDER BY nom")->fetchAll();
// Récupération des clients
$clients = $pdo->query("SELECT * FROM clients ORDER BY nom")->fetchAll();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Saisie livraison</title>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial;
            margin: 2em;
        }

        form {
            max-width: 400px;
            margin: auto;
        }

        input,
        select,
        button {
            width: 100%;
            margin-bottom: 10px;
            padding: 8px;
        }
    </style>
</head>

<body>

    <h2>Formulaire de livraison</h2>

    <form method="post">
        <label>Nom du chauffeur</label>
        <select name="chauffeur_id" required>
            <option value="">-- Sélectionner --</option>
            <?php foreach ($chauffeurs as $c): ?>
                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nom']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Date de livraison</label>
        <input type="date" name="date_livraison" required>

        <label>Client</label>
        <select name="client" id="client" required>
            <option value="">-- Sélectionner --</option>
            <?php foreach ($clients as $cl): ?>
                <option value="<?= $cl['id'] ?>"><?= htmlspecialchars($cl['nom']) ?></option>
            <?php endforeach; ?>
        </select>


        <label>Type de colis</label>
        <select name="type_colis" id="type_colis" required>
            <option value="">-- Choisissez un client d'abord --</option>
        </select>

        <label>Catégorie</label>
        <select name="categorie_colis" id="categorie_colis" required>
            <option value="">-- Choisissez un client d'abord --</option>
        </select>

        <label>Nombre de colis</label>
        <input type="number" name="nb_colis" required min="1">

        <button type="submit">Enregistrer</button>
    </form>

    <script>
        document.getElementById('client').addEventListener('change', function () {
            const clientId = this.value;
            const typeSelect = document.getElementById('type_colis');
            const catSelect = document.getElementById('categorie_colis');

            // Vider les anciennes options
            typeSelect.innerHTML = '<option value="">Chargement...</option>';
            catSelect.innerHTML = '<option value="">Chargement...</option>';

            if (!clientId) return;

            fetch('get_options.php?client_id=' + clientId)
                .then(response => response.json())
                .then(data => {
                    typeSelect.innerHTML = '<option value="">-- Sélectionner --</option>';
                    catSelect.innerHTML = '<option value="">-- Sélectionner --</option>';

                    data.types.forEach(type => {
                        const opt = document.createElement('option');
                        opt.value = type;
                        opt.textContent = type;
                        typeSelect.appendChild(opt);
                    });

                    data.categories.forEach(cat => {
                        const opt = document.createElement('option');
                        opt.value = cat;
                        opt.textContent = cat;
                        catSelect.appendChild(opt);
                    });
                });
        });
    </script>

</body>

</html>