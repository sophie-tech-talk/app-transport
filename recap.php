<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: formulaire_multi.php');
    exit;
}

$_SESSION['chauffeur_id'] = $_POST['chauffeur_id'];
$_SESSION['date_livraison'] = $_POST['date_livraison'];
$_SESSION['client'] = $_POST['client'];
$_SESSION['type_colis'] = $_POST['type_colis'];
$_SESSION['categorie_colis'] = $_POST['categorie_colis'];
$_SESSION['nb_colis'] = $_POST['nb_colis'];

$recap = [];
foreach ($_SESSION['client'] as $i => $client) {
    $recap[$client][] = [
        'type' => $_SESSION['type_colis'][$i],
        'categorie' => $_SESSION['categorie_colis'][$i],
        'nb' => $_SESSION['nb_colis'][$i]
    ];
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Récapitulatif</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            padding: 1em;
            background-color: #f2f2f2;
        }

        h2 {
            text-align: center;
            margin-bottom: 1em;
        }

        ul {
            list-style-type: none;
            padding-left: 0;
        }

        li {
            background: #fff;
            margin-bottom: 0.5em;
            padding: 1em;
            border-radius: 6px;
            border: 1px solid #ddd;
        }

        li ul {
            padding-top: 0.5em;
        }

        li ul li {
            background: #f9f9f9;
            border: none;
            padding: 0.5em;
            font-size: 0.95em;
        }

        button {
            display: block;
            width: 100%;
            padding: 1em;
            font-size: 1em;
            margin-top: 1em;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        button:first-of-type {
            background-color: #4CAF50;
            color: white;
        }

        button:last-of-type {
            background-color: #e0e0e0;
            color: #333;
        }
    </style>
</head>

<body>

    <h2>Récapitulatif des livraisons</h2>

    <ul>
        <?php foreach ($recap as $client => $details): ?>
            <li>
                <strong><?= htmlspecialchars($client) ?></strong>
                <ul>
                    <?php foreach ($details as $item): ?>
                        <li><?= htmlspecialchars($item['nb']) ?>
                            [<?= htmlspecialchars($item['type']) ?>/<?= htmlspecialchars($item['categorie']) ?>]</li>
                    <?php endforeach; ?>
                </ul>
            </li>
        <?php endforeach; ?>
    </ul>

    <form action="enregistrement.php" method="post">
        <button type="submit">Confirmer</button>
    </form>
    <form action="formulaire_multi.php" method="post">
        <button type="submit">Modifier</button>
    </form>

</body>

</html>