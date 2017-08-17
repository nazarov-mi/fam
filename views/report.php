<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Печать</title>
	<style type="text/css">
		
		table {
			border-collapse: collapse;
		}

		th, td {
			padding: .25em .5em;
			border: 1px solid #000;
			text-align: left;
		}

		.no-wrap {
			white-space: nowrap;
		}

		@media print {
			.no-print {
				display: none !important;
			}
		}

	</style>
</head>
<body>
	<div class="no-print">
		<p>Для печати нажмите на кнопку</p>
		<p>
			<button onclick="window.print();">Печать</button>
		</p>
	</div>
	<table>

		<?php
			$head = View::get("head");
			$events = View::get("events");
		?>

		<tr>
			<?php foreach ($head as $val): ?>
				<th>
					<?php echo $val; ?>
				</th>
			<?php endforeach; ?>
		</tr>

		<?php if ($events && count($events) > 0): ?>
			<?php foreach ($events as $row): ?>
				<tr>
					<?php foreach ($row as $key => $val): ?>
						<td>
							<?php
								if ($key == "active_date" || $key == "end_date" ) {
									echo '<span class="no-wrap">' . $val . "</span>";
								} else {
									echo $val;
								}
							?>
						</td>
					<?php endforeach; ?>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>

	</table>
</body>
</html>