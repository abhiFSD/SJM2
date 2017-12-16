<?php
use Aws\Sqs\SqsClient;
/**
 * Library for commonly used AWS Functions
 * 
 * @author Prasanth
 *
 */


class Aws
{
   
   // $CI->your_library->do_something();
    
    private $CI;
    
    
     
    public function __construct()
    {
        $this->CI =   & get_instance();
        
		require_once APPPATH."/libraries/aws/aws-autoloader.php";
      
    }
    
    /**
     * Get the SQS client for getting the message
     * @return Ambigous <\Aws\static, \Aws\Sqs\SqsClient>
     */
    public function getSQS($region = 'us-west-2') 
    {
        
        $sqsClient = SqsClient::factory(array(
                'credentials' => array(
                        'key'    => 'AKIAJUJ6NYLG5FOAWWRA',
                        'secret' => '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',
                ),
                'region' => $region
        ));
        
        return $sqsClient;
        
    }
    
    
    
}