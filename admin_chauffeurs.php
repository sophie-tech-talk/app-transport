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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nom'])) {
    $nom = trim($_POST['nom']);
    if ($nom !== '') {
        $stmt = $pdo->prepare("INSERT INTO chauffeurs (nom) VALUES (?)");
        $stmt->execute([$nom]);
    }
}

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $pdo->prepare("DELETE FROM chauffeurs WHERE id = ?")->execute([$id]);
}

$chauffeurs = $pdo->query("SELECT * FROM chauffeurs ORDER BY nom")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Gestion des chauffeurs</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            padding: 1em;
            background-color: #f2f2f2;
        }

        h1,
        h2 {
            text-align: center;
            color: #333;
        }

        form {
            background: white;
            padding: 1em;
            border-radius: 8px;
            margin: 1em auto;
            max-width: 500px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        label {
            font-weight: 600;
            display: block;
            margin-bottom: 0.4em;
        }

        input[type="text"] {
            width: 100%;
            padding: 0.75em;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1em;
            box-sizing: border-box;
            margin-bottom: 1em;
        }

        button {
            width: 100%;
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
            margin: 2em 0;
            background: white;
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

        .container {
            max-width: 700px;
            margin: auto;
        }

        a.back {
            display: block;
            text-align: center;
            margin-top: 2em;
            color: #333;
            text-decoration: none;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>Gestion des chauffeurs</h1>

        <form method="post">
            <label for="nom">Ajouter un chauffeur :</label>
            <input type="text" name="nom" id="nom" required placeholder="Nom du chauffeur">
            <button type="submit">Ajouter</button>
        </form>

        <h2>Liste des chauffeurs</h2>
        <table>
            <tr>
                <th>Nom</th>
                <th>Action</th>
            </tr>
            <?php foreach ($chauffeurs as $c): ?>
                <tr>
                    <td><?= htmlspecialchars($c['nom']) ?></td>
                    <td><a class="delete" href="?delete=<?= $c['id'] ?>"
                            onclick="return confirm('Supprimer ce chauffeur ?')">Supprimer</a></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <a class="back" href="admin.php">← Retour au menu admin</a>
    </div>

</body>

</html>