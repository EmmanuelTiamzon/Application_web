<section class="saison-detail-wrapper">
	<div class="saison-header">
		<img class="poster-saison-large" src="data:image/jpeg;base64,<?= base64_encode($season->jpeg ?? $tvshow->jpeg) ?>" alt="Poster Saison <?= $season->seasonNumber ?>">

		<div class="saison-infos">
			<h2><?= html_escape($tvshow->name) ?> – <span class="saison-num-title"><?= ($season->seasonNumber == 2147483647) ? 'Épisodes spéciaux' : 'Saison ' . html_escape($season->seasonNumber) ?>
		</span></h2>

		<?php if (!empty($season->overview)): ?>
			<p class="saison-synopsis"><?= html_escape($season->overview) ?></p>
		<?php endif; ?>
		<div style="margin-top: 15px;">
			<a href="#avis-section" class="btn-avis">Voir les avis</a>
		</div>
		<p class="saison-meta"><strong><?= count($episodes) ?></strong> épisode<?= count($episodes) > 1 ? 's' : '' ?></p>
	</div>
</div>
</section>

<hr class="section-divider" />

<h3>Liste des épisodes</h3>
<section class="episodes-cartes-horizontales">
	<?php foreach ($episodes as $ep): ?>
		<details class="carte-episode-horizontale">
			<summary class="episode-summary">
				<strong class="episode-num">Épisode <?= $ep->episodeNumber ?></strong> – 
				<span class="episode-title"><?= html_escape($ep->name) ?></span>
			</summary>
			<div class="carte-episode-contenu">
				<div class="episode-overview">
					<?= !empty($ep->overview) ? html_escape($ep->overview) : '<em>Pas de synopsis disponible.</em>' ?>
				</div>
			</div>
		</details>
	<?php endforeach; ?>
</section>

<hr class="section-divider" />
<section id="avis-section">
	<?php if ($session->userdata('logged_in')): ?>
		<section>
			<h3>Laisser un avis</h3>
			<form action="<?= site_url('tvshow/add_critique') ?>" method="post">
				<input type="hidden" name="tvshow_id" value="<?= $tvshow->id ?>" />
				<label for="avis_type">Type d'avis :</label>
				<select name="season_id" id="avis_type" >
					<option value="">Série entière</option>
					<option value="<?= $season->seasonNumber ?>" selected>
						<?= $season->seasonNumber == 2147483647 ? 'Épisodes spéciaux' : 'Saison ' . html_escape($season->seasonNumber) ?>
					</option>
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

	<hr class="section-divider" />

	<section class="critiques-saison">
		<h3>Avis de <?= $season->seasonNumber == 2147483647 ? 'la saison spéciale' : 'la saison ' . html_escape($season->seasonNumber) ?></h3>
		<?php if (!empty($critiques_saison)): ?>
			<ul class="season-review-list">
				<?php foreach ($critiques_saison as $critique): ?>
					<li>
						<?= str_repeat('⭐', $critique->note) ?>
						par <strong><?= html_escape($critique->auteur) ?></strong> :
						<?= html_escape($critique->commentaire) ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php else: ?>
			<p><em>Aucun avis pour cette saison.</em></p>
		<?php endif; ?>
	</section>
