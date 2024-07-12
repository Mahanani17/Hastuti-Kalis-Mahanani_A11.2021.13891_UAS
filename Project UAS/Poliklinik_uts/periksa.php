<?php 
$page="Periksa";

// Memeriksa apakah pengguna sudah login, jika tidak, arahkan kembali ke halaman login
if(!isset($_SESSION["username"])){
    header("location: index.php?page=loginUser");
    exit;
}
?>

<!-- FORM -->
<div class="form-group mx-sm-3 mb-2">
    <form action="" onsubmit="return(validate());" method="post">
    <!-- AMBIL DATA UNTUK UBAH -->
        <?php
        include ('koneksi.php');
        $id_pasien = '';
        $id_dokter = '';
        $tgl_periksa = '';
        $catatan = '';
        $obat = '';
        if (isset($_GET['id'])) {
            $ambil = mysqli_query($mysqli, 
            "SELECT * FROM periksa 
            WHERE id='" . $_GET['id'] . "'");
            while ($row = mysqli_fetch_array($ambil)) {
                $id_pasien = $row['id_pasien'];
                $id_dokter = $row['id_dokter'];
                $tgl_periksa = $row['tgl_periksa'];
                $catatan = $row['catatan'];
                $obat = $row['obat'];
            }
        ?>
            <input type=hidden name="id" value="<?php echo
            $_GET['id'] ?>">
        <?php
        }
        ?>
    <!-- SELECT PASIEN -->
        <label class="fw-bold">Pasien</label>
       <select class="form-control my-2" name="id_pasien">
           <?php
           include ('koneksi.php');
           $selected = '';
           $periksa = mysqli_query($mysqli, "SELECT * FROM pasien");
           while ($data = mysqli_fetch_array($periksa)) {
               if ($data['id'] == $id_pasien) {
                   $selected = 'selected="selected"';
               } else {
                   $selected = '';
               }
           ?>
               <option value="<?php echo $data['id'] ?>"  <?php echo $selected?>> <?php echo $data['nama'] ?></option>
           <?php
           }
           ?>
       </select>
    <!-- SELECT DOKTER -->
       <label class="fw-bold">Dokter</label>
       <select class="form-control my-2" name="id_dokter">
           <?php
           include ('koneksi.php');
           $selected = '';
           $dokter = mysqli_query($mysqli, "SELECT * FROM dokter");
           while ($data = mysqli_fetch_array($dokter)) {
               if ($data['id'] == $id_dokter) {
                   $selected = 'selected="selected"';
               } else {
                   $selected = '';
               }
           ?>
               <option value="<?php echo $data['id'] ?>"  <?php echo $selected ?>><?php echo $data['nama'] ?></option>
           <?php
           }
           ?>
       </select>
    <!-- COLOM INSERT DATETIME DAN TEXT -->
       <label class="fw-bold">Tanggal Periksa</label>
       <input type="datetime-local" name="tgl_periksa" value="<?php echo $tgl_periksa ?>" class="form-control my-2"  required>
       
       <label class="fw-bold">Catatan</label>
       <input type="text" class="form-control my-2" name="catatan"  value="<?php echo $catatan ?>"> 
       
       <label  class="fw-bold">Obat</label>
       <textarea class="form-control my-2" name="obat"><?php echo $obat ?></textarea> 

       <button class="btn btn-primary" type="submit" name="simpan" >Submit</button>
    </form>

    <!-- TABLE -->
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                <th scope="col">No</th>
                <th scope="col">Nama Pasien</th>
                <th scope="col">Nama Dokter</th>
                <th scope="col">Tanggal Periksa</th>
                <th scope="col">Catatan</th>
                <th scope="col">Obat</th>
                <th scope="col">Aksi</th>
                </tr>
            </thead>
            <?php
            include ('koneksi.php');
            date_default_timezone_set("Asia/Jakarta");
            $result = mysqli_query($mysqli, "SELECT pr.*,d.nama as 'nama_dokter', p.nama as 'nama_pasien' FROM periksa pr LEFT JOIN dokter d ON (pr.id_dokter=d.id) LEFT JOIN pasien p ON (pr.id_pasien=p.id) ORDER BY pr.tgl_periksa ASC");
            $no = 1;
            while ($data = mysqli_fetch_array($result)) {
            ?>
                <tr>
                    <td><?php echo $no++ ?></td>
                    <td><?php echo $data['nama_pasien'] ?></td>
                    <td><?php echo $data['nama_dokter'] ?></td>
                    <td><?php echo date('d-M-Y H:i:s', strtotime ($data['tgl_periksa']))  ?></td>
                    <td><?php echo $data['catatan'] ?></td>
                    <td><?php echo nl2br($data['obat']) ?></td>
                    <td>
                        <a class="btn btn-success rounded-pill px-3" 
                        href="index.php?page=periksa&id=<?php echo $data['id'] ?>">
                        Ubah</a>
                        <a class="btn btn-danger rounded-pill px-3" 
                        href="index.php?page=periksa&id=<?php echo $data['id'] ?>&aksi=hapus">Hapus</a>
                        <a class="btn btn-info rounded-pill px-3" 
                        href="invoice.php?id=<?php echo $data['id'] ?>">
                        Notta</a>
                    </td>
                </tr>
            <?php
            }
            ?>
        </table>
    </div>
</div>

<!-- FUNGSI CRUD -->
<?php
include ('koneksi.php');
if (isset($_POST['simpan'])) {
    $id_pasien = $_POST['id_pasien'];
    $id_dokter = $_POST['id_dokter'];
    $tgl_periksa = $_POST['tgl_periksa'];
    $catatan = $_POST['catatan'];
    $obat = $_POST['obat'];
    if (isset($_POST['id'])) {
        $sql = "UPDATE periksa SET id_pasien='$id_pasien',id_dokter='$id_dokter',tgl_periksa='$tgl_periksa',catatan = '$catatan',obat = '$obat' WHERE id = ". $_POST['id']."";
    } else {
        $sql = "INSERT INTO periksa (id_pasien,id_dokter,tgl_periksa,catatan,obat) VALUES ('$id_pasien','$id_dokter','$tgl_periksa','$catatan','$obat')";
    }
        
    if ($mysqli->query($sql) == TRUE)
    { echo "<script> 
        document.location='index.php?page=periksa';
        </script>";}
    else
    {echo "Error: " . $sql . "<br>" . $mysqli->error;}
    $mysqli->close();
}

if (isset($_GET['aksi'])) {
    if ($_GET['aksi'] == 'hapus') {
        $hapus = mysqli_query($mysqli, "DELETE FROM periksa WHERE id = '" . $_GET['id'] . "'");
    }

    echo "<script> 
            alert('Data Berhasil Dihapus');
            document.location='index.php?page=periksa';
            </script>";
}
?>