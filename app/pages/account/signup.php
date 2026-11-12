<?php
page_title('Sign Up');
if (current_user() !== null) {
	header('Location: ' . url(), true, 302);
	exit;
}

$error = '';
$name  = '';
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$name  = trim((string)($_POST['name'] ?? ''));
	$email = trim((string)($_POST['email'] ?? ''));
	$pass  = (string)($_POST['password'] ?? '');

	if (!csrf_check()) {
		$error = 'The form has expired. Reload the page and try again.';
	} elseif ($pass !== (string)($_POST['password2'] ?? '')) {
		$error = 'The two passwords do not match.';
	} else {
		$error = user_signup($name, $email, $pass) ?? '';
		if ($error === '') {
			header('Location: ' . url(), true, 303);
			exit;
		}
	}
}
?>
<div class="md:p-7 lg:p-9 p-4">
	<div class="d-flex flex-column gap-5" style="max-width: 24rem;">
		<h2 class="text-title">Sign Up</h2>

		<?php if ($error !== ''): ?>
			<p class="fg-danger" role="alert"><?= e($error) ?></p>
		<?php endif; ?>

		<form method="post" class="d-flex flex-column gap-3">
			<?= csrf_field() ?>
			<div class="form-field">
				<label for="name" class="form-label">Name</label>
				<input class="form-control" type="text" id="name" name="name" value="<?= e($name) ?>" maxlength="50" autocomplete="name" required>
			</div>
			<div class="form-field">
				<label for="email" class="form-label">Email</label>
				<input class="form-control" type="email" id="email" name="email" value="<?= e($email) ?>" maxlength="100" autocomplete="email" required>
			</div>
			<div class="form-field">
				<label for="password" class="form-label">Password</label>
				<input class="form-control" type="password" id="password" name="password" minlength="8" maxlength="72" autocomplete="new-password" required>
			</div>
			<div class="form-field">
				<label for="password2" class="form-label">Repeat password</label>
				<input class="form-control" type="password" id="password2" name="password2" minlength="8" maxlength="72" autocomplete="new-password" required>
			</div>
			<button type="submit" class="btn-outline theme-primary">Sign Up</button>
		</form>

		<span>Already have an account? <a href="<?= e(url('account', 'signin')) ?>">Sign In</a></span>
	</div>
</div>
