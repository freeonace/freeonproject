<?php
// app/data/users.php: sesi, akun, dan penjaga halaman admin
// Tabel: sql/user.sql

/* ---------------------------------------------------------------
 * Sesi
 * ------------------------------------------------------------- */

// Tanpa $create, sesi hanya dibuka bila browser sudah membawa cookie sesi,
// jadi pengunjung yang belum login tidak diberi cookie apa pun.
function auth_start(bool $create = false): void {
	if (session_status() === PHP_SESSION_ACTIVE) return;

	session_name('csf_sid');
	if (!$create && !isset($_COOKIE['csf_sid'])) return;

	session_set_cookie_params([
		'lifetime' => 0,
		'path'     => BASE_URL ?: '/',
		'httponly' => true,
		'samesite' => 'Lax',
		'secure'   => !empty($_SERVER['HTTPS']),
	]);
	session_start();
}

function auth_login(int $id): void {
	auth_start(true);
	session_regenerate_id(true);   // cegah session fixation
	$_SESSION['uid'] = $id;
	current_user(true);            // muat ulang akun yang sudah tersimpan di cache request ini
}

function user_signout(): void {
	auth_start();
	if (session_status() !== PHP_SESSION_ACTIVE) return;

	$_SESSION = [];
	$p = session_get_cookie_params();
	setcookie(session_name(), '', [
		'expires'  => time() - 42000,
		'path'     => $p['path'],
		'domain'   => $p['domain'],
		'secure'   => $p['secure'],
		'httponly' => $p['httponly'],
		'samesite' => $p['samesite'] ?? 'Lax',
	]);
	session_destroy();
}

/* ---------------------------------------------------------------
 * CSRF: wajib di setiap form POST
 * ------------------------------------------------------------- */

function csrf_token(): string {
	auth_start(true);
	return $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
}

function csrf_field(): string {
	return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_check(): bool {
	auth_start();
	$sent = $_POST['csrf'] ?? null;
	return is_string($sent) && isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $sent);
}

// Kembali ke halaman asal (field hidden "back"), hanya ke alamat di situs ini.
function redirect_back(): void {
	$to = (string)($_POST['back'] ?? '');
	$ok = $to !== ''
		&& $to[0] === '/'
		&& !str_starts_with($to, '//')
		&& !str_contains($to, '\\')
		&& !preg_match('/[\x00-\x1f]/', $to)
		&& str_starts_with($to, BASE_URL . '/');
	header('Location: ' . ($ok ? $to : url()), true, 303);
	exit;
}

/* ---------------------------------------------------------------
 * Akun
 * ------------------------------------------------------------- */

// Akun yang sedang login (dibaca dari database di setiap request, jadi perubahan
// role langsung berlaku), atau null.
function current_user(bool $refresh = false): ?array {
	static $user = false;
	if ($refresh) $user = false;
	if ($user !== false) return $user;

	auth_start();
	$id = (int)($_SESSION['uid'] ?? 0);
	if ($id <= 0) return $user = null;

	$st = db()->prepare(
		'SELECT userID AS id, userName AS name, userEmail AS email, userRole AS role,
		        userIcon AS icon, userImage AS image
		 FROM `users` WHERE userID = :id'
	);
	$st->execute(['id' => $id]);
	$user = $st->fetch() ?: null;
	if ($user === null) unset($_SESSION['uid']);   // akunnya sudah dihapus
	return $user;
}

function is_admin(): bool {
	return (current_user()['role'] ?? '') === 'admin';
}

// "HOME" user: admin -> index admin page, beside -> homepage.
function home_url(): string {
	return is_admin() ? url('admin') : url();
}

// Menu yang berlaku untuk pengguna ini. Pengunjung biasa mendapat menu apa adanya; admin mendapat
// perubahan dari $menu['admin_menu'] (replace: ganti tampilan section, hide: sembunyikan dari sidebar,
// groups: ganti sub-menu satu group). Dipanggil sebelum resolve() agar halaman admin ikut dikenali router.
function menu_for_user(array $menu): array {
	if (!is_admin() || empty($menu['admin_menu'])) return $menu;

	$o = $menu['admin_menu'];
	foreach ($o['replace'] ?? [] as $key => $sec) {
		if (isset($menu['sections'][$key])) $menu['sections'][$key] = array_merge($menu['sections'][$key], $sec);
	}
	foreach ($o['hide'] ?? [] as $key) {
		if (isset($menu['sections'][$key])) $menu['sections'][$key]['hidden'] = true;
	}
	foreach ($o['add'] ?? [] as $key => $sec) {
		$menu['sections'][$key] = $sec;
		$menu['groups'][$sec['group']] ??= [];   // group wajib ada, boleh kosong
	}
	foreach ($o['groups'] ?? [] as $group => $subs) {
		$menu['groups'][$group] = $subs;
	}
	return $menu;
}

// Panggil di awal halaman admin:   if (!admin_gate()) return;
// Belum login -> ke halaman Sign In. Sudah login tapi bukan admin -> 403.
function admin_gate(): bool {
	$u = current_user();
	if ($u === null) {
		header('Location: ' . url('account', 'signin'), true, 302);
		exit;
	}
	if ($u['role'] !== 'admin') {
		http_response_code(403);
		echo '<section class="text-center fg-secondary user-select-none"><h1>403</h1><p>Admin only.</p></section>';
		return false;
	}
	return true;
}

function user_signin(string $email, string $pass): bool {
	$st = db()->prepare('SELECT userID, userPass FROM `users` WHERE userEmail = :e');
	$st->execute(['e' => strtolower(trim($email))]);
	$row = $st->fetch();
	if (!$row || !password_verify($pass, $row['userPass'])) return false;

	auth_login((int)$row['userID']);
	return true;
}

// null = berhasil (langsung login), selain itu pesan kesalahan.
function user_signup(string $name, string $email, string $pass): ?string {
	$name  = trim($name);
	$email = strtolower(trim($email));

	if ($name === '' || mb_strlen($name) > 50)                          return 'Name is required (max 50 characters).';
	if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) return 'Email is not valid.';
	if (strlen($pass) < 8)                                              return 'Password must be at least 8 characters.';
	if (strlen($pass) > 72)                                             return 'Password is too long (max 72 characters).';

	try {
		$st = db()->prepare('INSERT INTO `users` (userName, userEmail, userPass) VALUES (:n, :e, :p)');
		$st->execute(['n' => $name, 'e' => $email, 'p' => password_hash($pass, PASSWORD_DEFAULT)]);
	} catch (PDOException $ex) {
		if ((int)($ex->errorInfo[1] ?? 0) === 1062) return 'That email is already registered.';
		throw $ex;
	}

	auth_login((int)db()->lastInsertId());
	return null;
}

function user_initials(string $name): string {
	$parts = preg_split('/\s+/u', trim($name), -1, PREG_SPLIT_NO_EMPTY);
	if (!$parts) return '?';

	$ini = count($parts) > 1
		? mb_substr($parts[0], 0, 1) . mb_substr(end($parts), 0, 1)
		: mb_substr($parts[0], 0, 2);
	return mb_strtoupper($ini);
}

function user_image_url(array $u): string {
	return BASE_URL . '/img/avatar/' . rawurlencode((string)$u['image']);
}
