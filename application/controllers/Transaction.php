<?php
class Transaction extends MY_Controller
{
    public function import()
    {
        if ($this->session->userdata('role_id') > 2) show_404();
        
        if ($this->input->post())
        {
            $file_data = $this->get_upload('csv');

            if (false !== ($fh = fopen($file_data['full_path'], 'r')))
            {
                $this->load->model('Txnimportmodel');
                $duplicates = [];
                $new = [];
                
                // flush headers
                fgetcsv($fh, 1000, ",");
                
                $columns = ['id', 'date_authorised', 'kiosk_number', 'position', 'sku_value', 'amount', 'currency', 'payment_method', 'card_method', 'card_type', 'first_4_digits', 'last_4_digits', 'confirmation_number', 'remarks'];

                while (($data = fgetcsv($fh, 1000, ",")) !== FALSE) 
                {
                    // prep data
                    $row = [];
                    $display_row = [];
                    foreach ($columns as $index => $column_name)
                    {
                        if (!empty($data[$index]))
                        {
                            $row[$column_name] = $data[$index];
                        }

                        $display_row[$column_name] = $data[$index];
                    }

                    // check duplicate
                    if ($this->Txnimportmodel->is_duplicate($row['date_authorised'], $row['kiosk_number']))
                    {
                        $duplicates[] = $display_row;
                    }
                    else
                    {
                        $this->Txnimportmodel->insert($row);
                        $new[] = $display_row;
                    }
                }

                $this->view_data['duplicates'] = $duplicates;
                $this->view_data['new'] = $new;
                $this->view_data['success'] = true;

                fclose($fh);
            }
            else
            {
                $this->view_data['msg'] = "Could not open file.";
            }

        }

        $this->default_view('transaction/import');
    }

    public function update_deployment()
    {
        // fetch all the deployment id null transactions
        $this->db->where('deployment_id is null', NULL, false);
        $query = $this->db->get_where('transaction', null, 5000);

        $transactions = $query->result();

        $this->load->model('DeploymentModel');
        // update the value
        foreach ($transactions as $transaction) 
        {
            $transactionTime = new DateTime($transaction->transaction_machine_time);
            $deploymentId  = $this->DeploymentModel->getDeploymentByDate($transaction->operator_id, $transactionTime);

            $data['deployment_id'] = $deploymentId;

            $this->db->update('transaction', $data, array('id' => $transaction->id));
        }
    }


}