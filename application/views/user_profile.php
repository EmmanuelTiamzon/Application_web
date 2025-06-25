<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Profil utilisateur</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="<?= site_url('assets/style.css') ?>">
</head>
<body>

  <?php if (isset($message)): ?>
    <p class="message"><?= html_escape($message) ?></p>
  <?php endif; ?>
  
  <?php if (!empty($prenom) && !empty($nom)): ?>
  <h3 class="bonjour-message">Bienvenue, <?= html_escape($prenom) ?></h3>
<?php endif; ?>

<hr>

<?php if (!empty($favoris)): ?>
  <div class="favoris-section">
    <h3>VOS SERIES PRÉFÉRÉES</h3>
    <div class="favoris-wrapper">
      <section class="list">
        <?php foreach ($favoris as $show): ?>
          <a href="<?= site_url('tvshow/detail/' . $show->id) ?>" class="tvshow-link">
            <article>
              <header class="short-text"><?= html_escape($show->name ?? 'Série inconnue') ?></header>
              <?php if (!empty($show->jpeg)): ?>
                <img src="data:image/jpeg;base64,<?= base64_encode($show->jpeg) ?>" alt="<?= html_escape($show->name) ?>" />
              <?php else: ?>
                <img src="<?= base_url('assets/img/default.jpg') ?>" alt="Image non disponible" />
              <?php endif; ?>
              <footer class="short-text">
                <?= (int)($show->season_count ?? 0) ?> saison<?= (($show->season_count ?? 0) > 1 ? 's' : '') ?>
              </footer>
            </article>
          </a>
        <?php endforeach; ?>
      </section>
    </div>
  </div>
<?php else: ?>
  <p>Tu n’as pas encore de séries préférées.</p>
<?php endif; ?>
<hr>

<section class="user-reviews">
  <h3>Vos Avis</h3>
  
  <?php if (!empty($critiques)): ?>
    <?php foreach ($critiques as $critique): ?>
      <div class="review-item" style="display: flex; gap: 20px; align-items: center; flex-wrap: wrap;">
        <div class="review-content" style="flex: 1;">
          <?php if (!empty($critique->commentaire)): ?>
            <p>"<?= nl2br(html_escape($critique->commentaire)) ?>"</p>
          <?php endif; ?>
          <div class="stars">
            <?php
            $note = (int) $critique->note;
            for ($i = 1; $i <= 5; $i++) {
              echo $i <= $note ? '<span>⭐</span>' : '<span class="empty-star">☆</span>';
            }
            ?>
          </div>
          <a href="<?= site_url('critiques/edit/' . $critique->id) ?>" class="btn-edit">Modifier</a>
        </div>
        
        <?php if (!empty($critique->tvshow_id)): ?>
          <p class="short-text" style="; margin-bottom: 200px; font-weight: bold;text-transform: uppercase; font-size: 1.3rem;">
            <?= html_escape($critique->tvshow_name ?? 'Série inconnue') ?>
          </p>
          <a href="<?= site_url('tvshow/detail/' . $critique->tvshow_id) ?>">
            <?php if (!empty($critique->jpeg)): ?>
              <img class="img-hover-grow" src="data:image/jpeg;base64,<?= base64_encode($critique->jpeg) ?>" alt="<?= html_escape($critique->tvshow_name ?? 'Série') ?>" />
            <?php else: ?>
              <img class="img-hover-grow" src="<?= base_url('assets/img/default.jpg') ?>" alt="Image non disponible" />
            <?php endif; ?>
          </a>
        </div>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
<?php else: ?>
  <p class="no-reviews">Tu n’as pas encore laissé de critique.</p>
<?php endif; ?>
</section>