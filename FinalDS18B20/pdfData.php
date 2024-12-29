<?php

// Pastikan autoload path sudah benar
require __DIR__ . '/../vendor/autoload.php'; // Sesuaikan path ke autoload Composer
require 'koneksi.php'; // Include file koneksi ke database

use Spipu\Html2Pdf\Html2Pdf;

// Mengambil data dari database
$data_pengunjung = mysqli_query($koneksi, "SELECT * FROM esp32_table_ds18b20_record ORDER BY time ASC");

// Cek jika query tidak berhasil
if (!$data_pengunjung) {
    die("Query gagal: " . mysqli_error($koneksi));
}

$content = '
<page>
    <h2 align="center">Data Sensor HC-SR04</h2>
    <table border="1" align="center" style="width: 100%; border-collapse: collapse;">
        <tr>
            <th align="center">No</th>
            <th align="center">Id</th>
            <th align="center">Board</th>
            <th align="center">Temperature 1 (°C)</th>
            <th align="center">Temperature 2 (°C)</th>
            <th align="center">Temperature 3 (°C)</th>
            <th align="center">Temperature 4 (°C)</th>
            <th align="center">Status Read Sensor DS18B20</th>
            <th align="center">Time</th>
            <th align="center">Date (dd-mm-yyyy)</th>
        </tr>';

// Inisialisasi nomor urut
$no = 1;

// Memasukkan data ke dalam tabel PDF
while ($sensor = mysqli_fetch_assoc($data_pengunjung)) {
    // Format date
    $date = date_create($sensor['date']);
    $dateFormat = date_format($date, "d-m-Y");

    $content .= '
        <tr>
            <td align="center">' . $no++ . '</td>
            <td align="center">' . $sensor['id'] . '</td>
            <td align="center">' . $sensor['board'] . '</td>
            <td align="center">' . $sensor['temperature1'] . '</td>
            <td align="center">' . $sensor['temperature2'] . '</td>
            <td align="center">' . $sensor['temperature3'] . '</td>
            <td align="center">' . $sensor['temperature4'] . '</td>
            <td align="center">' . $sensor['status_read_sensor_DS18B20'] . '</td>
            <td align="center">' . $sensor['time'] . '</td>
            <td align="center">' . $dateFormat . '</td>
        </tr>';
}

$content .= '
    </table>
</page>';

// Menggunakan orientasi lanskap dan ukuran A4
$html2pdf = new Html2Pdf('L', 'A4', 'en'); // 'L' berarti landscape
$html2pdf->writeHTML($content);
$html2pdf->output('data_sensor_DS18B20.pdf', 'D');
