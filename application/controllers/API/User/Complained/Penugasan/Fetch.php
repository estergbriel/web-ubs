<?php
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . '/libraries/JWT.php';
require APPPATH . '/libraries/key.php';
require APPPATH . '/libraries/ExpiredException.php';
require APPPATH . '/libraries/BeforeValidException.php';
require APPPATH . '/libraries/SignatureInvalidException.php';
require APPPATH . '/libraries/JWK.php';
 
//buat fetch list penugasan
class Fetch extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();  
    }

    public function index_get()
    {
        $authHeader = $this->input->request_headers()['Authorization'];
        $pass = verifyJWT($authHeader);
        if ($pass->code != 200) {
            $this->response($pass, REST_Controller::HTTP_UNAUTHORIZED); 
            return;
        }  
        $user = $pass->data; 
        
        $complains = $this->KomplainAModel->fetchForDivisi($user->KODEDIV,'PEND'); 
         
        $this->response([
            'data'=>$complains,
            'status'=>200
        ], REST_Controller::HTTP_OK);
    }
}
