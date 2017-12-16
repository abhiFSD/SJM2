<?php

class Customer extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		
	}
   
   
    public function register()
    {
       if ($this->input->post()) {

           $customerId = $this->input->post('customer');

           if ($customerId <= 0) {

               $uniqueCode = $this->getNewId();

               $customerData = array(
                   "first_name" => $this->input->post('first_name'),
                   "last_name" => $this->input->post('last_name'),
                   "gender" => $this->input->post('gender'),
                   "email_address" => $this->input->post('email_address'),
                   "date_of_birth" => $this->input->post('dob'),
                   "country" => $this->input->post('country'),
                   "postcode" => $this->input->post('postcode'),
                   "promotions" => $this->input->post('promotions'),
                   "unique_code" => $uniqueCode,
                   'created_date' => date('Y-m-d H:i:s')
               );

               $this->db->insert('customer', $customerData);

               $customerId = $this->db->insert_id();
           }

           $config['upload_path'] = BASEPATH . '/../uploads/customer/';
           $config['allowed_types'] = 'gif|jpg|png';
           $config['max_size'] = '2048';

           $this->load->library('upload', $config);
           $data['photo'] = '';

           if (!$this->upload->do_upload('photo')) {
               $error = array('error' => $this->upload->display_errors());
           } else {
               $upload_data = array('upload_data' => $this->upload->data());
               $data['photo'] = $upload_data['upload_data']['file_name'];
           }


           $productData = array(
               "purchase_date" => $this->input->post('purchase_date'),
               "name" => $this->input->post('product_name'),
               "site" => $this->input->post('site'),
               "state" => $this->input->post('state'),
               "location" => $this->input->post('location'),
               "serial_number" => $this->input->post('serial'),
               'created_date' => date('Y-m-d H:i:s'),
               "customer_id" => $customerId,
               "photo" => $data['photo']

           );

           $this->db->insert('registered_customer_product', $productData);


           $this->load->library('email');

           $this->email->from('support@powerpod.net');
           $this->email->to($customerData['email_address']);
           $this->email->subject('PowerPod - Product Registration: ' . $productData['name']);
           $content = "Dear Customer \n";
           $content .= "Thanks for purchasing " . $productData['name'] . "\n\n";
           $content .= "Our support team will verify the details and get back to you with the invoice.";
           $content .= "\n\nThanks, PowerPod Team.";
           $this->email->message($content);
           $this->email->send();

           $consumerData = "";
           foreach ($customerData as $key => $value) {
               $consumerData .= str_replace("_", " ", $key) . " : ". $value. "\n";
           }

           foreach ($productData as $key => $value) {
               $consumerData .= str_replace("_", " ", $key) . " : ". $value. "\n";
           }

           $this->email->from('support@powerpod.net');
           $this->email->to('admin@powerpod.net');
           $this->email->subject('PowerPod - Product Registration: ' . $productData['name']);
           $content = "A new product registration is received: \n\n";
           $content .= $consumerData;
           $content .= "\n\nThanks, PowerPod Team.";
           $this->email->message($content);
           $this->email->send();



           redirect('http://www.powerpod.net/pages/thanks-for-registering-with-us');
       }

        $products = $this->db->get('item');
        $sites = $this->db->get('site');
        $locations = $this->db->get('kiosk_location');
        
        $productsStr = "";
        foreach ($products->result() as $product) {
            $productsStr .= '"'. $product->name .'",';
        }
        
        $data['products'] =  $productsStr ;
        
        $params['disableMenu'] = 1;
        $params['turnOff'] = 0;
        
        $this->load->view("templates/header.php", $params);
        $this->load->view('customer/register', $data);
        $this->load->view("templates/footer.php", $params);

    }


    public function thanks()
    {
        $params['disableMenu'] = 1;
        $params['turnOff'] = 0;
        $this->load->view("templates/header.php", $params);
        $this->load->view('customer/thankyou');
        $this->load->view("templates/footer.php", $params);
    }

    private function getNewId()
    {

        $lastId = $this->db->select('unique_code')->order_by('id','desc')->limit(1)->get('customer')->row('unique_code');

        if ($lastId) {
           $lastId = (int)str_replace('PC', '', $lastId);
        } else {
            $lastId = 0;
        }
        $lastId++;

        $lastId = sprintf("%07s",$lastId);
        $lastId = 'PC'.$lastId;
        return $lastId;
    }


    public function checkcustomer()
    {
        $email = $this->input->post('email');

        $userData = $this->db->get_where('customer', array('email_address' => $email));

        if ($userData->num_rows() > 0 ) {
            $data = $userData->result();

            $details = array(
                "id" => $data[0]->id,
                "data" => "Success"
            );


        } else {
            $details = array(

                "data" => "Error"
            );

        }
        echo json_encode($details);
    }


    public function getallsites($state)
    {

        $data = $this->db->order_by('name')->get_where('site', array('state' => $state, 'status' => 'Active'));
        $siteData = array();
        foreach ($data->result() as $site) {
            $siteData[] = array (

                "id"	=> $site->site_id,
                "name"	=> $site->name

            );
        }

        echo json_encode($siteData);

    }

    public function getalllocations($siteId = null, $date = null)
    {
        $this->db->select('d.id, l.name');
        $this->db->select("case when d.status = 'Removed' then case when uninstalled_date > '".$date."' then 1 else 0 end else 1 end as un_date  ");
        $this->db->from('kiosk_deployment as d');
        $this->db->join('kiosk_location as l', 'l.location_id = d.location_id');
        $this->db->join('site as s', 's.id = l.site_id');
        $this->db->join('licensor as lr', 's.licensor_id = lr.id');
        $this->db->where("lr.licensor_id != 'LIC00001'");
        //affected-area
        if ($siteId != null && $date != null) {
            $this->db->where("s.id ='". $siteId ."'");
            $this->db->where("d.installed_date < '". $date ."'");

        } 
        $db = $this->db->get();
        $locations = array();
        foreach ($db->result_object() as $site)
        {

            if ($site->un_date == null || $site->un_date == 0) {

                continue;
            }

        
            $locations[] = array (
                'id'          => $site->id,
                'name'        => $site->name,
               
            );
        }



        echo json_encode($locations);


    }

}
