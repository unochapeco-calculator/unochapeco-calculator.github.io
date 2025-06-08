<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
function calcularMedia($data) {
    $avaliacoes = [];
    $quantidade = 0;
    $soma = 0.0;
    $totalPesos = 0.0;

    $pares = explode('&', $data);

    $tipos = [];
    $notas = [];
    $pesos = [];

    foreach ($pares as $par) {
        if (preg_match('/tipo(\d+)=(.*)/', $par, $m)) {
            $idx = intval($m[1]);
            $tipos[$idx] = urldecode($m[2]);
        } elseif (preg_match('/nota(\d+)=(.*)/', $par, $m)) {
            $idx = intval($m[1]);
            $notas[$idx] = floatval($m[2]);
        } elseif (preg_match('/peso(\d+)=(.*)/', $par, $m)) {
            $idx = intval($m[1]);
            $pesos[$idx] = floatval($m[2]);
        }
    }

    $quantidade = max(array_keys($tipos)) + 1;

    for ($i = 0; $i < $quantidade; $i++) {
        if (!isset($notas[$i]) || !isset($pesos[$i])) {
            continue;
        }
        $peso = $pesos[$i] / 100.0;
        $soma += $notas[$i] * $peso;
        $totalPesos += $peso;
    }

    if ($totalPesos == 0) {
        return -1.0;
    }

    return $soma;
}

$post_data = file_get_contents('php://input');

$media = calcularMedia($post_data);

header("Content-Type: text/html; charset=utf-8");
?>
<html>
<body>
<?php if ($media < 0): ?>
    <p>Erro: pesos inválidos.</p>
<?php else: ?>
    <p>Média final: <?= number_format($media, 2) ?></p>
<?php endif; ?>
</body>
</html>
