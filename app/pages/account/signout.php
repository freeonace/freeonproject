<?php
// Hanya menerima POST + token CSRF (tombol Sign Out di header berupa form).
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_check()) {
	user_signout();
}
header('Location: ' . url(), true, 303);
exit;
