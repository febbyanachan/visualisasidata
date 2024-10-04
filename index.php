<?php
// Fungsi untuk membaca data dari file CSV
function readCSV($filename) {
    $data = [];
    if (($handle = fopen($filename, 'r')) !== false) {
        fgetcsv($handle); // Abaikan header pertama
        $header = fgetcsv($handle); // Baca header

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) === count($header)) {
                $data[] = array_combine($header, $row);
            }
        }
        fclose($handle);
    }
    return $data;
}

// Baca data dari CSV
$data = readCSV('data.csv');

// Dapatkan daftar provinsi
$provinces = array_column($data, 'Provinsi');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/visualisasi.png" type="image/png"> <!-- Ganti icon di sini -->
    <title>Visualisasi Upah Rata-rata Pekerja</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="judul-container">
    <h1>VISUALISASI DATA</h1>
</div>


<div class="container">
    <div class="sidebar">
        
        <h2>Filter</h2>
        <!-- Filter Provinsi -->
        <label for="province" class="filter-label">Filter Provinsi:</label>
        <select id="province" class="filter-select" onchange="updateCharts()">
            <option value="all">Semua</option>
            <?php foreach ($provinces as $province): ?>
                <option value="<?php echo $province; ?>"><?php echo $province; ?></option>
            <?php endforeach; ?>
        </select>

        <!-- Filter Tahun -->
        <label for="year" class="filter-label">Filter Tahun:</label>
        <select id="year" class="filter-select" onchange="updateCharts()">
            <option value="all">Semua</option>
            <option value="2021">2021</option>
            <option value="2022">2022</option>
            <option value="2023">2023</option>
        </select>
        

        <br><br><br><br>

        <h2>Anggota Kelompok TM-21-1</h2>
        <ul class="team-members">
            <li>Febbyana Chandra</li>
            <li>Angelin Julia</li>
            <li>Alvin Stansley Wijaya</li>
            <li>Kevin</li>
            <li>Rico Fernando</li>
        </ul>

        <br>

        <!-- Tombol untuk menampilkan data -->
                <div class="button-container" style="text-align: center; margin-top: 20px;">
        <a href="data.php" class="data-button">Lihat Data Tabel</a>
        </div>
    </div>


        <div class="chart-container">
            <h2>Visualisasi Upah Rata-rata Per Jam Pekerja Menurut Provinsi</h2>
            <div class="charts-row">
                <div class="chart-box">
                    <h2>Upah Rata-rata</h2>
                    <canvas id="wageChart" width="400" height="250"></canvas>
                </div>
                <div class="chart-box">
                    <h2>Upah Rata-rata</h2>
                    <canvas id="pieChart" width="400" height="250"></canvas>
                </div>
            </div>
            <div class="chart-box">
                <h2>Upah Rata-rata</h2>
                <canvas id="lineChart" width="400" height="250"></canvas>
            </div>
        </div>
    </div>


    <script>
    const data = <?php echo json_encode($data); ?>; // Kirim data ke JavaScript
    const ctxBar = document.getElementById('wageChart').getContext('2d');
    const ctxLine = document.getElementById('lineChart').getContext('2d');
    const ctxPie = document.getElementById('pieChart').getContext('2d');

    // Chart Bar
        let wageChart = new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Upah Rata-rata (Rupiah/Jam)',
                    data: [],
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1,
                    hoverBackgroundColor: 'rgba(75, 192, 192, 0.8)',
                    hoverBorderColor: 'rgba(75, 192, 192, 1)',
                    hoverBorderWidth: 3,
                    // Tambahkan efek bayangan
                    shadowOffsetX: 2,
                    shadowOffsetY: 2,
                    shadowBlur: 10,
                    shadowColor: 'rgba(0, 0, 0, 0.5)',
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Provinsi'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Upah (Rupiah/Jam)',
                        },
                        beginAtZero: true
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.dataset.label + ': ' + tooltipItem.raw.toLocaleString();
                            }
                        }
                    }
                },
                elements: {
                    bar: {
                        borderWidth: 2,
                        borderColor: 'rgba(0, 0, 0, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.5)',
                        hoverBackgroundColor: 'rgba(255, 165, 0, 0.7)',
                        hoverBorderColor: 'rgba(0, 0, 0, 1)',
                        hoverBorderWidth: 3,
                        // Tambahkan ini untuk efek timbul
                        tension: 0.4,
                        // Efek timbul
                        y: function(tooltipItem) {
                            return tooltipItem.parsed.y + 10; // Menaikkan bar 10px saat dihover
                        }
                    }
                }

            }
        });

        // Chart Garis
        let lineChart = new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Upah Rata-rata (Rupiah/Jam)',
                    data: [],
                    fill: false,
                    borderColor: 'rgba(54, 162, 235, 1)', // Ganti dengan warna biru
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Provinsi'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Upah (Rupiah/Jam)',
                        },
                        beginAtZero: true
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.dataset.label + ': ' + tooltipItem.raw.toLocaleString();
                            }
                        }
                    }
                },
                elements: {
                    line: {
                        tension: 0.4, // Tambahkan ini untuk efek kelancaran
                        borderWidth: 4, // Lebar garis
                        borderColor: 'rgba(54, 162, 235, 1)', // Warna garis
                        fill: true, // Isi area di bawah garis
                        hoverBackgroundColor: 'rgba(54, 162, 235, 0.2)', // Warna saat hover
                    }
                }
            }

        });

        // Chart Pie
        let pieChart = new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: [],
                datasets: [{
                    label: 'Upah Rata-rata (Rupiah/Jam)',
                    data: [],
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.5)', // Biru muda
                        'rgba(54, 162, 235, 0.5)', // Biru
                        'rgba(25, 99, 132, 0.5)', // Biru gelap
                        'rgba(153, 102, 255, 0.5)', // Ungu
                        'rgba(54, 162, 235, 0.5)', // Biru
                    ],
                    borderColor: 'rgba(0, 0, 0, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': ' + tooltipItem.raw.toLocaleString() + ' (Tahun: ' + tooltipItem.dataIndex + ')';
                            }
                        }
                    },
                    // Menambahkan efek timbul pada pie chart
                    pieChart: {
                        hoverOffset: 20 // Menambah offset saat hover
                    }
                }

            }
        });


    function updateCharts() {
        wageChart.data.labels = [];
        wageChart.data.datasets[0].data = [];

        lineChart.data.labels = [];
        lineChart.data.datasets[0].data = [];

        pieChart.data.labels = [];
        pieChart.data.datasets[0].data = [];

        const provinceFilter = document.getElementById('province').value;
        const yearFilter = document.getElementById('year').value;

        const provinceData = data.filter(row => provinceFilter === 'all' || row['Provinsi'] === provinceFilter);

        if (provinceFilter === 'all') {
            provinceData.forEach(row => {
                if (yearFilter === 'all') {
                    const averageWage = (parseFloat(row['2021']) + parseFloat(row['2022']) + parseFloat(row['2023'])) / 3; // Rata-rata dari semua tahun
                    wageChart.data.labels.push(row['Provinsi']);
                    wageChart.data.datasets[0].data.push(averageWage);
                    
                    // Data untuk chart garis
                    lineChart.data.labels.push(row['Provinsi']);
                    lineChart.data.datasets[0].data.push(averageWage);
                    
                    // Data untuk chart pie
                    pieChart.data.labels.push(row['Provinsi'] + ' (2021, 2022, 2023)'); // Menambahkan tahun pada label
                    pieChart.data.datasets[0].data.push(averageWage);
                } else {
                    // Menangani tahun yang dipilih
                    const wage = parseFloat(row[yearFilter]);
                    if (!isNaN(wage)) {
                        wageChart.data.labels.push(row['Provinsi']);
                        wageChart.data.datasets[0].data.push(wage);
                        
                        // Data untuk chart garis
                        lineChart.data.labels.push(row['Provinsi']);
                        lineChart.data.datasets[0].data.push(wage);
                        
                        // Data untuk chart pie
                        pieChart.data.labels.push(row['Provinsi'] + ' (' + yearFilter + ')'); // Menambahkan tahun pada label
                        pieChart.data.datasets[0].data.push(wage);
                    }
                }
            });
        } else {
            const selectedProvince = provinceData[0]; // Ambil data provinsi yang dipilih
            if (yearFilter === 'all') {
                // Tampilkan data untuk semua tahun
                for (let year of ['2021', '2022', '2023']) {
                    const wage = parseFloat(selectedProvince[year]);
                    if (!isNaN(wage)) {
                        wageChart.data.labels.push(selectedProvince['Provinsi']);
                        wageChart.data.datasets[0].data.push(wage);
                        
                        // Data untuk chart garis
                        lineChart.data.labels.push(year);
                        lineChart.data.datasets[0].data.push(wage);
                        
                        // Data untuk chart pie
                        pieChart.data.labels.push(selectedProvince['Provinsi'] + ' (' + year + ')'); // Menambahkan tahun pada label
                        pieChart.data.datasets[0].data.push(wage);
                    }
                }
            } else {
                const wage = parseFloat(selectedProvince[yearFilter]);
                if (!isNaN(wage)) {
                    wageChart.data.labels.push(selectedProvince['Provinsi']);
                    wageChart.data.datasets[0].data.push(wage);
                    
                    // Data untuk chart garis
                    lineChart.data.labels.push(yearFilter);
                    lineChart.data.datasets[0].data.push(wage);
                    
                    // Data untuk chart pie
                    pieChart.data.labels.push(selectedProvince['Provinsi'] + ' (' + yearFilter + ')'); // Menambahkan tahun pada label
                    pieChart.data.datasets[0].data.push(wage);
                }
            }
        }

        wageChart.update();
        lineChart.update();
        pieChart.update();
    }

    // Inisialisasi grafik
    updateCharts();
    </script>
</body>
</html>
