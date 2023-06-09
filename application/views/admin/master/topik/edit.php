<h1 class="h3 mb-4 text-gray-800" style="font-weight:bold">Detail Topik</h1>

<?= error_button("Kembali", "fas fa-fw fa-step-backward", "", "", "Admin/Master/Topik") ?>

<form action="<?= base_url() ?>Admin/Master/Topik/EditProcess/<?= $topic->KODE_TOPIK; ?>" method="post" class="mt-4" style="color:black;">
    <div class="row">
        <div class="col">
            <label for="topik" class="form-label">Topik</label>
            <input type="text" class="form-control" placeholder="Masukkan Topik" aria-label="First name" name="topik" value="<?= $topic->TOPIK; ?>" required>

            <label for="inputPassword5" class="form-label mt-4">Nama</label>
            <input type="text" class="form-control" placeholder="Masukkan Nama" aria-label="First name" name="nama" value="<?= $topic->NAMA; ?>" required>
        </div>
        <div class="col">
            <label for="inputPassword5" class="form-label">Divisi Tujuan</label>
            <select name="divisi" class="form-control">
               
                <?php
                    foreach ($list_divisi as $divisi) { 
                        if($divisi->KODE_DIVISI == $topic->DIV_TUJUAN){ 
                            echo "<option value='$divisi->KODE_DIVISI' selected>$divisi->NAMA_DIVISI</option>";
                        }else{ 
                            echo "<option value='$divisi->KODE_DIVISI'>$divisi->NAMA_DIVISI</option>";
                        }
                    }
                ?>
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <label for="inputPassword5" class="form-label mt-5">Deskripsi</label>
            <textarea class="form-control" placeholder="" name="deskripsi" required><?= $topic->DESKRIPSI ?></textarea>
        </div> 
    </div>
    <div class="row mt-4">
        <div class="col">   
            
            <?= error_button("Hapus", "fas fa-fw fa-trash", "btnDelete") ?>

            <?= primary_submit_button("Ubah", "fas fa-fw fa-pen",) ?>
    </div> 
    </div>
</form>



<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Konfirmasi</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Apakah anda yakin ingin menghapus topik ini?</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Tidak</button>
                    <a class="btn btn-danger" href="<?= base_url() ?>Admin/Master/Topik/DeleteProcess/<?= $topic->KODE_TOPIK; ?>">Ya</a>
                </div>
            </div>
        </div>
    </div> 


    <script src="<?= asset_url(); ?>js/template/confirmDeleteModalMasterTopik.js"></script>