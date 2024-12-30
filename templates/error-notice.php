<?php
/**
 * @var Exception $e
 */
?>
<div class="notice notice-error is-dismissible">
	<p><strong>Sort | Error</p>
	<table style="border-top: 1px solid lightgrey; width: 100%">
		<tr>
			<td>Message</td>
			<td><code style="background: transparent"><?php echo $e->getMessage(); ?></code></td>
		</tr>
		<tr>
			<td>File</td>
			<td><code style="background: transparent"><?php echo $e->getFile(); ?></code></td>
		</tr>
		<tr>
			<td>Line</td>
			<td><code style="background: transparent"><?php echo $e->getLine(); ?></code></td>
		</tr>
	</table>
</div>