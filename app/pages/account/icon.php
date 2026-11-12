<?php
// Mengganti tampilan avatar (text / image). Hanya untuk akun yang sudah login.
$u = current_user();
if ($u === null) {
	header('Location: ' . url('account', 'signin'), true, 302);
	exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_check()) {
	$icon = $_POST['icon'] ?? '';
	// "image" hanya boleh bila akun memang punya gambar
	if ($icon === 'text' || ($icon === 'image' && $u['image'])) {
		db()->prepare('UPDATE `user` SET userIcon = :i WHERE userID = :id')
		    ->execute(['i' => $icon, 'id' => $u['id']]);
	}
}
redirect_back();
