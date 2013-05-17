CodeIgniter Skebby Library
=================================

This is a CodeIniger Library to work with [Skebby](http://www.skebby.it/) API.

-----

####How to install

1. Copy all files in your application folder.
2. Edit `config/skebby.php` file with your data.
3. Edit `controllers/welcome.php` with real phone numbers.

####How to use

1. How to load library:

		$this->load->library('skebby');
	
2. How to get credit:

		$credit = $this->skebby->get_credit();
		if ( ! $credit) {
			echo $this->skebby->get_error();
		} else {
			print_r($credit);
		}		


3. How to send a SMS:

		$res = $this->skebby->set_recipients('393*********')
			->set_recipients(array('393*********', '393*********'))
			->set_text('The answer to life the universe and everything.')
			->send();
		
		if ( ! $res) {
			echo $this->skebby->get_error();
		} else {
			print_r($res);
		}
	
4. How to change method:

		$this->skebby->set_method('send_sms_classic');

5. How to change sender:

		$this->skebby->set_sender('Douglas');

6. Chaotic things:

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
	

####Public function list:

- `set_recipients` Set SMS recipients (string for single recipient, array for multiple recipients)
- `set_text` Set SMS text
- `set_method` Set sending method
- `set_sender` Set sender (just 'classic' and 'classic_plus' method)
- `send` Perform SMS sending; return remaining SMS number
- `get_credit` Return remaining credit and the number of text messages that can be sent
- `get_error` Get error message