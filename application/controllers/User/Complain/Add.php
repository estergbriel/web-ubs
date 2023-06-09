<?php   
    //input komplain baru diajukan user 
     
    class Add extends CI_Controller {
      /**
       * Untuk bagian constructor class berisi kode-kode yang digunakan untuk memanggil middleware dan pemanggilan library yang dibutuhkan. Constructor akan dijalankan sebelum function tujuan dijalankan, sehingga middleware bisa dijalankan di semua function dalam class tersebut.
       */
        public function __construct(){
            parent::__construct();
            middleware_auth(1); //hak akses user 
            $this->data['page_title'] = "User Page";
            $this->data['navigation'] = "Complain";  
            $this->data['login'] = $this->UsersModel->getLogin();
 
            $this->load->library("form_validation");  
            $this->load->library('session'); 
            $this->load->library('upload');
            $this->load->library('email'); 
        }
        public function index(){
          redirect("User/Complain/Add/pilihDivisi");
        }
        public function pilihDivisi(){
          $data = $this->data;
          $data['page_title'] = "Tambah Komplain Baru";
 
          $data['allDivisi'] = $this->DivisiModel->fetch();
          
          loadView_User("user/complain/add/input-divisi",$data);
        }
        public function processPilihDivisi(){ 
          $divisi =  $this->input->post("divisi"); 
          redirect("User/Complain/Add/pilihTopik/$divisi");
        }
        public function pilihTopik($divisi=null){
          $data = $this->data;
          $data['page_title'] = "Pilih Topik Tambah Komplain Baru";
 
          if($divisi==null){
            $this->session->set_flashdata('message', 'Silahkan memilih divisi untuk dikomplain terlebih dahulu');
              redirect('User/Complain/Add/pilihDivisi');
          }
          $data["divisi"] =  $this->DivisiModel->get($divisi);  
          $data["allTopik"] = $this->TopikModel->fetchByDivisi($divisi);
           
          loadView_User("user/complain/add/input-topik",$data);
        }
        public function processPilihTopik($divisi=null){
          $topik =  $this->input->post("topik"); 
          redirect("User/Complain/Add/pilihSubTopik1/$divisi/$topik");
        }
        public function pilihSubTopik1($divisi=null,$topik=null){ 
          
          $data = $this->data;
          $data['page_title'] = "Tambah Komplain Baru"; 
 
          if($divisi==null || $topik==null){
            $this->session->set_flashdata('message', 'Silahkan memilih divisi untuk dikomplain terlebih dahulu');
              redirect('User/Complain/Add/pilihDivisi');
          } 
          $data["divisi"] =  $this->DivisiModel->get($divisi);  
          $data["topik"] =  $this->TopikModel->get($topik);  
          $data["allSubTopik1"] = $this->SubTopik1Model->fetchByTopik($topik); 
          //todo fetch topik per divisi
          
          loadView_User("user/complain/add/input-subtopik1",$data);
        }
        public function processPilihSubTopik1($divisi=null,$topik=null){
          $subtopik1 =  $this->input->post("subtopik1"); 
          redirect("User/Complain/Add/pilihSubTopik2/$divisi/$topik/$subtopik1");
        }
        public function pilihSubTopik2($kode_divisi=null,$kode_topik=null,$kode_subtopik1=null){
          $data = $this->data;
          $data['page_title'] = "Tambah Komplain Baru";
          
  
            
          $data['dateNow'] =  date("Y-m-d");
          
          $data['minDate'] = date('Y-m-d', strtotime('-14 days'));   
          if($kode_divisi==null || $kode_topik==null ||$kode_subtopik1==null){
            $this->session->set_flashdata('message', 'Silahkan memilih divisi untuk dikomplain terlebih dahulu');
              redirect('User/Complain/Add/pilihDivisi');
          } 
          $data["divisi"] =  $this->DivisiModel->get($kode_divisi);  
          $data["topik"] =  $this->TopikModel->get($kode_topik);  
          $data["subtopik1"] =  $this->SubTopik1Model->get($kode_topik,$kode_subtopik1);  
          $data["allSubTopik2"] = $this->SubTopik2Model->fetchBySubTopik1($kode_topik,$kode_subtopik1);
          
          loadView_User("user/complain/add/input-subtopik2",$data);
        }
        public function processPilihSubtopik2($divisi=null,$topik=null,$subtopik1=null){
          $subtopik2 =  $this->input->post("subtopik2"); 
          $tanggal =  $this->input->post("tanggal"); 
          
          $tanggalMin = date('Y-m-d', strtotime('-14 days'));
          if($tanggal < $tanggalMin){
              $this->session->set_flashdata('header', 'Pesan');
              $this->session->set_flashdata('message', 'Tanggal tidak boleh lebih dari 14 hari');
              redirect("User/Complain/Add/pilihSubTopik2/$divisi/$topik/$subtopik1");
          }
          //process 
          $this->session->set_userdata('tanggalPreSended', $tanggal);
          
          redirect("User/Complain/Add/pilihLampiran/$divisi/$topik/$subtopik1/$subtopik2");

        }
        public function pilihLampiran($kode_divisi=null,$kode_topik=null,$kode_subtopik1=null,$kode_subtopik2=null){
          $data = $this->data;
          $data['page_title'] = "Tambah Komplain Baru"; 
 
          $data['tanggalPreSended'] = $this->session->userdata('tanggalPreSended');
          $data['divisi'] = $this->DivisiModel->get($kode_divisi);
          $data['topik'] = $this->TopikModel->get($kode_topik);
          $data['subtopik1'] = $this->SubTopik1Model->get($kode_topik,$kode_subtopik1);
          $data['subtopik2'] = $this->SubTopik2Model->get($kode_topik,$kode_subtopik1,$kode_subtopik2);
          loadView_User("user/complain/add/input-lampiran",$data); 
        }

        /**
         * Untuk bagian function tambah komplain berisi kode-kode yang digunakan untuk menambahkan data komplain baru dengan insert ke database ke tabel komplain A, komplain B, dan jika dibutuhkan pada tabel lampiran apabila komplain tersebut memiliki lampiran terkait.
         */
        public function addComplainProcess($kode_divisi=null,$kode_topik=null,$kode_subtopik1=null,$kode_subtopik2=null){
          $topik =  $kode_topik;
          $subtopik1 = $kode_subtopik1;
          $subtopik2 =  $kode_subtopik2;
          $tanggal = $this->session->userdata('tanggalPreSended'); 
          $deskripsi = $this->input->post("deskripsi");
          $feedback = $this->input->post("feedback"); 

          $lampirans = $_FILES["lampiran"];
          
          $today = date('Y-m-d'); 
         
          $divisi = $this->DivisiModel->get($kode_divisi);

          $newkode = $this->KomplainAModel->getNewKode(); 
          $newkomplain = new KomplainAModel();
          $newkomplain->NO_KOMPLAIN = $newkode;
          $newkomplain->TOPIK = $topik;
          $newkomplain->SUB_TOPIK1 = $subtopik1;
          $newkomplain->SUB_TOPIK2 = $subtopik2;
 
          $newkomplain->TGL_KEJADIAN = $tanggal;
          

          $newkomplain->TGL_TERBIT = $today;
          $newkomplain->TGL_VERIFIKASI = null;
          $newkomplain->USER_VERIFIKASI = null;
          $newkomplain->TGL_CANCEL = null;
          $newkomplain->USER_CANCEL = null;
          $newkomplain->TGL_BANDING = null;
          $newkomplain->USER_BANDING = null;
          $newkomplain->TGL_VALIDASI = null;
          $newkomplain->USER_VALIDASI = null;
          $newkomplain->PENUGASAN = null;
          $newkomplain->STATUS = 'OPEN';
          $newkomplain->TGL_PENANGANAN = null;
          $newkomplain->USER_PENANGANAN = null;
          $newkomplain->USER_PENANGANAN = null;
          $newkomplain->TGL_DEADLINE = null;
          $newkomplain->TGL_DONE = null;
          $newkomplain->USER_DONE = null;
          $newkomplain->USER_PENERBIT = $this->UsersModel->getLogin()->NOMOR_INDUK;
          
          $newkomplain->insert(); 

          //insert komplainb
          $newkomplainb = new KomplainBModel();
          $newkomplainb->NO_KOMPLAIN = $newkode;
          $newkomplainb->DESKRIPSI_MASALAH = $deskripsi;
          $newkomplainb->AKAR_MASALAH = '';
          $newkomplainb->T_KOREKTIF = '';
          $newkomplainb->T_PREVENTIF = '';
          $newkomplainb->KEBERATAN = ''; 
          $newkomplainb->insert();
          
          $isError = false;
          //Untuk bagian function upload lampiran komplain berisi kode-kode yang digunakan untuk melakukan upload file, yaitu memindahkan file yang dikirim ke folder uploads di dalam server, lalu menambahkan data di tabel lampiran sesuai komplain yang diakses.
          if($lampirans['name'][0]!=""){ 
              if (!file_exists('./uploads/')) {
                  mkdir('./uploads/', 0777, true);
              } 
              for($i=0;$i < count($lampirans['name']);$i++){
                  $getNewFileName = 'K_WEB'. generateUID(22).substr($newkode,-3,3);
                  
                  if($i < count($lampirans)){ 
                      $_FILES['lampiran']['name'] = $lampirans['name'][$i];
                      $_FILES['lampiran']['type'] = $lampirans['type'][$i];
                      $_FILES['lampiran']['tmp_name'] = $lampirans['tmp_name'][$i];
                      $_FILES['lampiran']['error'] = $lampirans['error'][$i];
                      $_FILES['lampiran']['size'] = $lampirans['size'][$i];

                      $ext = pathinfo($lampirans['name'][$i], PATHINFO_EXTENSION);

                      $config['upload_path'] = './uploads/';
                      $config['allowed_types'] = 'gif|jpg|png|pdf|jpeg|txt|docx|xlsx|csv';
                      $config['max_size'] = 5000; // in Kilobytes
                      $config['file_name'] = $getNewFileName;

                      $this->load->library('upload', $config);
                      $this->upload->initialize($config);

                      if (!$this->upload->do_upload('lampiran')) {
                          // Handle upload error
                          $error = $this->upload->display_errors();
                          // echo $error;
                          // echo FCPATH.'uploads/';
                          $isError = true;
                      } else {
                          // Upload success
                          $upload_data = $this->upload->data();
                          $file_name = $upload_data['file_name']; 
                          $newLampiran = new LampiranModel();
                          $newLampiran->KODE_LAMPIRAN = $getNewFileName.".".$ext;
                          $newLampiran->NO_KOMPLAIN = $newkode;
                          $newLampiran->TANGGAL = $today;
                          $newLampiran->TIPE = 0; //komplain
                          $newLampiran->insert();
                      }
                  }
              } 
          } 
          if($isError){ 
              $this->session->set_flashdata('message', 'Terdapat error dalam upload lampiran, pastikan ukuran file tidak lebih dari 5 MB');
              redirect("User/Complain/Add/pilihLampiran/$kode_divisi/$kode_topik/$kode_subtopik1/$kode_subtopik2");
          }else{ 
              $topikDes = $this->TopikModel->get($kode_topik)->DESKRIPSI;
              $subtopik1Des = $this->SubTopik1Model->get($kode_topik,$kode_subtopik1)->DESKRIPSI;
              $subtopik2Des = $this->SubTopik2Model->get($kode_topik,$kode_subtopik1,$subtopik2)->DESKRIPSI;
              $template = templateEmail("Notifikasi Penambahan Komplain Baru",$this->UsersModel->getLogin()->NAMA, 
              "Sistem mencatat anda telah mengirimkan sebuah komplain baru terkait topik $topikDes - $subtopik1Des - $subtopik2Des. Komplain anda akan diteruskan ke divisi $divisi->NAMA_DIVISI, dengan keterangan '$deskripsi'"
              );
              $resultmail = send_mail($this->UsersModel->getLogin()->EMAIL, 
              'Notifikasi Penambahan Komplain Baru', $template);  

              $this->session->unset_userdata('tanggalPreSended');   
              if($resultmail){ 
                
                $this->session->set_flashdata('header', 'Pesan');
                $this->session->set_flashdata('message', 'Berhasil menambahkan komplain baru, silahkan cek email anda'); 
                  redirect('User/Complain/ListComplain');
              }else{ 
                  $this->session->set_flashdata('message', 'Berhasil menambahkan komplain baru, namun gagal mengirim email');
                  redirect('User/Complain/ListComplain');
              }

              //TODO email manajer  
          }
      }  



        public function page_DEPRECATED($page){
            $data = $this->data;
            $data['page_title'] = "Tambah Komplain Baru";
            
 
            if($page==1){ 
                //dapatkan input lama kalau ada
                $data['subtopik2PreSended'] = $this->session->userdata('subtopik2PreSended');
                $data['tanggalPreSended'] = $this->session->userdata('tanggalPreSended');

                //dapetkan 14 hari yg lalu

                $data['tanggalMin'] = date('Y-m-d', strtotime('-14 days'));
                $data['tanggalSekarang'] = date('Y-m-d');

                //ambil data subtopik 2, subtopik 1, dan complain selain divisi tersebut
                $subtopics = $this->SubTopik2Model->fetchSubtopikAll($data['login']->KODEDIV,false); 
                $data['subtopics'] = $subtopics; 
                $this->load->view("templates/user/header", $data);
                $this->load->view("user/complain/add/input-topic", $data);
                $this->load->view("templates/user/footer", $data);
            }else{
                $subtopik2 =  $this->session->userdata('subtopik2PreSended');
                if($subtopik2==null){
                    redirect('User/Complain/Add/page/1');
                }
                $data['subtopik2PreSended'] = $subtopik2;
                $data['topikShow'] = $subtopik2;
                $data['tanggalPreSended'] = $this->session->userdata('tanggalPreSended');
             
                $subtopics = $this->SubTopik2Model->fetchSubtopikAll($data['login']->KODEDIV,false); 
                foreach ($subtopics as $subtopik) {
                    if($subtopik->SUB_TOPIK2==$subtopik2){  
                        $data['topikShow'] = "$subtopik->KODE_TOPIK - $subtopik->SUB_TOPIK1 - $subtopik->S2DESKRIPSI";
                    }
                } 
                loadView_User("user/complain/add/input-detail", $data);
            }
 
        } 

        public function pageProcess1_DEPRECATED(){
            // $topik =  $this->input->post("inputTopik");
            // $subtopik1 =  $this->input->post("inputSubtopik1");
            // $subtopik2 =  $this->input->post("inputSubtopik2");
            // $tanggal =  $this->input->post("tanggal");

            // //cek date
            
            // $tanggalMin = date('Y-m-d', strtotime('-14 days'));
            // if($tanggal < $tanggalMin){
            //     $this->session->set_flashdata('header', 'Pesan');
            //     $this->session->set_flashdata('message', 'Tanggal tidak boleh lebih dari 14 hari');
            //     redirect('User/Complain/Add/page/1');
            // }
            // //untuk menampung data sebelum dikirim
            // $this->session->set_userdata('tanggalPreSended', $tanggal);
            // $this->session->set_userdata('subtopik2PreSended', $subtopik2);
            // $this->session->set_userdata('subtopik1PreSended', $subtopik1);
            // $this->session->set_userdata('topikPreSended', $topik);
            // redirect('User/Complain/Add/page/2');
        }

 
 
    }
  