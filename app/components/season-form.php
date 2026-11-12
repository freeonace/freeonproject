<?php // variabel: $seasons, $seasonId ?>
<form method="get" class="pb-3">
	<div class="d-flex align-items-center gap-3">
		<div class="form-field">
			<select class="form-control" id="season" name="season" aria-label="Season">
				<?php foreach ($seasons as $s): ?>
					<option value="<?= (int)$s['id'] ?>"<?= (int)$s['id'] === $seasonId ? ' selected' : '' ?>><?= e($s['name']) ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<button type="submit" class="btn-solid theme-primary">Show</button>
	</div>
</form>