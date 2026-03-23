<?php

/**
 * Arquivo para testar a conexão com o banco de dados
 * Acesse este arquivo no navegador para verificar se a conexão está funcionando
 */

// Ativa exibição de erros para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Teste de Conexão com Banco de Dados</h2>";

// Configurações do banco de dados
$host = 'localhost';
$dbname = 'gti_bd';
$user = 'root';
$password = ''; // Ajuste aqui se necessário

echo "<h3>1. Testando conexão básica com MySQL...</h3>";
try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ <strong>SUCESSO:</strong> Conectado ao servidor MySQL!<br><br>";
} catch (PDOException $e) {
    echo "❌ <strong>ERRO:</strong> Não foi possível conectar ao MySQL<br>";
    echo "<strong>Mensagem:</strong> " . $e->getMessage() . "<br><br>";
    echo "<strong>Possíveis causas:</strong><br>";
    echo "- MySQL não está rodando<br>";
    echo "- Usuário ou senha incorretos<br>";
    echo "- Host incorreto<br><br>";
    exit;
}

echo "<h3>2. Verificando se o banco de dados 'gti_bd' existe...</h3>";
try {
    $stmt = $pdo->query("SHOW DATABASES LIKE 'gti_bd'");
    $dbExists = $stmt->fetch();

    if ($dbExists) {
        echo "✅ <strong>SUCESSO:</strong> Banco de dados 'gti_bd' existe!<br><br>";
    } else {
        echo "❌ <strong>ERRO:</strong> Banco de dados 'gti_bd' NÃO existe!<br>";
        echo "<strong>Solução:</strong> Execute o script SQL criar_tabela_usuarios.sql ou crie o banco manualmente:<br>";
        echo "<code>CREATE DATABASE gti CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;</code><br><br>";
    }
} catch (PDOException $e) {
    echo "❌ <strong>ERRO:</strong> " . $e->getMessage() . "<br><br>";
}

echo "<h3>3. Testando conexão com o banco de dados 'gti_bd'...</h3>";
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ <strong>SUCESSO:</strong> Conectado ao banco de dados 'gti_bd'!<br><br>";
} catch (PDOException $e) {
    echo "❌ <strong>ERRO:</strong> Não foi possível conectar ao banco 'gti_bd'<br>";
    echo "<strong>Mensagem:</strong> " . $e->getMessage() . "<br><br>";
    exit;
}

echo "<h3>4. Verificando se a tabela 'usuarios' existe...</h3>";
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'usuario'");
    $tableExists = $stmt->fetch();

    if ($tableExists) {
        echo "✅ <strong>SUCESSO:</strong> Tabela 'usuario' existe!<br><br>";

        echo "<h3>5. Verificando estrutura da tabela 'usuario'...</h3>";
        $stmt = $pdo->query("DESCRIBE usuario");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td>" . $column['Field'] . "</td>";
            echo "<td>" . $column['Type'] . "</td>";
            echo "<td>" . $column['Null'] . "</td>";
            echo "<td>" . $column['Key'] . "</td>";
            echo "<td>" . $column['Default'] . "</td>";
            echo "</tr>";
        }
        echo "</table><br>";

        echo "<h3>6. Testando INSERT na tabela (teste)...</h3>";
        try {
            $stmt = $pdo->prepare("INSERT INTO usuario (nome_completo, email, senha, data_cadastro) VALUES (:nome_completo, :email, :senha, NOW())");
            $testHash = password_hash('teste123', PASSWORD_DEFAULT);
            $result = $stmt->execute([
                'nome_completo' => 'Teste Conexão',
                'email' => 'teste@conexao.com',
                'senha' => $testHash
            ]);

            if ($result) {
                echo "✅ <strong>SUCESSO:</strong> INSERT funcionou!<br>";

                // Remove o registro de teste
                $pdo->prepare("DELETE FROM usuario WHERE email = 'teste@conexao.com'")->execute();
                echo "✅ Registro de teste removido.<br><br>";
            }
        } catch (PDOException $e) {
            echo "❌ <strong>ERRO no INSERT:</strong> " . $e->getMessage() . "<br>";
            echo "<strong>Possível causa:</strong> Estrutura da tabela não corresponde ao esperado.<br><br>";
        }

    } else {
        echo "❌ <strong>ERRO:</strong> Tabela 'usuario' NÃO existe!<br>";
        echo "<strong>Solução:</strong> Execute o script SQL criar_tabela_usuarios.sql<br><br>";
    }
} catch (PDOException $e) {
    echo "❌ <strong>ERRO:</strong> " . $e->getMessage() . "<br><br>";
}

echo "<h3>✅ Teste concluído!</h3>";
echo "<p>Se todos os testes passaram, a conexão está funcionando corretamente.</p>";