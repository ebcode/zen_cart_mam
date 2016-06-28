<?php
/**
 * Mediaflex Sp. z O.O.
 * @author krystian.kuczek@mediaflex.pl
 * @package Moneybookers Payment Plugin
 */

class moneybookers_curl extends base {

	var $_request = null;

	var $_timeout = 10;

	var $_crl = null;

	var $_redirectBlockType				= 'moneybookers/admin';
	var $_moneybookersServer			= 'https://www.moneybookers.com';
	var $_checkEmailUrl					= '/app/email_check.pl';
	var $_checkEmailCustId				= '6999315';
	var $_checkEmailPassword			= 'a4ce5a98a8950c04a3d34a2e2cb8c89f';
	var $_checkSecretUrl				= '/app/secret_word_check.pl';
	var $_activationEmailTo				= 'ecommerce@moneybookers.com';
	var $_activationEmailSubject		= 'Zen Cart Moneybookers Activation';
	var $_moneybookersMasterCustId		= '7283403';
	var $_moneybookersMasterSecretHash	= 'c18524b6b1082653039078a4700367f0';
	var $_moneybookersPayment			= '/app/payment.pl';

	function ValidateEmail($email) {
		$this->_request = $this->_checkEmailUrl . "?email=$email&cust_id=" . $this->_checkEmailCustId . "&password=" . $this->_checkEmailPassword;
		return $this->doRequest();
	}

	function ValidateSecretWord($email, $word) {
		$this->_request = $this->_checkSecretUrl . "?email=$email&secret=" . md5(md5($word) . $this->_moneybookersMasterSecretHash) . "&cust_id=" . $this->_moneybookersMasterCustId;
		return $this->doRequest();
	}

	function SessionStart($data) {
		$this->_request = $this->_moneybookersPayment;
		$data['prepare_only'] = 1;
		$ret = $this->doRequest('post', $data);

		return $ret;
	}

	/**
	 * Funkcja wywołująca zapytanie na serwie moneybookers'a
	 *
	 *
	 * @param string $method
	 * @return <type>
	 */
	function doRequest($method = 'get', $data = null) {
		if (!$this->_request) {
			return null;
		}

		$request = $this->_moneybookersServer . $this->_request;

		$curlOptions = array();
//		$curlHeaders = array();

//		$curlHeaders[] = 'Connection: Keep-Alive';
//		$curlHeaders[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';

//		$curlOptions[CURLOPT_HTTPHEADER] = $curlHeaders;
		$curlOptions[CURLOPT_USERAGENT] = 'ZenCart Moneybookers';
		$curlOptions[CURLOPT_HEADER] = false;
		$curlOptions[CURLOPT_RETURNTRANSFER] = true;
		$curlOptions[CURLOPT_FOLLOWLOCATION] = true;
		$curlOptions[CURLOPT_TIMEOUT] = $this->_timeout;

		switch ($method) {
//			case 'put':
//			case 'delete':
//				break;
			case 'post':
				$curlOptions[CURLOPT_POST] = true;
				$curlOptions[CURLOPT_POSTFIELDS] = $data;
				break;
			case 'get':
				if (is_array($data) && sizeof($data)) {
					$request .= '?';

					foreach ($data as $k => $v) {
						$request .= $k . '=' . $v . '&';
					}

					$request = substr($request, 0, strlen($request) - 1);
				}
				break;
			default:
				trigger_error("Can't execute '$method' method.", E_ERROR);
		}

		$curlOptions[CURLOPT_URL] = $request;

		$this->_crl = curl_init();

		curl_setopt_array($this->_crl, $curlOptions);

		$response = curl_exec($this->_crl);

		return $response;
	}

	function sendShopActivationMail($email, $id) {
		global $db;

		$text = "Zen Cart\n
			Customer ID: $id\n
			Email: $email\n
			URL to Shop: " . 'http://' . $_SERVER['SERVER_NAME'] . array_shift(explode("admin/moneybookers_ajax.php", $_SERVER['REQUEST_URI'])) . "\n
			Language: " . $_SESSION['language'];

		zen_mail("Moneybookers", $this->_activationEmailTo, $this->_activationEmailSubject, $text, STORE_NAME, $email, array(), 'xml_record');
	}
}
?>
