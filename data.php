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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/visualisasi.png" type="image/png"> <!-- Ganti icon di sini -->
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <title>Tabel Data CSV</title>
</head>
<body>
    <div class="data-container">
        <div class="juduldata-container">
            <h1>Tabel Data</h1>
            <a href="index.php" class="back-button">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    
    <div class="chart-container">
        <table class="data-table">
            <thead>
                <tr>
                    <?php if (!empty($data)): ?>
                        <?php foreach (array_keys($data[0]) as $header): ?>
                            <th>
                                <?php echo htmlspecialchars($header); ?>
                                <?php if ($header !== 'Tahun'): ?>
                                    <select multiple class="filter-select-data" onchange="filterTable()">
                                        <option value="">Pilih Filter...</option>
                                        <?php
                                        // Ambil nilai unik untuk filter dari setiap kolom, kecuali tahun
                                        $uniqueValues = array_unique(array_column($data, $header));
                                        foreach ($uniqueValues as $value):
                                        ?>
                                            <option value="<?php echo htmlspecialchars($value); ?>"><?php echo htmlspecialchars($value); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php else: // Dropdown untuk filter tahun ?>
                                    <select multiple class="filter-select-data" onchange="filterTable()">
                                        <option value="">Pilih Tahun...</option>
                                        <?php
                                        // Ambil nilai unik untuk tahun
                                        $uniqueYears = array_unique(array_column($data, 'Tahun'));
                                        foreach ($uniqueYears as $year):
                                        ?>
                                            <option value="<?php echo htmlspecialchars($year); ?>"><?php echo htmlspecialchars($year); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php endif; ?>
                            </th>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row): ?>
                    <tr>
                        <?php foreach ($row as $cell): ?>
                            <td><?php echo htmlspecialchars($cell); ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div style="margin-top: 20px; text-align: center;">
        <a href="index.php" class="data-button">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <br><br>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.filter-select-data').select2({
                placeholder: "Pilih Filter...",
                allowClear: true
            });
        });

        function filterTable() {
            const table = document.querySelector('.data-table');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const filterInputs = document.querySelectorAll('.filter-select-data');

            // Filter Rows
            rows.forEach(row => {
                let showRow = true;

                filterInputs.forEach((select, index) => {
                    const selectedValues = Array.from(select.selectedOptions).map(option => option.value.toLowerCase());
                    const cellValue = row.cells[index].textContent.toLowerCase();

                    // Periksa apakah nilai dalam pilihan filter ada dalam nilai sel
                    if (selectedValues.length > 0 && !selectedValues.includes(cellValue)) {
                        showRow = false;
                    }
                });

                row.style.display = showRow ? '' : 'none';
            });
        }
    </script>
</body>
</html>
