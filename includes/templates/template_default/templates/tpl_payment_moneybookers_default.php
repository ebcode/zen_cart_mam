<?php
/**
 * Mediaflex Sp. z O.O.
 * @author krystian.kuczek@mediaflex.pl
 * @package Moneybookers Payment Plugin
 */
?>

<div class="centerColumn">
	<div style="text-align: center; margin: auto;">
		<?= zen_image(DIR_WS_IMAGES . "moneybookers.gif", "partners"); ?>
	</div>

	<iframe src="https://www.moneybookers.com/app/payment.pl?<?= $get ?>" style="border:0; width:410px; height: 1000px;">
	</iframe>
</div>