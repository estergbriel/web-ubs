<?php 
class Email extends CI_Controller{
    
    public function __construct()
    {
        parent::__construct();
        $this->data['page_title'] = "Halaman Master Email Admin";
        $this->data['navigation'] = "Master"; 

        $this->load->library("form_validation");

        
        middleware_auth(4); //hak akses admin
    }

    public function index(){
         //halaman ini digunakan untuk menampilkan daftar email yang ada
         $data = $this->data;
         $data['page_title'] = "Master Email";
         $data['login'] = $this->UsersModel->getLogin();
 
         $users = $this->UsersModel->fetch();
         $data['users'] = $users;  

         loadView_Admin("admin/master/email/index", $data); 
    }
    public function Add(){ 
         //halaman ini digunakan untuk menampilkan form tambah email
         $data = $this->data;
         $data['page_title'] = "Master Email";
         $data['login'] = $this->UsersModel->getLogin();
 
         $users = $this->UsersModel->fetch();
         $managers = $this->UsersModel->fetchAtasan();
         $users_no_email = $this->UsersModel->fetchWithoutEmail();
         $data['users'] = $users;  
         $data['users_no_email'] =  $users_no_email;
         $data['managers'] =  $managers;
        
        loadView_Admin("admin/master/email/add", $data); 
    }
    public function AddProcess(){ 
        $noInduk_atasan = $this->input->post("inputAtasan"); 
        $noIndukuser =  $this->input->post("user"); 
        $emailUser =  $this->input->post("email_user"); 

        //cek ubslinux
        $posisidomain = strpos($emailUser, "@ubslinux.com");
        // if($posisidomain == false){
        //     redirectWith('Admin/Master/Email/Add','Domain email harus menggunakan @ubslinux.com !');
        // }

        $cekCollisionEmail = $this->UsersModel->checkEmailExist($emailUser); 

        if($cekCollisionEmail){ 
            $this->session->set_flashdata('header', 'Pesan');
            $this->session->set_flashdata('message', 'Email ini sudah pernah digunakan! ');
            redirect('Admin/Master/Email/Add');
        }else{ 
            $user = new UsersModel();
            $user->EMAIL = $emailUser;
            $user->NOMOR_INDUK = $noIndukuser;
            $user->KODE_ATASAN = $noInduk_atasan;
            $user->addEmail();
            

            $this->session->set_flashdata('header', 'Pesan');
            $this->session->set_flashdata('message', 'Berhasil Menambah Email User! ');
            redirect('Admin/Master/Email');
        }


    }
    public function Detail($nomor_induk){ 
        $data = $this->data;
        $data['page_title'] = "Master Email";
        $data['login'] = $this->UsersModel->getLogin();

        $user = $this->UsersModel->get($nomor_induk);
        
        if($user==null){
            $this->session->set_flashdata('header', 'Pesan');
            $this->session->set_flashdata('message', 'User ini  tidak ditemukan! ');
            redirect('Admin/Master/Email');
        }

        //code ini untuk memunculkan opsi user terkait yang memiliki email dan user yang tidak memiliki email
        $managers = $this->UsersModel->fetchAtasan();
        $users = $this->UsersModel->fetchWithoutEmail();
        
        for ($i=0; $i < sizeof($users); $i++) { 
            if($users[$i]->EMAIL == $user->EMAIL){
                unset($users[$i]);
            }
        }
        $users[] = $user;


        $data['users'] = $users;   
        $data['user'] = $user;   
        $data['managers'] =  $managers;
       
        loadView_Admin("admin/master/email/edit", $data); 

    }
    public function EditProcess($nomor_induk){
        $noInduk_atasan = $this->input->post("inputAtasan");  
        $emailUser =  $this->input->post("email_user"); 

        $user = $this->UsersModel->get($nomor_induk);
        
        //cek ubslinux
        // $posisidomain = strpos($emailUser, "@ubslinux.com");
        // if($posisidomain == false){
        //     redirectWith('Admin/Master/Email/Detail/'.$nomor_induk,'Domain email harus menggunakan @ubslinux.com !'); 
        // } 
        
        $cekCollisionEmail = $this->UsersModel->checkEmailExist($emailUser); 

        if($cekCollisionEmail && $user->EMAIL != $emailUser){  
            redirectWith('Admin/Master/Email/Detail/'.$nomor_induk, 'Email ini sudah pernah digunakan!');
        }else{ 

            $user = new UsersModel();
            $user->EMAIL = $emailUser;
            $user->NOMOR_INDUK = $nomor_induk;
            $user->KODE_ATASAN = $noInduk_atasan;
            $user->addEmail();
        
            redirectWith('Admin/Master/Email', 'Berhasil Mengubah Email User!');
        }
    }
    public function DeleteProcess($nomor_induk){
        
        $user = new UsersModel(); 
        $user->NOMOR_INDUK = $nomor_induk;
 
        $user->resetEmail();
 
        redirectWith('Admin/Master/Email', 'Berhasil Menghapus data email dan atasan');
    }

}