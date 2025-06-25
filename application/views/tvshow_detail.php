<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8" />
	<title>Détail de la série</title>
	<link rel="stylesheet" href="<?= base_url('assets/style.css') ?>">
</head>
<body>
	<div class="fiche-wrapper">
			<div class="fiche-image">
			<a href="<?= html_escape($tvshow->homepage) ?>">
				<img
				class="serie-img"
				src="data:image/jpeg;base64,<?= base64_encode($tvshow->jpeg) ?>"
				alt="<?= html_escape($tvshow->name) ?>"
				/>
			</a>
		</div>

		<div class="fiche-infos">
			<div class="titre-et-coeur">
				<h2><?= html_escape($tvshow->name) ?></h2>
				<?php if ($session->userdata('logged_in')): ?>
					<form method="post" action="<?= site_url($est_favori ? 'favoris/supprimer/' . $tvshow->id : 'favoris/ajouter/' . $tvshow->id) ?>" class="favoris-form">
						<button type="submit" class="coeur-btn <?= $est_favori ? '' : 'vide' ?>">
							<?= $est_favori ? '♥' : '♡' ?>
						</button>
					</form>
				<?php endif; ?>
			</div>
			<div style="margin-top: 15px;">
				<a href="#avis-section" class="btn-avis">Voir les avis</a>
			</div>
			<?php if (isset($moyenne)): ?>
				<div class="note-absolue">
					<strong>Note moyenne :</strong> 
					<span class="stars">
						<?php
						$note_arrondie = round($moyenne);
						for ($i = 1; $i <= 5; $i++) {
							echo $i <= $note_arrondie ? '⭐' : '<span class="empty-star">☆</span>';
						}
						?>
					</span>
					(<?= round($moyenne, 1) ?>/5 – <?= $nb_votes ?> avis)
				</div>
			<?php else: ?>
				<p class="no-review-message"><em>Aucune critique pour le moment.</em></p>
			<?php endif; ?>

			<div class="genres-absolus">
				<strong>Genres :</strong> 
				<?= implode(', ', array_map(function($g) {
					return html_escape($g->name);
				}, $genres)) ?>
			</div>

			<div class="synopsis-absolu">
				<strong>Synopsis :</strong> <em><?= html_escape($tvshow->overview) ?></em>
			</div>
		</div>
	</div>

	<?php if (empty($episodes)): ?>
		<p>Aucun épisode trouvé pour cette série.</p>
	<?php else: ?>
		<?php
			$bySeason = [];
			$specials = [];

			foreach ($episodes as $ep) {
				if ($ep->season_number == 2147483647) {
					$specials[] = $ep;
				} else {
					$bySeason[$ep->season_number][] = $ep;
				}
			}

			ksort($bySeason);
		?>
		<hr class="section-divider" />
		<h3>Saisons disponibles</h3>
		<section class="saison-cartes-ligne">
			<?php foreach ($bySeason as $sNumber => $eps): ?>
				<?php $seasonData = $this->Model_tvshow->getSeasonByNumber($tvshow->id, $sNumber); ?>
				<a href="<?= site_url('tvshow/saison/' . $tvshow->id . '/' . $sNumber) ?>" class="carte-saison-ligne">
					<img class="poster-saison" src="data:image/jpeg;base64,<?= base64_encode($seasonData->jpeg ?? $tvshow->jpeg) ?>" alt="Poster Saison <?= $sNumber ?>">
					<div class="infos-saison">
						<div class="saison-num">Saison <?= $sNumber ?></div>
						<div class="saison-episodes"><strong><?= count($eps) ?></strong> épisode<?= count($eps) > 1 ? 's' : '' ?></div>
					</div>
				</a>
			<?php endforeach; ?>
		</section>

		<?php if (!empty($specials)): ?>
			<a href="<?= site_url('tvshow/saison/' . $tvshow->id . '/2147483647') ?>" class="carte-saison-ligne">
				<img class="poster-saison" src="data:image/jpeg;base64,<?= base64_encode($tvshow->jpeg) ?>" alt="Poster Épisodes spéciaux">
				<div class="infos-saison">
					<div class="saison-num">Épisodes spéciaux</div>
				</div>
			</a>
		<?php endif; ?>
	<?php endif; ?>

	<hr class="section-divider" />

	<?php if (isset($suggestions) && !empty($suggestions)): ?>
	<div>
		<h3>Séries similaires</h3>
		<section class="list">
			<?php foreach ($suggestions as $suggestion): ?>
				<a href="<?= site_url('tvshow/detail/' . $suggestion->id) ?>" class="tvshow-link" style="text-decoration:none; color:inherit;">
					<article>
						<header class="short-text"><?= html_escape($suggestion->name) ?></header>
						<img
							src="data:image/jpeg;base64,<?= base64_encode($suggestion->jpeg) ?>"
							alt="<?= html_escape($suggestion->name) ?>"
						/>
						<footer class="short-text">
							<?php if (isset($suggestion->seasons_count)): ?>
								<?= $suggestion->seasons_count ?> saison<?= ($suggestion->seasons_count > 1 ? 's' : '') ?>
							<?php else: ?>
								0 saison
							<?php endif; ?>
						</footer>
					</article>
				</a>
			<?php endforeach; ?>
		</section>
	</div>
	<?php endif; ?>

	<hr class="section-divider" />

	<?php if ($this->session->userdata('logged_in')): ?>
	<section>
		<h3>Laisser un avis</h3>
		<form action="<?= site_url('tvshow/add_critique') ?>" method="post">
			<input type="hidden" name="tvshow_id" value="<?= $tvshow->id ?>" />

			<label for="season_id">Saison (optionnelle) :</label>
			<select name="season_id" id="season_id">
				<option value="">Série entière</option>
				<?php
				$seen = [];
				foreach ($episodes as $episode):
					if (!isset($seen[$episode->season_number])): ?>
						<option value="<?= $episode->season_number ?>">
							<?= ($episode->season_number == 2147483647) ? 'Épisodes spéciaux' : 'Saison ' . $episode->season_number ?>
						</option>
					<?php
					$seen[$episode->season_number] = true;
					endif;
				endforeach;
				?>
			</select>

			<label for="note">Note :</label>
			<div class="rating">
				<?php for ($i = 5; $i >= 1; $i--): ?>
					<input type="radio" id="star<?= $i ?>" name="note" value="<?= $i ?>" required>
					<label for="star<?= $i ?>">★</label>
				<?php endfor; ?>
			</div>

			<label for="commentaire">Commentaire :</label>
			<textarea name="commentaire" id="commentaire" required></textarea>

			<button type="submit">Envoyer</button>
		</form>
	</section>
	<?php else: ?>
		<p><a href="<?= site_url('login') ?>">Connectez-vous</a> pour laisser un avis.</p>
	<?php endif; ?>
	<br /><section id="avis-section">
	<hr class="section-divider" />
	<section class="critiques-saison">
		<h3>Avis</h3>
		<br />
		<?php if (!empty($critiques)): ?>
			<?php
			$saisons = [];
			$season_id_to_number = [];

			// Récupération des numéros de saison à partir de leur ID
			foreach ($episodes as $ep) {
				$seasonData = $this->Model_tvshow->getSeasonByNumber($tvshow->id, $ep->season_number);
				if ($seasonData) {
					$season_id_to_number[$seasonData->id] = $seasonData->seasonNumber;
				}
			}

			// Groupement des critiques par ID de saison
			foreach ($critiques as $critique) {
				$key = $critique->season_id ?? 0;
				$saisons[$key][] = $critique;
			}

			// Tri : série entière (0), puis saisons triées par numéro croissant
			uksort($saisons, function($a, $b) use ($season_id_to_number) {
				if ($a == 0) return -1;
				if ($b == 0) return 1;
				$nA = $season_id_to_number[$a] ?? PHP_INT_MAX;
				$nB = $season_id_to_number[$b] ?? PHP_INT_MAX;
				return $nA <=> $nB;
			});
			?>

			<?php foreach ($saisons as $season_id => $liste): ?>
				<h4 class="titre-saison">
					<?php
					if ($season_id == 0) {
						echo "Série entière";
					} else {
						$sn = $season_id_to_number[$season_id] ?? null;
						echo ($sn == 2147483647) ? "Épisodes spéciaux" : "Saison $sn";
					}
					?>
				</h4>

				<ul class="season-review-list">
					<?php foreach ($liste as $critique): ?>
						<li>
							<?= str_repeat('⭐', $critique->note) ?>
							par <strong><?= html_escape($critique->auteur) ?></strong> :
							<?= html_escape($critique->commentaire) ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endforeach; ?>
		<?php else: ?>
			<p><em>Aucune critique pour le moment.</em></p>
		<?php endif; ?>
	</section>
</section>

</body>
</html>
