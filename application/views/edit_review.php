<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier la critique</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= base_url('assets/style.css') ?>">
</head>
<body>

<div class="edit-review-container">
    <h2>Modifier votre critique</h2>

    <?php if (!empty($critique->tvshow_id)): ?>

        <div style="text-align: center; margin-bottom: 30px;">
                <?php if (!empty($critique->jpeg)): ?>
                <img
                    style="display: block; margin: 0 auto;"
                    src="data:image/jpeg;base64,<?= base64_encode($critique->jpeg) ?>" />

                <?php else: ?>
                    <img
                         src="<?= base_url('assets/img/default.jpg') ?>"
                         alt="Image non disponible" />
                <?php endif; ?>
            </a>
        </div>
    <?php endif; ?>

    <form method="post">
        <label class="edit-label">Note :</label>
        <div class="rating">
            <?php for ($i = 5; $i >= 1; $i--): ?>
                <input type="radio" id="star<?= $i ?>" name="note" value="<?= $i ?>" <?= ($critique->note == $i ? 'checked' : '') ?>>
                <label for="star<?= $i ?>">★</label>
            <?php endfor; ?>
        </div>

        <label for="commentaire" class="edit-label">Commentaire :</label>
        <textarea name="commentaire" id="commentaire" class="edit-textarea" rows="6"><?= html_escape($critique->commentaire ?? '') ?></textarea>

        <button type="submit" class="edit-btn">Enregistrer</button>
    </form>
</div>

</body>
</html>
