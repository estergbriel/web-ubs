<?php
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . '/libraries/JWT.php';
require APPPATH . '/libraries/key.php';
require APPPATH . '/libraries/ExpiredException.php';
require APPPATH . '/libraries/BeforeValidException.php';
require APPPATH . '/libraries/SignatureInvalidException.php';
require APPPATH . '/libraries/JWK.php';
 
//buat add complain pilih subtopik2
class PilihSubTopik2 extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();  
    }

    public function index_get($iget,$divisiParam, $topikParam, $subTopik1Param)
    {
        //untuk dapat list divisi
        $authHeader = $this->input->request_headers()['Authorization'];
        $pass = verifyJWT($authHeader);
        if ($pass->code > 299) {
            $this->response($pass, REST_Controller::HTTP_UNAUTHORIZED); 
            return;
        }    
         
        $divisi =  $this->DivisiModel->get($divisiParam);     
        $topik =  $this->TopikModel->get($topikParam);   
        $subtopik1 =  $this->SubTopik1Model->get($topikParam,$subTopik1Param);  
        $allSubTopik2 = $this->SubTopik2Model->fetchBySubTopik1($topikParam,$subTopik1Param);
        $this->response([
            'data'=>$allSubTopik2,
            'divisi'=>$divisi,
            'topik'=>$topik,
            'subtopik1'=>$subtopik1,
            'status'=>200
        ], REST_Controller::HTTP_OK);

    }
}
