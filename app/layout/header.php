<?php // app/layout/header.php ?>
<header class="sticky-top border-bottom">
	<nav class="navbar bg-1">
		<!-- Navigation Bar Toggle -->
		<div class="sidebar-nav container-fluid">
			<a class="navbar-brand fs-lg" href="<?= e(home_url()) ?>"><?= e(site('name')) ?></a>
			<button type="button" class="btn btn-menu sidebar-toggle" aria-controls="sidebar" aria-expanded="true" aria-label="toggle sidebar">
				<i class="fa-solid fa-bars"></i>
			</button>
			<button type="button" class="btn-icon sidebar-open fg-primary" data-bs-toggle="drawer" data-bs-target="#sidebar" aria-controls="sidebar" aria-label="Buka menu">
				<i class="fa-solid fa-bars"></i>
			</button>
		</div>

		<!-- Main Navigation Bar -->
		<div class="main-nav container-fluid border-start">
			<div class="search">
				<label class="form-control form-adorn">
					<span class="form-adorn-text" aria-hidden="true">
						<i class="fa-solid fa-magnifying-glass"></i>
					</span>
					<input type="search" class="form-ghost" placeholder="Search...">
				</label>

				<div class="search-panel" id="search-panel" hidden>
					<p class="search-panel-title">Last search...</p>
					<ul class="search-panel-list">
						<li><a href="">Search A</a></li>
					</ul>
				</div>
			</div>
			<div class="d-flex md:gap-2">
				<button type="button" class="btn btn-menu" data-bs-toggle="menu" aria-expanded="false" aria-label="Themes">
					<i class="fa-solid fa-circle-half-stroke"></i>
				</button>
				<div class="menu">
					<a class="menu-item" data-theme-value="light">Light <i class="fa-regular fa-sun"></i></a>
					<a class="menu-item" data-theme-value="dark">Dark <i class="fa-solid fa-moon"></i></a>
					<a class="menu-item" data-theme-value="system">System <i class="fa-solid fa-desktop"></i></a>
				</div>

				<?php $u = current_user(); ?>
				<?php if ($u === null): ?>
					<!-- Belum login: hanya Sign In / Sign Up -->
				<button type="button" class="avatar pro" data-bs-toggle="menu" aria-expanded="false" aria-label="Account">
					<i class="fa-regular fa-circle-user"></i>
				</button>
				<div class="menu">
					<a class="menu-item" href="<?= e(url('account', 'signin')) ?>">Sign In</a>
					<a class="menu-item" href="<?= e(url('account', 'signup')) ?>">Sign Up</a>
				</div>
				<?php else: ?>
				<!-- Sudah login: avatar = gambar atau inisial, sesuai userIcon -->
				<button type="button" class="avatar pro" data-bs-toggle="menu" aria-expanded="false" aria-label="Account: <?= e($u['name']) ?>">
				<?php if ($u['icon'] === 'image' && $u['image']): ?>
					<span class="avatar"><img class="avatar-img" src="<?= e(user_image_url($u)) ?>" alt=""></span>
				<?php else: ?>
					<span class="avatar"><?= e(user_initials($u['name'])) ?></span>
				<?php endif; ?>
				</button>
				<div class="menu">
					<span class="menu-item"><?= e($u['name']) ?></span>
					<hr class="menu-divider">
				<?php if ($u['role'] === 'admin'): ?>
					<a class="menu-item" href="<?= e(url('admin')) ?>">Admin</a>
				<?php endif; ?>
					<div class="submenu">
						<button type="button" class="menu-item">Change Icon</button>
						<form class="menu" method="post" action="<?= e(url('account', 'icon')) ?>">
						<?= csrf_field() ?>
							<input type="hidden" name="back" value="<?= e($_SERVER['REQUEST_URI'] ?? '') ?>">
							<button type="submit" name="icon" value="text" class="menu-item<?= $u['icon'] === 'text' ? ' active' : '' ?>">Text</button>
							<button type="submit" name="icon" value="image" class="menu-item<?= $u['icon'] === 'image' ? ' active' : '' ?>"<?= $u['image'] ? '' : ' disabled' ?>>Image</button>
						</form>
					</div>
					<form method="post" action="<?= e(url('account', 'signout')) ?>">
						<?= csrf_field() ?>
						<button type="submit" class="menu-item">Sign Out</button>
					</form>
				</div>
				<?php endif; ?>
			</div>
		</div>
	</nav>
</header>