<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Inscription</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <link rel="stylesheet" href="<?= base_url('assets/style.css') ?>">
    <style>
        .inline-fields {
            display: flex;
            gap: 20px;
            margin-bottom: 1rem;
        }
        .inline-fields > div {
            flex: 1;
        }
    </style>
</head>
<body>
<main class="container">
    <nav>
        <ul>
            <li><a href="<?= site_url('tvshow') ?>"><i class="fas fa-home"></i></a></li>
        </ul>
    </nav>

    <h2>Inscription</h2>

    <?php if (!empty($error)): ?>
        <p class="error"><?= html_escape($error) ?></p>
    <?php endif; ?>

    <form method="post" action="<?= site_url('auth/register') ?>">
        <div class="inline-fields">
            <div>
                <label for="firstname">Prénom</label>
                <input type="text" name="firstname" id="firstname" placeholder="Prénom" required />
            </div>
            <div>
                <label for="lastname">Nom</label>
                <input type="text" name="lastname" id="lastname" placeholder="Nom" required />
            </div>
        </div>
        <label for="email">Adresse email</label>
        <input type="email" name="email" id="email" placeholder="Email" required />

        <label for="password">Mot de passe</label>
        <input type="password" name="password" id="password" placeholder="Mot de passe" required />

        <label for="password_confirm">Confirmation du mot de passe</label>
        <input type="password" name="password_confirm" id="password_confirm" placeholder="Confirmer" required />

        <button type="submit">S'inscrire</button>
    </form>

    <p>Déjà inscrit ? <a href="<?= site_url('auth/login') ?>">Connexion</a></p>
</main>
</body>
</html>
