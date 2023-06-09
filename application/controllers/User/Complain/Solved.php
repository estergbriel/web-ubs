<?php
    //untuk validasi komplain yang diajukan oleh departemen user tersebut 
    class Solved extends CI_Controller {
        public function __construct(){
            parent::__construct();
            $this->data['page_title'] = "Halaman Penyelesaian Komplain Diterima";
            $this->data['navigation'] = "Solved";  

            middleware_auth(1); //hak akses user 
            
            $this->data['login'] = $this->UsersModel->getLogin();
             
            $this->load->library("form_validation");  
            $this->load->library('session'); 

    }
    public function index(){ 
        $data = $this->data;
        $data['page_title'] = "Halaman Daftar Penyelesaian Komplain Diterima";

        //fetch complain user tersebut yang statusnya PEND dan belum ada PENUGASAN
        $complains = $this->KomplainAModel->fetchKomplainDone($data['login']->NOMOR_INDUK); 
        $data['complains'] = $complains;

        loadView_User("user/complain/solved/list",$data); 
    }

    public function detail($nomor_komplain){
        
        $data = $this->data;
        $data['page_title'] = "Halaman Detail Penyelesaian Komplain Diterima";
        
        middleware_komplainA($nomor_komplain,'User/Complain/Solved',true);
        $komplain = $this->KomplainAModel->get($nomor_komplain);
 
        $data['komplain'] = $komplain;
        
        loadView_User("user/complain/solved/detail",$data); 
    }
    /**
     * Pemberian keputusan pada sebuah komplain digunakan untuk memberikan keputusan pada sebuah penyelesaian komplain yang telah diberikan oleh divisi bersangkutan berdasarkan parameter nomor komplain yang dikirim oleh user. Aksi ini dilakukan jika user yang memberikan komplain sudah menerima penyelesaian komplain dari divisi terkait. 
     * 
     * Untuk request ini membutuhkan dua parameter, yaitu keputusan dan permintaan banding. Untuk keputusan sendiri terdapat 3 jenis, yaitu banding, validasi, dan cancel. Function ini dapat dijalankan di controller Solved pada direktori Complain.
     */
    public function solveProcess($nomor_komplain){
        middleware_komplainA($nomor_komplain,'User/Complain/Solved',true);
        
        $komplain = $this->KomplainAModel->get($nomor_komplain);
 
        $keputusan = $this->input->post('keputusan');
        $permintaanBanding = $this->input->post('permintaanBanding');
         
        $today = date('Y-m-d'); 
        
        $resultmail = false;
        $resultmailRecepient = false;
 
        //isi apabila banding
        if($keputusan=='banding'){
            if($permintaanBanding==''){
                redirectWith('User/Complain/Solved/detail'.$nomor_komplain,'Permintaan banding harus diisi'); 
            }
            $komplainB = new KomplainBModel();
            $komplainB->NO_KOMPLAIN = $nomor_komplain;
            $komplainB->KEBERATAN = $permintaanBanding;
            $komplainB->updateBandingKomplain();

            //update komplainA
            $komplain->TGL_BANDING = $today;
            $komplain->USER_BANDING = $this->UsersModel->getLogin()->NOMOR_INDUK;
            $komplain->updateBandingKomplain();
 
            $header = "Sukses melakukan banding penyelesaian komplain";
            $message = "Sistem telah mencatat anda melakukan banding atas komplain $nomor_komplain, dengan keluhan $permintaanBanding. Mohon ditunggu untuk tindakan lanjutan dari divisi bersangkutan. Terima kasih.";

            $template = templateEmail($header, $this->UsersModel->getLogin()->NAMA,
            $message);
   
            $resultmail = send_mail($this->UsersModel->getLogin()->EMAIL, 
            $header, $template);  
            
            $headerRecipient = "Sukses melakukan banding penyelesaian komplain";
            $messageRecipient = "Sistem telah mencatat anda mendapatkan banding atas komplain $nomor_komplain, dengan keluhan $permintaanBanding. Mohon menindaklanjuti permintaan divisi bersangkutan. Terima kasih.";

            $templateRecipient =  templateEmail($headerRecipient, $komplain->PENANGANAN->NAMAPENERBIT,
            $messageRecipient);

            $resultmailRecepient = send_mail($komplain->PENANGANAN->EMAIL, 
            $headerRecipient, $templateRecipient); 

        }else if($keputusan=='cancel'){
            //update komplainA
            $komplain->TGL_CANCEL = $today;
            $komplain->USER_CANCEL = $this->UsersModel->getLogin()->NOMOR_INDUK;
            $komplain->updateCancelKomplain();

            
            $header = "Sukses membatalkan penyelesaian komplain";
            $message = "Sistem telah mencatat anda melakukan pembatalan (cancel) atas komplain $nomor_komplain, terima kasih.";
            $template = templateEmail($header, $this->UsersModel->getLogin()->NAMA,
            $message);
            
            $resultmail = send_mail($this->UsersModel->getLogin()->EMAIL, 
            $header, $template); 

            
            $headerRecipient = "Pembatalan penyelesaian komplain";
            $messageRecipient = "Sistem telah mencatat terdapat pembatalan penyelesaian komplain atas komplain dengan nomor $nomor_komplain.";

            $templateRecipient =  templateEmail($headerRecipient, $komplain->PENANGANAN->NAMAPENERBIT,
            $messageRecipient);

            $resultmailRecepient = send_mail($komplain->PENANGANAN->EMAIL, 
            $headerRecipient, $templateRecipient); 

        }else{
            //validasi
            //update komplainA
            $komplain->TGL_VALIDASI = $today;
            $komplain->USER_VALIDASI = $this->UsersModel->getLogin()->NOMOR_INDUK;
            $komplain->updateValidasiKomplain(); 
           
            $header = "Sukses memvalidasi penyelesaian komplain";
            $message = "Sistem telah mencatat anda telah melakukan validasi atas komplain $nomor_komplain, terima kasih atas kerja sama anda.";
            $template = templateEmail($header, $this->UsersModel->getLogin()->NAMA,
            $message);
   
            $resultmail = send_mail($this->UsersModel->getLogin()->EMAIL, 
            $header, $template); 

            
            $headerRecipient = "Validasi penyelesaian komplain";
            $messageRecipient = "Sistem telah mencatat terdapat validasi penyelesaian komplain atas komplain dengan nomor $nomor_komplain.";

            $templateRecipient =  templateEmail($headerRecipient, $komplain->PENANGANAN->NAMAPENERBIT,
            $messageRecipient);

            $resultmailRecepient = send_mail($komplain->PENANGANAN->EMAIL, 
            $headerRecipient, $templateRecipient); 
        }
        if($resultmail==true && $resultmailRecepient==true){ 

            redirectWith('User/Complain/Solved','Berhasil memberikan keputusan pada penyelesaian komplain, silahkan cek email anda'); 
        }else{ 
            redirectWith('User/Complain/Solved','Berhasil memberikan keputusan pada penyelesaian komplain, namun gagal mengirim email');
        }
    }

     
}