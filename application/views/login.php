<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Connexion - Séries</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <link rel="stylesheet" href="<?= base_url('assets/style.css') ?>">
</head>
<body>
<main class="container">

    <nav>
        <ul>
            <li><a href="<?= site_url('tvshow') ?>"><i class="fas fa-home"></i></a></li>
        </ul>
    </nav>

    <h2>Connexion</h2>

    <?php
    $CI =& get_instance();
    if ($CI->session->flashdata('error')):
    ?>
        <p class="error"><?= html_escape($CI->session->flashdata('error')) ?></p>
    <?php endif; ?>

    <form method="post" action="<?= site_url('login') ?>">
        <label for="login">Email :</label>
        <input type="text" name="email" id="login" required />

        <label for="password">Mot de passe :</label>
        <input type="password" name="password" id="password" required />

        <button type="submit">Se connecter</button>
    </form>

    <p>Pas encore inscrit ? <a href="<?= site_url('auth/register') ?>">S'inscrire</a></p>
</main>
</body>
</html>
