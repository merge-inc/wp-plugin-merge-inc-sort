<?php
/**
 * @var string $adminEmail
 */
/**
 * @var string $siteUrl
 */
/**
 * @var string $message
 */
?>
<div id="ms-subscribe-notice-container"
     class="notice woocommerce-message woocommerce-admin-promo-messages is-dismissible ms-hidden">
    <p>📊 | <strong>Sort</strong>
        <span><?=$message?></span>
        <input type="hidden" value="<?=$siteUrl?>" id="msSiteUrl" name="msSiteUrl">
        <input type="text" value="<?=$adminEmail?>" size="<?=strlen($adminEmail)?>" id="msAdminEmail"
               name="msAdminEmail"/><input
                type="button" value="Submit" class="button button-primary"></p>
</div>
