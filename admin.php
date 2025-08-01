<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <title>Admin - Gestion du transport</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      margin: 0;
      padding: 2em 1em;
      background-color: #f2f2f2;
      text-align: center;
    }

    h1 {
      margin-bottom: 2em;
      color: #333;
    }

    .btn {
      display: block;
      background-color: #007bff;
      color: white;
      text-decoration: none;
      padding: 1em;
      border-radius: 8px;
      margin: 1em auto;
      max-width: 320px;
      font-size: 1.1em;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .btn:hover {
      background-color: #0056b3;
    }
  </style>
</head>

<body>

  <h1>Interface administrateur</h1>

  <a class="btn" href="admin_chauffeurs.php">Gérer les chauffeurs</a>
  <a class="btn" href="admin_clients.php">Gérer les clients & tarifs</a>

</body>

</html>