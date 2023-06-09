<h1 class="h3 mb-4 text-gray-800" style="font-weight:bold">Tambah Subtopik2 </h1>

<?= error_button("Kembali", "fas fa-fw fa-step-backward", "", "", "Admin/Master/Subtopik2") ?>

<form action="<?= base_url() ?>Admin/Master/Subtopik2/AddProcess" method="post" class="mt-4" style="color:black;">
    <div class="row">
        <div class="col">
            <label class="form-label" id='labelTopik'>Topik : </label> <br>
            <label class="form-label" id='labelDivisi'>Divisi : </label>
            <input type="hidden" name="inputKodeTopik" id="inputKodeTopik">
            <input type="hidden" name="inputKodeSubtopik1" id="inputKodeSubtopik1">
        </div>
    </div>
    <div class="row">
        <div class="col">
            <label for="subtopik1" class="form-label">Subtopik 1 : </label>
            <select id="subtopik1" class="form-control">

                <?php
                foreach ($list_subtopik1 as $subtopik) {
                    // echo "<option value='$subtopik->SUB_TOPIK1'>$subtopik->SUB_TOPIK1 - $subtopik->DESKRIPSI</option>";

                    echo "<option value='$subtopik->KODE_TOPIK - $subtopik->TOPIK @$subtopik->SUB_TOPIK1^$subtopik->NAMA_DIVISI'>$subtopik->NAMA_DIVISI - $subtopik->SUB_TOPIK1 - $subtopik->DESKRIPSI</option>";
                }
                ?>
            </select>
        </div>
        <div class="col"></div>
    </div>
    <div class="row">
        <div class="col">
            <label for="deskripsi" class="form-label mt-5">Deskripsi</label>
            <textarea class="form-control" placeholder="" name="deskripsi" required></textarea>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col">
            <?= primary_submit_button("Tambah", "fas fa-fw fa-plus") ?>
        </div>
    </div>
</form>

<script type="text/javascript">
    //Script ini digunakan untuk mengubah label topik sesuai dengan subtopik 1 yang dipilih
    //dan juga mendapatkan kode topik dan subtopik 1 yang dipilih yang dimasukkan ke input hidden
    //gunanya untuk memudahkan proses insert data ke database, namun tetap memudahkan user untuk memilih topik dan subtopik 1 dari tampilan yang diberikan
    window.onload = function() {
        // let x = document.getElementById('subtopics1').childNodes[3].value;
        // console.log(x);
        let labelTopik = document.getElementById('labelTopik');
        let labelDivisi = document.getElementById('labelDivisi');
        let subTopik1 = document.getElementById('subtopik1');
        let inputKodeSubtopik1 = document.getElementById('inputKodeSubtopik1');
        let inputKodeTopik = document.getElementById('inputKodeTopik');

        //cari string @ di subtopik1.value
        let indexSubtopik1 = subTopik1.value.indexOf("@");
        let indexDivisi = subTopik1.value.indexOf("^");
        let indexTopik = subTopik1.value.indexOf("-");

        let kodeTopikShow = subTopik1.value.substring(0, 3) + " - ";
        let topikShow = subTopik1.value.substring(indexTopik + 1, indexSubtopik1 - 1);
        labelTopik.innerText = "Topik : " + kodeTopikShow + topikShow;
        labelDivisi.innerText = "Divisi : " + subTopik1.value.substring(indexDivisi + 1);

        inputKodeSubtopik1.value = subTopik1.value.substring(indexSubtopik1 + 1,indexDivisi);
        inputKodeTopik.value = subTopik1.value.substring(0, indexTopik-1);

        subTopik1.addEventListener('change', function() {
            let indexSubtopik1 = subTopik1.value.indexOf("@");
            let indexTopik = subTopik1.value.indexOf("-");
            let indexDivisi = subTopik1.value.indexOf("^");

            let kodeTopikShow = subTopik1.value.substring(0, 3) + " - ";
            let topikShow = subTopik1.value.substring(indexTopik + 1, indexSubtopik1 - 1);
            labelTopik.innerText = "Topik : " + kodeTopikShow + topikShow;
            labelDivisi.innerText = "Divisi : " + subTopik1.value.substring(indexDivisi + 1);

            inputKodeSubtopik1.value = subTopik1.value.substring(indexSubtopik1 + 1,indexDivisi);
            inputKodeTopik.value = subTopik1.value.substring(0, indexTopik-1);

            // console.log(inputKodeSubtopik1.value);
            // console.log(inputKodeTopik.value);
        });
    }
</script>