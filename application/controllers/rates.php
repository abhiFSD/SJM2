<?php if (!defined('BASEPATH')) die();
class Rates extends Main_Controller {

   public function index()
	{
		
		$rates = $this->db->get('rate');
		
		
		$gbpRate = $this->conversionRate('GBP');
		$audRate = $this->conversionRate('AUD');

		$euRate = $this->conversionRate('EUR');

		

		$userId = $this->session->userdata('user_id');
		
		$this->load->view('include/home_header', array( 'userId' => $userId) );
      	$this->load->view('rate', array('rates' => $rates, 'gbp' => $gbpRate, 'aud' => $audRate, 'eu' => $euRate));
      	$this->load->view('include/home_footer');
      	
	}
	
	
	
	
	public function do_upload()
	{
		
		$config['upload_path'] = '/home3/computa/public_html/assets/uploads';
		$config['allowed_types'] = 'csv|application/vnd.ms-excel|text/comma-separated-values|application/csv|application/excel|application/vnd.ms-excel|application/vnd.msexcel|text/anytext|text/csv';
		$config['max_size']	= '10000';
		
	
		$this->load->library('upload', $config);
		
		if ( ! $this->upload->do_upload())
		{
			$error = array('error' => $this->upload->display_errors());
			
			
			
		}
		else
		{
			$data = array('upload_data' => $this->upload->data());
	
			$data = $this->upload->data();
			$this->import_rates($data);
			redirect('rates/upload');
		}
		
	}
	
	public function filter($country, $currency) 
	{
		

		$rate = $this->conversionRate($currency);

		
		
		$query = $this->db->get_where('rate', array('destination' => $country));
		$result = $query->result_array();
		if(count($result) >0) {
			switch($currency) {
				case "USD":
					$data['currency'] = '$'; 
					
					break;
				case "AUD":
					$data['currency'] = 'A$';
					
					break;
				case "GBP":
					$data['currency'] = '&pound;';
					
					break;
				case "EUR":
					$data['currency'] = '&euro;';
					
					break;
						
			}
			
			$data['mobile'] = number_format($result[0]['usd_mobile'] * $rate, 3);

			$data['landline'] = number_format($result[0]['usd_landline'] * $rate, 3);
			
		} else {
			$data['msg'] = "No rates found for this country";
		}
		$data['currency_value'] = $currency;
		
		$page = $this->load->view('rates/filter', $data, true);
		echo $page;
	}
	
	private function conversionRate($to)
	{
		
		
		$base = file_get_contents('http://api.fixer.io/latest?symbols=USD');
		$conversionResult = json_decode($base);
		

		$baserate = $conversionResult->rates->USD; //1.34
		
		$usdRate = 1/$baserate;
		
		if ($to == 'EUR') {
			return $usdRate;
		}
	
		$data = file_get_contents('http://api.fixer.io/latest?symbols='.$to);

		$result = json_decode($data);

		$actualRate = ($result->rates->$to/$usdRate);
		
		//$conversionResult = json_decode($data);
		 		
		return $actualRate;
	}
	
	private function import_rates($file)
	{
		
		$fp = fopen($file['full_path'], 'r');
		while (($row = fgetcsv($fp)) !== FALSE) {
			
			$data[] = array(
					
							"destination" 		=> $row[0],
							"usd_mobile"		=> $row[1],
							"usd_landline"		=> $row[2],
							
								
								
			);
		}
		
		$this->db->insert_batch('rate', $data);
		
	}

	public function upload()
	{
		
		
		$role = $this->session->userdata('role_id');
		$userId = $this->session->userdata('user_id');
		
		
		$this->load->view('include/header', array('role' => $this->session->userdata('role_id'), 'name' => $this->session->userdata('name')));
		$this->load->view('rates/upload');
		$this->load->view('include/footer');
	}
	
	public function manage($offset = 0, $limit = 10, $criteria = null)
	{
		
		if($this->session->userdata('email_address') == "" ) {
			redirect('/user/login');
		}
		
		$db = clone $this->db;
		$countQ = $this->db->get('rate');
		$db->limit($limit, $offset);
		$query = $db->get('rate');
		
		$config = array();
		$config["base_url"] = site_url('rates/manage');
		$config["total_rows"] = $countQ->num_rows;
		$config["per_page"] = 10;
		$config["uri_segment"] = 3;
		$config['full_tag_open'] = '<ul class="pagination">';
		$config['full_tag_close'] = '</ul>';
		
		$this->pagination->initialize($config);
		
		$msg = "";
		$this->load->view('include/header', array('role' => $this->session->userdata('role_id'), 'name' => $this->session->userdata('name')));
		$this->load->view('rates/manage', array('rates' => $query, 'pages' => $this->pagination->create_links(), 'msg' => $msg));
		$this->load->view('include/footer');
	}
} 