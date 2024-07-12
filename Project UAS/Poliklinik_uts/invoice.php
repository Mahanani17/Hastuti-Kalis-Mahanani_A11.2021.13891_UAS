<?php 
// Memeriksa apakah sesi sudah dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$page = "Invoice";

// Memeriksa apakah pengguna sudah login, jika tidak, arahkan kembali ke halaman login
if (!isset($_SESSION["username"])) {
    header("location: index.php?page=loginUser");
    exit;
}

// Include database connection
include ('koneksi.php');

// Memeriksa apakah parameter 'id' diset di URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT pr.*, d.nama AS nama_dokter, d.alamat AS alamat_dokter, d.no_hp AS no_hp_dokter, 
            p.nama AS nama_pasien, p.alamat AS alamat_pasien, p.no_hp AS no_hp_pasien
            FROM periksa pr 
            LEFT JOIN dokter d ON pr.id_dokter = d.id 
            LEFT JOIN pasien p ON pr.id_pasien = p.id
            WHERE pr.id = '$id'";
    $result = mysqli_query($mysqli, $sql);
    // $periksa = mysqli_query($mysqli, "SELECT * FROM periksa WHERE id = '$id'");
    // $row = mysqli_fetch_array($periksa);

    // if ($row) {
    //     $id_pasien = $row['id_pasien'];
    //     $id_dokter = $row['id_dokter'];
    //     $tgl_periksa = $row['tgl_periksa'];
    //     $catatan = $row['catatan'];
    // }

    // $dokter = mysqli_query($mysqli, "SELECT * FROM ");

    if (!$result) {
        die("Query failed: " . mysqli_error($mysqli));
    }

    $data = mysqli_fetch_assoc($result);

    if ($data) {
        $jasa_dokter = 150000; // Jasa Dokter fixed amount
        $obat_list = explode("\n", $data['obat']);
        $total_obat = 0;
    } else {
        // Handle the case where no data is found for the given id
        echo "No data found for the given id.";
        exit;
    }
} else {
    // Jika parameter 'id' tidak diset di URL, tampilkan pesan kesalahan
    echo "ID not set in URL. Please provide a valid ID.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <link rel="stylesheet" href="path/to/your/css/bootstrap.min.css">
    <style>
        .invoice {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 16px;
            line-height: 24px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }
        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }
        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }
        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }
        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }
        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }
        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }
        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }
        .invoice-box table tr.item td{
            border-bottom: 1px solid #eee;
        }
        .invoice-box table tr.item.last td {
            border-bottom: none;
        }
        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="invoice">
    <h2 class="text-center">Nota Pembayaran</h2>
    <table cellpadding="0" cellspacing="0">
        <tr class="top">
            <td colspan="2">
                <table>
                    <tr>
                        <td>
                            No. Periksa: #<?php echo $data['id']; ?><br>
                            Tanggal Periksa: <?php echo date('Y-m-d H:i:s', strtotime($data['tgl_periksa'])); ?><br>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        
        <tr class="information">
            <td colspan="2">
                <table>
                    <tr>
                        <td>
                            Pasien:<br>
                            <?php echo $data['nama_pasien']; ?><br>
                            <?php echo $data['alamat_pasien']; ?><br>
                            <?php echo $data['no_hp_pasien']; ?>
                        </td>
                        
                        <td>
                            Dokter:<br>
                            <?php echo $data['nama_dokter']; ?><br>
                            <?php echo $data['alamat_dokter']; ?><br>
                            <?php echo $data['no_hp_dokter']; ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        
        <tr class="heading">
            <td>
                Deskripsi
            </td>
            
            <td>
                Harga
            </td>
        </tr>
        
        <tr class="item">
            <td>
                Jasa Dokter
            </td>
            
            <td>
                Rp <?php echo number_format($jasa_dokter, 2, ',', '.'); ?>
            </td>
        </tr>

        <?php
        $i = 0;
        $total_obat = 0; // pastikan $total_obat diinisialisasi sebelum loop

        while ($i < count($obat_list)) {
            $obat_name = $obat_list[$i];
            $harga = mysqli_query($mysqli, "SELECT nama_obat, harga FROM obat WHERE nama_obat = '$obat_name'");
            
            if ($harga) {
                $obat_exec = mysqli_fetch_assoc($harga);
                if ($obat_exec) {
                    $total_obat += $obat_exec['harga'];
                ?>
                <tr class="item">
                    <td>
                        <?php echo $obat_exec['nama_obat']; ?>
                    </td>
                    <td>
                        Rp <?php echo number_format($obat_exec['harga'], 2, ',', '.'); ?>
                    </td>
                </tr>
                <?php 
                }
            }
            $i++;
        }
        ?>


        <tr class="total">
            <td></td>
            
            <td>
               Subtotal Obat: Rp <?php echo number_format($total_obat, 2, ',', '.'); ?>
            </td>
        </tr>

        <tr class="total">
            <td></td>
            
            <td>
               Total: Rp <?php echo number_format($jasa_dokter + $total_obat, 2, ',', '.'); ?>
            </td>
        </tr>
    </table>
</div>

</body>
</html>
