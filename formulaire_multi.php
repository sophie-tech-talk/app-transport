<?php
session_start();
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
    echo 'Erreur : ' . $e->getMessage();
    exit;
}

$chauffeurs = $pdo->query("SELECT * FROM chauffeurs ORDER BY nom")->fetchAll();
$clients = $pdo->query("SELECT * FROM clients ORDER BY nom")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Livraison</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            padding: 2em;
            background-color: #fff;
        }

        h2 {
            text-align: center;
            margin-bottom: 1em;
        }

        form {
            width: 100%;
            max-width: 600px;
            margin: auto;
        }

        label {
            display: block;
            margin-top: 1em;
            margin-bottom: 0.4em;
            font-weight: 600;
        }

        input,
        select,
        button,
        textarea {
            display: block;
            width: 100%;
            min-width: 0;
            max-width: 100%;
            padding: 0.75em;
            font-size: 1em;
            border: none;
            border-radius: 6px;
            box-sizing: border-box;
            color: #000;
        }

        input[type="date"] {
            width: 100%;
            max-width: 100%;
            -webkit-appearance: none;
            appearance: none;
            padding: 0.75em;
            font-size: 1em;
            border: none;
            border-radius: 6px;
        }

        button[type="submit"] {
            background-color: #00561b;
        }

        button[type="button"] {
            background-color: darkolivegreen;
        }

        button[type="button"]:hover {
            background-color: darkolivegreen;
        }

        .livraison-block {
            background-color: #f2f2f2;
            border: none;
            padding: 1em;
            border-radius: 6px;
            margin-top: 1.5em;
        }

        button {
            margin-top: 1.2em;
            background-color: #000000;
            color: white;
            border: none;
            border-radius: 50px;
            cursor: pointer;
        }

        button:hover {
            background-color: #00561b;
        }
    </style>
</head>

<body>

    <form method="post" action="recap.php">
        <h2>LIVRAISON</h2>

        <label>Nom du chauffeur</label>
        <select name="chauffeur_id" required id="chauffeur_id">
            <option value="">Sélectionner</option>
            <?php foreach ($chauffeurs as $c): ?>
                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nom']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Date de livraison</label>
        <input type="date" name="date_livraison" id="date_livraison" required>

        <div id="livraisons"></div>

        <button type="button" onclick="ajouterBloc()">+ Ajouter une livraison</button>
        <button type="submit">Continuer</button>
    </form>

    <script>
        let index = 0;

        function ajouterBloc() {
            const bloc = document.createElement('div');
            bloc.className = 'livraison-block';

            bloc.innerHTML = `
                <label>Client</label>
                <select name="client[]" onchange="chargerOptions(this, ${index})" required>
                    <option value="">Sélectionner</option>
                    <?php foreach ($clients as $cl): ?>
                        <option value="<?= $cl['id'] ?>"><?= htmlspecialchars($cl['nom']) ?></option>
                    <?php endforeach; ?>
                </select>

                <label>Type de colis</label>
                <select name="type_colis[]" id="type_colis_${index}" required>
                    <option value="">Choisissez d'abord un client</option>
                </select>

                <label>Catégorie</label>
                <select name="categorie_colis[]" id="categorie_colis_${index}" required>
                    <option value="">Choisissez d'abord un client</option>
                </select>

                <label>Nombre de colis</label>
                <input type="number" name="nb_colis[]" min="1" max="150" required inputmode="numeric" pattern="[0-9]*" placeholder="Renseignez le nombre de colis livrés">
            `;

            document.getElementById('livraisons').appendChild(bloc);
            index++;
        }

        function chargerOptions(selectClient, idx) {
            const clientId = selectClient.value;
            if (!clientId) return;

            fetch('get_options.php?client_id=' + clientId)
                .then(response => response.json())
                .then(data => {
                    const typeSelect = document.getElementById('type_colis_' + idx);
                    const catSelect = document.getElementById('categorie_colis_' + idx);

                    typeSelect.innerHTML = '<option value="">Sélectionner</option>';
                    data.types.forEach(t => {
                        const opt = document.createElement('option');
                        opt.value = t;
                        opt.textContent = t;
                        typeSelect.appendChild(opt);
                    });

                    catSelect.innerHTML = '<option value="">Sélectionner</option>';
                    data.categories.forEach(c => {
                        const opt = document.createElement('option');
                        opt.value = c;
                        opt.textContent = c;
                        catSelect.appendChild(opt);
                    });
                });
        }

        ajouterBloc(); // Un bloc par défaut au chargement
    </script>

</body>

</html>