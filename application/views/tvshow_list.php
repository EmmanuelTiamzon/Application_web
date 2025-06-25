<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<title>Liste des séries</title>
	<link rel="stylesheet" href="<?= base_url('assets/style.css') ?>">
</head>

<body>
	<br>
	<br>
	<section class="list">
    	<?php foreach ($tvshows as $show): ?>
        	<a href="<?= site_url("tvshow/detail/{$show->id}") ?>" class="tvshow-link">
            	<article>
                	<header class="short-text">
                    	<?= html_escape($show->name) ?>
                	</header>

                	<img src="data:image/jpeg;base64,<?= base64_encode($show->jpeg) ?>"
                	alt="<?= html_escape($show->name) ?>" />

					<footer class="short-text">
						<?= $show->seasons_count ?> saison<?= ($show->seasons_count > 1 ? 's' : '') ?><br>

						<?php if ($show->average_rating !== null): ?>
							<?php
								$fullStars = floor($show->average_rating);
								$hasHalf = ($show->average_rating - $fullStars) >= 0.5;
								$emptyStars = 5 - $fullStars - ($hasHalf ? 1 : 0);
								for ($i = 0; $i < $fullStars; $i++) {
									echo '<span style="color: gold;">★</span>';
								}

								if ($hasHalf) {
									echo '<span style="color: lightgray;">★</span>';
								}

								for ($i = 0; $i < $emptyStars; $i++) {
									echo '<span style="color: lightgray;">★</span>';
								}
							?>
						<?php else: ?>
							Aucune note
						<?php endif; ?>
					</footer>

            	</article>
        	</a>
    	<?php endforeach; ?>
	</section>
</body>
</html>



