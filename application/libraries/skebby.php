<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Library for CodeIgniter to connect with Skebby API.
 * @author	Luigi Mozzillo <luigi@innato.it>
 * @link	http://innato.it
 * @forked_from	https://github.com/riccamastellone/codeigniter-skebby
 * @version	1.1
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * THIS SOFTWARE AND DOCUMENTATION IS PROVIDED "AS IS," AND COPYRIGHT
 * HOLDERS MAKE NO REPRESENTATIONS OR WARRANTIES, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO, WARRANTIES OF MERCHANTABILITY OR
 * FITNESS FOR ANY PARTICULAR PURPOSE OR THAT THE USE OF THE SOFTWARE
 * OR DOCUMENTATION WILL NOT INFRINGE ANY THIRD PARTY PATENTS,
 * COPYRIGHTS, TRADEMARKS OR OTHER RIGHTS.COPYRIGHT HOLDERS WILL NOT
 * BE LIABLE FOR ANY DIRECT, INDIRECT, SPECIAL OR CONSEQUENTIAL
 * DAMAGES ARISING OUT OF ANY USE OF THE SOFTWARE OR DOCUMENTATION.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://gnu.org/licenses/>.
 */
class Skebby {

	protected $_gateway		= 'https://gateway.skebby.it/api/send/smseasy/advanced/http.php';

	protected $_config		= array();
	protected $_recipients	= array();
	protected $_text		= '';

	private $_error			= '';

	/**
	 * Constructor.
	 *
	 * @access public
	 * @param mixed $config
	 * @return void
	 */
	public function __construct($config) {
		$this->CI =& get_instance();

		if ( ! empty($config))
			$this->initialize($config);
	}

	// --------------------------------------------------------------------------

	/**
	 * Initialize library.
	 *
	 * @access public
	 * @param mixed $data
	 * @return void
	 */
	public function initialize($config) {
		$this->_config = $config;
	}

	// --------------------------------------------------------------------------

	/**
	 * Set SMS recipients.
	 * - '393*********' Single recipient
	 * - array('393*********', '393*********', ...) Multiple recipients
	 *
	 * @access public
	 * @param mixed $recipients
	 * @return void
	 */
	public function set_recipients($recipients) {
		if ( ! is_array($recipients)) {
			$recipients = array($recipients);
		}
		$this->_recipients = array_merge($this->_recipients, $recipients);
		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 * Set SMS text.
	 *
	 * @access public
	 * @param mixed $string
	 * @return void
	 */
	public function set_text($text) {
		$this->_text = $text;
		return $this;
	}

	// --------------------------------------------------------------------------

	/*
	 * Set sending method
	 */
	public function set_method($method) {
		$this->_config['method'] = $method;
		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 * Set sender (just 'classic' and 'classic_plus' method).
	 *
	 * @access public
	 * @param mixed $sender (int) for num, (string) for alphanum
	 * @return void
	 */
	public function set_sender($sender) {
		$this->_config['sender'] = $sender;
		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 * Perform SMS sending; return remaining SMS number.
	 *
	 * @access public
	 * @return void - Remaining SMS
	 */
	public function send() {
		$res = $this->_send_sms();
		return isset($res->remaining_sms)
			? $res->remaining_sms
			: FALSE;
	}

	// --------------------------------------------------------------------------

	/**
	 * Return remaining credit and the number of text messages that can be sent.
	 *
	 * @access public
	 * @return void
	 */
	public function get_credit() {

		$parameters = array(
			  'method'		=> 'get_credit'
			, 'username'	=> $this->_config['username']
			, 'password'	=> $this->_config['password']
			, 'charset'		=> $this->_config['charset']
		);

		return $this->_request($parameters);
	}

	// --------------------------------------------------------------------------

	/**
	 * Get error message.
	 *
	 * @access public
	 * @return void
	 */
	public function get_error() {
		return $this->_error;
	}

	// --------------------------------------------------------------------------

	/**
	 * Perform the sending of SMS.
	 *
	 * @access protected
	 * @return void
	 */
	protected function _send_sms() {

		$parameters = array(
			   'method'		=> $this->_config['method']
			 , 'username'	=> $this->_config['username']
			 , 'password'	=> $this->_config['password']
			 , 'charset'	=> $this->_config['charset']
			 , 'text'		=> $this->_text
			 , 'recipients'	=> $this->_recipients
		);

		if ( ! is_int($this->_config['sender'])) {
			$parameters['sender_string'] = $this->_config['sender'];
		} else {
			$parameters['sender_number'] = $this->_config['sender'];
		}

		return $this->_request($parameters);
	}

	// --------------------------------------------------------------------------

	/**
	 * Sending request.
	 *
	 * @access protected
	 * @param mixed $data_array
	 * @return void
	 */
	protected function _request($parameters_array) {
		$parameters = http_build_query($parameters_array);
		$res = array();

		$ch = curl_init();
    	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    	curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    	curl_setopt($ch, CURLOPT_USERAGENT, $this->_config['useragent']);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
    	curl_setopt($ch, CURLOPT_URL, $this->_gateway);

    	$response = curl_exec($ch);

    	parse_str($response, $res);

		if ($res['status'] == 'failed') {
			$this->set_error($res['code'], $res['message']);
			return FALSE;
		}

		unset($res['status']);
		return (object) $res;
	}

	// --------------------------------------------------------------------------

	/**
	 * Set error message.
	 *
	 * @access private
	 * @param mixed $error_label
	 * @return void
	 */
	private function set_error($code, $message) {
		$this->_error = '[Code: '. $code .'] '. urldecode(stripcslashes($message));
	}
}

/* End of file Skebby.php */
/* Location: ./application/libraries/Skebby.php */