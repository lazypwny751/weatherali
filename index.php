<?php
// Sayfa ile alakalı ayarlamalar.
$TITLE      = "WeatherAli";
$SEHIR      = "Düzce";
$YIL        = "2020";

// Yapay zeka verisinin içinde olduğu veri tabanı.
$database = "weatherali.db";

// CSS dosyalarını içeren bir dizi.
$cssFiles = array(
    "yardimcilar/css/bootstrap.min.css"
);

// JS dosyalarını içeren bir dizi.
$jsFiles = array(
    "yardimcilar/js/bootstrap.min.js"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        .card {
            margin: 10px;
            text-align: center;
        }
        .card-title {
            font-size: 24px;
        }

        <?php
        // CSS dosyalarını include et
        foreach ($cssFiles as $cssFile) {
            include "$cssFile";            
        }
        ?>
    </style>

    <link href="yardimcilar/css/bootstrap.min.css">

    <title><?php echo($TITLE); ?></title>
</head>
<body>
    <?php if (file_exists($database)) { 
        
    try {
        // weatherali.db dosyasına bağlan
        $pdo = new PDO("sqlite:$database");
        // Hata modunu etkinleştir (sadece geliştirme sırasında kullanın)
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        // Hata durumunda mesaj göster
        echo 'Veritabanı bağlantı hatası: ' . $e->getMessage();
        exit();
    }

    try {
        // Sorgu oluştur
        $sql = 'SELECT value FROM tahminiHavaDurumu';
        // Sorguyu hazırla
        $stmt = $pdo->prepare($sql);
        // Sorguyu çalıştır
        $stmt->execute();
        // Sonuçları al
        $values = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        // Hata durumunda mesaj göster
        echo 'Sorgu hatası: ' . $e->getMessage();
        exit();
    }
    ?>
    <div class="container mt-5">
        <h1 class="text-center"><?php print($SEHIR); ?> için <?php print($YIL); ?> yılın hava durumu tahminleri</h1>
        <table class="table table-dark table-striped">
            <tbody>
                <?php
                // Başlangıç tarihi
                $startDate = strtotime("$YIL-01-01");
                // Verileri tabloya ekle
                foreach ($values as $key => $value) {
                    // Tarihi hesapla
                    $date = date('Y-m-d', strtotime("+$key days", $startDate));
                    // Hava durumu sınıflandırması
                    $weatherCondition = '';
                    if ($value <= -1) {
                        $weatherCondition = 'Karlı veya Buzlu';
                    } elseif ($value <= 10) {
                        $weatherCondition = 'Sisli veya Yağmurlu';
                    } elseif ($value <= 16) {
                        $weatherCondition = 'Soğuk Hava';
                    } elseif ($value <= 24) {
                        $weatherCondition = 'Güneşli';
                    } elseif ($value <= 34) {
                        $weatherCondition = 'Sıcak Hava';
                    }

                    // HTML tablosuna ekle
                    echo '<tr>';
                    echo '<td class="text-center">' . htmlspecialchars($date) . '</td>';
                    echo '<td class="text-center">' . htmlspecialchars($weatherCondition) . '</td>';
                    echo '<td class="text-center">' . htmlspecialchars($value) . '°C</td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <?php } else { ?>
        <h1>NOT HOLA</h1>
    <?php } ?>

    <?php
    // JS dosyalarını include et
    foreach ($jsFiles as $jsFile) {
        echo('<script src="' . $jsFile . '"></script>');
    }
    ?>
</body>
</html>
