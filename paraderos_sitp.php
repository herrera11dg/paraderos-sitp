<?php
$baseUrl = "https://transport.opendatasoft.com/api/explore/v2.1/catalog/datasets/paraderos-sitp/records";
$pageLimit = 100;
$maxPages = 50;

$localidades = [
    "Usaquén" => ["lat_min" => 4.72, "lat_max" => 4.82, "lon_min" => -74.07, "lon_max" => -73.98],
    "Chapinero" => ["lat_min" => 4.63, "lat_max" => 4.72, "lon_min" => -74.07, "lon_max" => -74.03],
    "Santa Fe" => ["lat_min" => 4.59, "lat_max" => 4.63, "lon_min" => -74.08, "lon_max" => -74.04],
    "San Cristóbal" => ["lat_min" => 4.55, "lat_max" => 4.60, "lon_min" => -74.10, "lon_max" => -74.05],
    "Usme" => ["lat_min" => 4.45, "lat_max" => 4.56, "lon_min" => -74.15, "lon_max" => -74.08],
    "Tunjuelito" => ["lat_min" => 4.54, "lat_max" => 4.59, "lon_min" => -74.15, "lon_max" => -74.10],
    "Bosa" => ["lat_min" => 4.56, "lat_max" => 4.63, "lon_min" => -74.18, "lon_max" => -74.13],
    "Kennedy" => ["lat_min" => 4.59, "lat_max" => 4.66, "lon_min" => -74.18, "lon_max" => -74.10],
    "Fontibón" => ["lat_min" => 4.63, "lat_max" => 4.70, "lon_min" => -74.20, "lon_max" => -74.13],
    "Engativá" => ["lat_min" => 4.67, "lat_max" => 4.76, "lon_min" => -74.16, "lon_max" => -74.08],
    "Suba" => ["lat_min" => 4.70, "lat_max" => 4.80, "lon_min" => -74.13, "lon_max" => -74.03],
    "Barrios Unidos" => ["lat_min" => 4.65, "lat_max" => 4.71, "lon_min" => -74.08, "lon_max" => -74.04],
    "Teusaquillo" => ["lat_min" => 4.62, "lat_max" => 4.67, "lon_min" => -74.08, "lon_max" => -74.05],
    "Los Mártires" => ["lat_min" => 4.59, "lat_max" => 4.63, "lon_min" => -74.10, "lon_max" => -74.07],
    "Antonio Nariño" => ["lat_min" => 4.56, "lat_max" => 4.60, "lon_min" => -74.12, "lon_max" => -74.08],
    "Puente Aranda" => ["lat_min" => 4.60, "lat_max" => 4.65, "lon_min" => -74.13, "lon_max" => -74.09],
    "La Candelaria" => ["lat_min" => 4.59, "lat_max" => 4.61, "lon_min" => -74.08, "lon_max" => -74.06],
    "Rafael Uribe Uribe" => ["lat_min" => 4.51, "lat_max" => 4.57, "lon_min" => -74.12, "lon_max" => -74.07],
    "Ciudad Bolívar" => ["lat_min" => 4.44, "lat_max" => 4.53, "lon_min" => -74.17, "lon_max" => -74.10],
    "Sumapaz" => ["lat_min" => 4.30, "lat_max" => 4.45, "lon_min" => -74.30, "lon_max" => -74.15],
];

$localidadSel = isset($_GET['localidad']) ? $_GET['localidad'] : '';

$total = 0;
$error = '';

if ($localidadSel && isset($localidades[$localidadSel])) {
    $bounds = $localidades[$localidadSel];
    $offset = 0;
    $page = 0;

    try {
        while ($page < $maxPages) {
            $url = $baseUrl . "?limit=" . $pageLimit . "&offset=" . $offset . "&select=geopoint";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                $error = "Error al conectar con la API: " . curl_error($ch);
                curl_close($ch);
                break;
            }

            $data = json_decode($response, true);
            curl_close($ch);

            if (!isset($data['results']) || empty($data['results'])) {
                break;
            }

            foreach ($data['results'] as $item) {
                $lat = $item['geopoint']['lat'] ?? null;
                $lon = $item['geopoint']['lon'] ?? null;

                if ($lat !== null && $lon !== null) {
                    if ($lat >= $bounds['lat_min'] && $lat <= $bounds['lat_max'] &&
                        $lon >= $bounds['lon_min'] && $lon <= $bounds['lon_max']) {
                        $total++;
                    }
                }
            }

            $offset += $pageLimit;
            $page++;
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paraderos SITP por Localidad - Bogotá</title>
    <style>
        * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background: #f5f7fa; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 30px; }
        h1 { color: #1a5276; text-align: center; margin-bottom: 10px; }
        .subtitle { text-align: center; color: #5d6d7e; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50; }
        select { width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 6px; font-size: 16px; background: white; transition: border-color 0.3s; }
        select:focus { outline: none; border-color: #3498db; }
        .btn { background: #3498db; color: white; padding: 12px 30px; border: none; border-radius: 6px; font-size: 16px; cursor: pointer; transition: background 0.3s; width: 100%; }
        .btn:hover { background: #2980b9; }
        .alert { padding: 15px; border-radius: 6px; margin-bottom: 20px; }
        .alert-error { background: #fadbd8; color: #c0392b; border: 1px solid #f5b7b1; }
        .alert-info { background: #d6eaf8; color: #2980b9; border: 1px solid #aed6f1; }
        .result { text-align: center; padding: 30px 20px; }
        .result .number { font-size: 64px; font-weight: 700; color: #1a5276; }
        .result .label { font-size: 18px; color: #5d6d7e; margin-top: 5px; }
        .result .localidad { font-size: 20px; color: #2c3e50; font-weight: 600; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Paraderos del SITP</h1>
        <p class="subtitle">Consulta la cantidad de paraderos por localidad en Bogotá</p>

        <form method="GET" action="">
            <div class="form-group">
                <label for="localidad">Seleccionar Localidad:</label>
                <select name="localidad" id="localidad" required>
                    <option value="">-- Seleccione una localidad --</option>
                    <?php foreach ($localidades as $nombre => $bounds): ?>
                        <option value="<?= htmlspecialchars($nombre) ?>" <?= $localidadSel === $nombre ? 'selected' : '' ?>>
                            <?= htmlspecialchars($nombre) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn">Buscar Paraderos</button>
        </form>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($localidadSel && !$error): ?>
            <div class="result">
                <div class="localidad"><?= htmlspecialchars($localidadSel) ?></div>
                <div class="number"><?= $total ?></div>
                <div class="label">paraderos encontrados</div>
            </div>
        <?php elseif (!$localidadSel): ?>
            <div class="alert alert-info">Seleccione una localidad y presione "Buscar Paraderos".</div>
        <?php endif; ?>
    </div>
</body>
</html>
