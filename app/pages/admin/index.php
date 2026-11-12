<?php
if (!admin_gate()) return;
page_title('Admin');
?>
<div class="md:p-7 lg:p-9 p-4">
	<div class="d-flex flex-column">
		<div class="pb-8">
			<h2 class="text-title">Admin</h2>
			<span class="fg-inverse">Signed in as <?= e(current_user()['name']) ?></span>
		</div>

		<!-- konten admin di sini -->
	</div>
</div>
