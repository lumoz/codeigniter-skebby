<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function index() {

		$this->load->library('skebby');

		$something = $result = TRUE;

		$this->skebby->set_method('send_sms_classic')
			->set_sender('Me')
			->set_recipients(array('393*********', '393*********'));

		if ($something === TRUE) {
			// Add recipient...
			$this->skebby->set_recipients('393*********');
		}

		if ($result === FALSE) {
			$this->skebby->set_text('Sorry man…');
		} else {
			$this->skebby->set_text('You win!');
		}

		$remaining_sms = $this->skebby->send();

		if ($remaining_sms !== FALSE) {
			echo 'You have '. $remaining_sms .' SMS left, my dear';
		}
	}
}

/* End of file skebby.php */
/* Location: ./application/controllers/skebby.php */