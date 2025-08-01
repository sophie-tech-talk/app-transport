<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

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

$clientAjoute = false;
$nomClientAjoute = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nom_client'])) {
    $nom = trim($_POST['nom_client']);
    if ($nom !== '') {
        $stmt = $pdo->prepare("INSERT INTO clients (nom) VALUES (?)");
        $stmt->execute([$nom]);
        $clientAjoute = true;
        $nomClientAjoute = $nom;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['client_id'], $_POST['type_colis'], $_POST['categorie_colis'], $_POST['prix'])) {
    $stmt = $pdo->prepare("INSERT INTO tarifs_clients (client_id, type_colis, categorie_colis, prix) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $_POST['client_id'],
        $_POST['type_colis'],
        $_POST['categorie_colis'],
        $_POST['prix']
    ]);
}

if (isset($_GET['delete_tarif'])) {
    $id = (int) $_GET['delete_tarif'];
    $pdo->prepare("DELETE FROM tarifs_clients WHERE id = ?")->execute([$id]);
}

if (isset($_GET['delete_client'])) {
    $id = (int) $_GET['delete_client'];
    $pdo->prepare("DELETE FROM clients WHERE id = ?")->execute([$id]);
}

$clients = $pdo->query("SELECT * FROM clients ORDER BY nom")->fetchAll();
$tarifs = $pdo->query("
    SELECT tc.*, c.nom AS nom_client
    FROM tarifs_clients tc
    JOIN clients c ON c.id = tc.client_id
    ORDER BY c.nom, tc.type_colis
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Gestion des clients</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            padding: 1em;
            background-color: #f2f2f2;
        }

        .container {
            max-width: 800px;
            margin: auto;
        }

        .alert {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 1em;
            border-radius: 6px;
            margin-bottom: 1em;
        }

        .client-list-top {
            background: #fff;
            padding: 1em;
            border-radius: 6px;
            margin-bottom: 2em;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        h1,
        h2 {
            text-align: center;
            color: #333;
            margin-top: 1.5em;
        }

        form {
            background-color: #fff;
            padding: 1em;
            margin: 1.5em auto;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        label {
            display: block;
            margin: 0.8em 0 0.3em;
            font-weight: 600;
        }

        input,
        select {
            width: 100%;
            padding: 0.75em;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            margin-top: 1.2em;
            padding: 0.9em;
            font-size: 1em;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.5em 0;
            background-color: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        th,
        td {
            padding: 1em;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #fafafa;
            font-weight: 600;
        }

        a.delete {
            color: red;
            font-weight: bold;
            text-decoration: none;
        }

        ul {
            padding-left: 1.2em;
        }

        ul li {
            margin: 0.5em 0;
        }

        a.back {
            display: block;
            text-align: center;
            margin: 2em auto;
            color: #333;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="container">

        <?php if ($clientAjoute): ?>
            <div class="alert">Le client <strong><?= htmlspecialchars($nomClientAjoute) ?></strong> a été ajouté avec
                succès.</div>
        <?php endif; ?>

        <h1>Gestion des clients</h1>

        <h2>Liste des clients</h2>
        <ul>
            <?php foreach ($clients as $c): ?>
                <li>
                    <?= htmlspecialchars($c['nom']) ?>
                    <a class="delete" href="?delete_client=<?= $c['id'] ?>"
                        onclick="return confirm('Supprimer ce client et tous ses tarifs ?')">[Supprimer]</a>
                </li>
            <?php endforeach; ?>
        </ul>

        <form method="post">
            <label>Ajouter un client :</label>
            <input type="text" name="nom_client" required placeholder="Nom du client">
            <button type="submit">Ajouter</button>
        </form>

        <h2>Ajouter un tarif</h2>
        <form method="post">
            <label>Client</label>
            <select name="client_id" required>
                <option value="">-- Sélectionner --</option>
                <?php foreach ($clients as $c): ?>
                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nom']) ?></option>
                <?php endforeach; ?>
            </select>

            <label>Type de colis</label>
            <input type="text" name="type_colis" required placeholder="Ex : colis, carton, palette, sac...">

            <label>Catégorie</label>
            <input type="text" name="categorie_colis" required placeholder="Ex : standard, discount...">

            <label>Prix unitaire (€)</label>
            <input type="number" step="0.01" name="prix" required placeholder="Ex : 3.20">

            <button type="submit">Ajouter le tarif</button>
        </form>

        <h2>Tarifs configurés</h2>
        <table>
            <tr>
                <th>Client</th>
                <th>Type</th>
                <th>Catégorie</th>
                <th>Prix (€)</th>
                <th>Action</th>
            </tr>
            <?php foreach ($tarifs as $t): ?>
                <tr>
                    <td><?= htmlspecialchars($t['nom_client']) ?></td>
                    <td><?= htmlspecialchars($t['type_colis']) ?></td>
                    <td><?= htmlspecialchars($t['categorie_colis']) ?></td>
                    <td><?= number_format($t['prix'], 2) ?></td>
                    <td><a class="delete" href="?delete_tarif=<?= $t['id'] ?>"
                            onclick="return confirm('Supprimer ce tarif ?')">Supprimer</a></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <a class="back" href="admin.php">← Retour au menu admin</a>
    </div>
</body>

</html>