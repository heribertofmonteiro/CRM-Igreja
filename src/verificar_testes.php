<?php
echo "=== Verificação de Testes ===\n";

// Testa sintaxe dos arquivos de teste
$arquivos = [
    'tests/ReuniaoTest.php',
    'tests/MinisterioTest.php', 
    'tests/MensagemTest.php'
];

foreach ($arquivos as $arquivo) {
    if (file_exists($arquivo)) {
        $saida = shell_exec("php -l $arquivo 2>&1");
        echo basename($arquivo) . ": " . (strpos($saida, 'No syntax errors') !== false ? "✓ OK" : "✗ ERRO") . "\n";
    } else {
        echo basename($arquivo) . ": ✗ NÃO ENCONTRADO\n";
    }
}

echo "\n=== Verificação concluída ===\n";