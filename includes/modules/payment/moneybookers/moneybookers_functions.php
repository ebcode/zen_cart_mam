<?php
/**
 * Mediaflex Sp. z O.O.
 * @author krystian.kuczek@mediaflex.pl
 * @package Moneybookers Payment Plugin
 */

include_once((IS_ADMIN_FLAG === true ? DIR_FS_CATALOG_LANGUAGES : DIR_WS_LANGUAGES) . $_SESSION['language'] . '/modules/payment/moneybookers.php');

function mb_cfg_select_option($select_array, $key_value, $key = '') {
	return zen_cfg_select_option($select_array, $key_value, $key) . '<div id="mbNext0">';
}

function mb_cfg_pull_down_order_statuses($order_status_id, $key = '') {
	return zen_cfg_pull_down_order_statuses($order_status_id, $key) . '<div style="text-align: center"><h3 style="color: red; padding: 0; margin:0; margin-top: 20px">' . MODULE_PAYMENT_MONEYBOOKERS_TEXT_MONEYBOOKERS_DATA . '</h3></div>
		<div id="mbActivation" style="display: none">' . MODULE_PAYMENT_MONEYBOOKERS_ABOUT_ACTIVATION . '
			<div style="text-align: center; margin: auto;">'.zen_image_button('button_activate_account.gif', 'Activate your moneybookers merchant accuont', 'onclick="mbSendActivation();"') .'</div>
		
		</div>
		<div id="mbNext0">';
}

function mb_cfg_email($value, $key = '') {
	//TODO:
	// Tu trzeba wpierniczyć zapytanie do

	$string = '<script language="javascript" type="text/javascript">

		var mbUpdateElement;
		var mbRet;
		function mbDoHideUpdate() {

			els = document.getElementsByTagName("input");

			for (var i = 0 ; i < els.length ; i++) {
				if (els[i].type == \'image\' && els[i].alt=="Update") {
					mbUpdateElement = els[i];
				}
			}

			mbUpdateElement.style.display="none";
		}


		function mbDoEmailCheck(field) {
			mbDoAjaxRequest("./moneybookers_ajax.php?action=validate&field=email&data=" + field.value, mbDoneEmailCheck);

			try {
				document.getElementById("mbLoader1").style.display="inline";
			} catch (e) {}
		}

		function mbDoneEmailCheck(ret) {
			if (ret != "NOK") {
				ret = ret.substr(3);
				document.getElementById("mbIdForUser").value=ret;
				document.getElementById("mbIdForSystem").value=ret;
				document.getElementById("mbNext0").style.display="none";
				document.getElementById("mbNext1").style.display="none";
				document.getElementById("mbActivation").style.display="block";
			} else {
				alert("' . strtr(MODULE_PAYMENT_MONEYBOOKERS_TEXT_EMAIL_ERROR, '"', '\"') . '");
			}

			try {
				document.getElementById("mbLoader1").style.display="none";
			} catch (e) {}
			return ret;
		}
		
		function mbDoSecretWordCheck(field, field2) {
			mbDoAjaxRequest("./moneybookers_ajax.php?action=validate&field=secretWord&data=" + field.value + "&data2=" + field2.value, mbDoneSecretWordCheck);

			try {
				document.getElementById("mbLoader2").style.display="inline";
			} catch (e) {}
		}

		function mbDoneSecretWordCheck(ret) {
			if (ret == "OK") {
				document.getElementById("mbNext1").style.display = "none";
				document.getElementById("mbNext2").style.display = "block";
//				document.getElementById("mbActivation").style.display = "none";
				mbUpdateElement.style.display="inline";
			} else {
				alert("' . strtr(MODULE_PAYMENT_MONEYBOOKERS_TEXT_SECRET_WORD_ERROR, '"', '\"') . '");;
			}

			try {
				document.getElementById("mbLoader2").style.display="none";
			} catch (e) {}
			

			return ret;
		}

		function mbDoBackToEmail() {
			document.getElementById("mbNext1").style.display = "none";
			document.getElementById("mbNext0").style.display = "block";
			document.getElementById("mbActivation").style.display = "none";
		}


		function mbDoDisplayUpdate() {
			;
		}

		function mbSendActivation() {
			var data = document.getElementById("mbIdForSystem").value;
			var data2 = document.getElementById("mbEmail").value;

			url = "./moneybookers_ajax.php?action=email&data=" + data + "&data2=" + data2;

			mbDoAjaxRequest(url, mbActivationSent);

		}

		function mbActivationSent(ret) {
			alert("' . strtr(MODULE_PAYMENT_MONEYBOOKERS_TEXT_ACTIVATION_EMAIL_SENT, '"', '\"') . '");
			document.getElementById("mbActivation").style.display = "none";
			document.getElementById("mbNext1").style.display="block";
		}

		function mbDoAjaxRequest(url, fun) {
			var ret = null;

			if(navigator.appName == "Microsoft Internet Explorer") {
				http = new ActiveXObject("Microsoft.XMLHTTP");
			} else {
				http = new XMLHttpRequest();
			}

			http.open("GET", url, true);
			http.send(null);

			http.onreadystatechange=function() {
				if(http.readyState == 4) {
					fun(http.responseText);
				}
			}

			ret = mbRet;

			try {
				return ret;
			} catch (e) {
				return "NOK";
			}
		}

		setTimeout("mbDoHideUpdate();", 10);
	</script>';

	$string .= '<br /><input type="text" name="configuration[' . $key . ']" value="' . $value . '" id="mbEmail" />' . zen_image(DIR_WS_IMAGES . 'loader.gif', MODULE_PAYMENT_MONEYBOOKERS_TEXT_LOADING, 16, 16, 'id="mbLoader1" style="display: none"') .
			'<div style="clear: both"></div>'.zen_image_button('button_next.gif', 'Next step', 'onclick="mbDoEmailCheck(document.getElementById(\'mbEmail\')); return false;");" style="float: right;"') . '</div><div style="display: none" id="mbNext1">';

	return $string;
}

function mb_cfg_ident($value, $key = '') {
	$string = '<input type="text" name="mb_id_blank" disabled="disabled" id="mbIdForUser" /><input type="hidden" value="" id="mbIdForSystem" name="configuration[' . $key . ']" />';

	return $string;
}

function mb_cfg_scr_word($value, $key = '') {
	$string = '<br /><input name="configuration[' . $key . ']" type="text" value="' . $value . '" id="mbSecretWord" />' . zen_image(DIR_WS_IMAGES . 'loader.gif', MODULE_PAYMENT_MONEYBOOKERS_TEXT_LOADING, 16, 16, 'id="mbLoader2" style="display: none"') . '<br />' .
		zen_image_button('button_back.gif', 'Next step', 'onclick="mbDoBackToEmail(); return false;" style="float: left;"') . zen_image_button('button_next.gif', 'Next step', 'onclick="mbDoSecretWordCheck(document.getElementById(\'mbEmail\'), document.getElementById(\'mbSecretWord\')); return false;" style="float: right;"') .
		'</div><div id="mbNext2" style="display: none;">' . MODULE_PAYMENT_MONEYBOOKERS_TEXT_SUCCESS_CONF . '</div>';

	return $string;
}

function mb_cfg_info_email($value, $key = '') {
	$string .= '<input name="configuration[' . $key . ']" type="text" value="' . $value . '" id="infoEmail" /><br />';

	return $string;
}

?>
