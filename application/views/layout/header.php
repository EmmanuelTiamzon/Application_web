<!doctype html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<title>Séries</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="<?= base_url('assets/style.css') ?>">

</head>
<body>
<main class="container">
<button onclick="scrollToTop()" id="btnScrollTop"><span class="fleche">↑</span></button>

<nav>
	<ul>
		<li><strong>Séries</strong></li>
		<li><a href="<?= site_url('tvshow') ?>"><i class="fas fa-home"></i></a></li>
		<?php if ($this->session->userdata('logged_in')): ?>
			<li><a href="<?= site_url('user/profile') ?>"><i class="fas fa-user"></i></a></li>
			<li><a href="<?= site_url('auth/logout') ?>" title="Déconnexion"><i class="fas fa-sign-out-alt" style="font-size: 22px;"></i></a></li>
		<?php else: ?>
			<li><a href="<?= site_url('login') ?>"><i class="fas fa-user"></i></a></li>
		<?php endif; ?>
	</ul>
	<ul>
		<li>
			<form method="GET" action="<?= site_url('tvshow') ?>" role="search">
				<select name="type" onchange="this.form.submit()" aria-label="Genre"><option value="">Genre</option>
					<?php foreach ($all_genres as $g): ?>
						<option value="<?= $g->name ?>" <?= ($this->input->get('type') == $g->name) ? 'selected' : '' ?>><?= $g->name ?></option>
					<?php endforeach; ?>
				</select>
				<select name="min_rating" onchange="this.form.submit()"  aria-label="Note minimale">
					<option value="">Note minimale</option>
					
					<?php for ($i = 1; $i <= 5; $i++): ?>
						<option value="<?= $i ?>" <?= ($this->input->get('min_rating') == $i) ? 'selected' : '' ?>><?= $i ?> étoile<?= $i > 1 ? 's' : '' ?> ou plus</option>
					<?php endfor; ?>
				</select>
				<input name="search" type="search" placeholder="Rechercher..." value="<?= html_escape($this->input->get('search')) ?>" />
				<input type="submit" value="Chercher" />
			</form>
		</li>
	</ul>
</nav>
				<script>
				document.addEventListener('DOMContentLoaded', function () {
					const btnScroll = document.getElementById('btnScrollTop');
					btnScroll.style.display = 'none';
					
					window.addEventListener('scroll', () => {
						if (window.scrollY > 300) {
							btnScroll.style.display = 'block';
						} else {
							btnScroll.style.display = 'none';
						}
					});
				});
				
				function scrollToTop() {
					window.scrollTo({
						top: 0,
						behavior: 'smooth'
					});
				}
				</script>
				
				