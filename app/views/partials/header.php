<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Takalo-takalo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .object-card { border: 1px solid #ddd; border-radius: 8px; overflow: hidden; }
        .object-card img { height: 180px; object-fit: cover; width: 100%; }
        .price-estimated { font-weight: bold; color: #2c7be5; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <div class="container">
        <a class="navbar-brand" href="/">Takalo-takalo</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="/objects">Tous les objets</a>
            <a class="nav-link" href="/objects/my">Mes objets</a>
            <!-- plus tard : lien profil, déconnexion... -->
        </div>
    </div>
</nav>

<main class="container">