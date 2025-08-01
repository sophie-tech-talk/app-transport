<?php
session_start();

// Sécurité : empêcher l'accès direct sans passage par enregistrement
if (!isset($_SESSION['livraison_ok'])) {
    header('Location: formulaire_multi.php');
    exit;
}

// On vide la confirmation pour éviter les rafraîchissements abusifs
unset($_SESSION['livraison_ok']);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Succès</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            padding: 2em 1em;
            background-color: #f9f9f9;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
        }

        .check {
            font-size: 5em;
            color: #4CAF50;
            margin-bottom: 0.5em;
        }

        h2 {
            margin-top: 0;
            font-size: 1.5em;
            color: #333;
        }

        p {
            color: #666;
            font-size: 1em;
        }

        a button {
            margin-top: 2em;
            padding: 1em 2em;
            font-size: 1em;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            max-width: 300px;
        }

        a button:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>

    <div class="check">✔️</div>
    <h2>Livraisons enregistrées avec succès</h2>
    <p>Merci pour votre envoi.</p>
    <a href="formulaire_multi.php"><button>Retour à l'accueil</button></a>

</body>

</html>