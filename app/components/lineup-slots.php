<?php
// variabel: $F (baris dari fixture_details() untuk satu tim, atau null)
// Satu <a class="pos-XX" style="--xxpos: NN%"> per slot yang terisi di tabel formation.
if (!$F) return;
foreach (formation_slots() as $code => $var):
	$pos = $F['form' . $code] ?? null;
	if ($pos === null) continue;
?>
<a class="pos-<?= $code ?>" style="--<?= $var ?>: <?= (int)$pos ?>%;" title="<?= $code ?>" aria-label="<?= $code ?>"><span><?= $code ?></span></a>
<?php endforeach; ?>
