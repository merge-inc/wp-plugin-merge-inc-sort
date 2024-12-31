<?php
/**
 * @var string $id
 */
/**
 * @var int $value
 */

/**
 * @var bool $freemiumActivated
 */
?>
<input type="text" id="<?=$id;?>" name="<?=$id?>" value="<?=$value?>" class="regular-text"
	<?=$freemiumActivated ? "" : "disabled"?> style="width: 100%; max-width: 25rem;"/>
