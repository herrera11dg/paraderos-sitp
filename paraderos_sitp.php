<?php
$localidades = [
    "Usaquén", "Chapinero", "Santa Fe", "San Cristóbal", "Usme",
    "Tunjuelito", "Bosa", "Kennedy", "Fontibón", "Engativá",
    "Suba", "Barrios Unidos", "Teusaquillo", "Los Mártires", "Antonio Nariño",
    "Puente Aranda", "La Candelaria", "Rafael Uribe Uribe", "Ciudad Bolívar", "Sumapaz",
];

$localidadSel = isset($_GET['localidad']) ? $_GET['localidad'] : '';
$total = null;
$error = '';

if ($localidadSel && in_array($localidadSel, $localidades)) {
    $nombrePy = $localidadSel;
    $nombrePy = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'n'], $nombrePy);

    $script = __DIR__ . '/paraderos.py';
    $comando = 'python "' . $script . '" "' . $nombrePy . '" 2>&1';
    $salida = shell_exec($comando);

    if ($salida === null) {
        $error = "No se pudo ejecutar el script de Python. Verifique que Python este instalado.";
    } else {
        $datos = json_decode(trim($salida), true);
        if ($datos === null) {
            $error = "Error al procesar la respuesta de Python.";
        } elseif (isset($datos['error'])) {
            $error = $datos['error'];
        } else {
            $total = $datos['total'];
        }
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
                    <?php foreach ($localidades as $nombre): ?>
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

        <?php if ($total !== null): ?>
            <div class="result">
                <div class="localidad"><?= htmlspecialchars($localidadSel) ?></div>
                <div class="number"><?= $total ?></div>
                <div class="label">paraderos encontrados</div>
            </div>
        <?php elseif (!$localidadSel && !$error): ?>
            <div class="alert alert-info">Seleccione una localidad y presione "Buscar Paraderos".</div>
        <?php endif; ?>
    </div>
</body>
</html>
