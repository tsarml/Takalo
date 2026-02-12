<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Takalo-Takalo <?= isset($pageTitle) ? '— ' . htmlspecialchars($pageTitle) : '' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>

<header class="site-header">
    <div class="header-inner">
        <a href="/" class="logo">
            <span class="logo-icon">⇄</span>
            <span class="logo-text">Takalo<span class="logo-accent">.</span>Takalo</span>
        </a>
        <nav class="header-nav">
            <a href="/objects" class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/objects') !== false) ? 'active' : '' ?>">
                Objets
            </a>
            <a href="/exchanges" class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/exchanges') !== false) ? 'active' : '' ?>">
                Mes échanges
            </a>
        </nav>
        <div class="header-user">
            <?php if (isset($_SESSION['user'])): ?>
                <span class="user-name"><?= htmlspecialchars($_SESSION['user']['name']) ?></span>
                <a href="/logout" class="btn-ghost">Déconnexion</a>
            <?php else: ?>
                <a href="/login" class="btn-ghost">Connexion</a>
                <a href="/register" class="btn-primary">S'inscrire</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<main class="site-main">