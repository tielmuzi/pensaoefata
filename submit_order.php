<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pedidos";

// Ativa a exibição de erros (remover em produção)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Função para validar e limpar dados
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

try {
    // Criação da conexão
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificando a conexão
    if ($conn->connect_error) {
        throw new Exception("Falha na conexão: " . $conn->connect_error);
    }

    // Verifica se os dados foram enviados via POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Valida e limpa os dados recebidos
        $nome = cleanInput($_POST['name']);
        $telefone = cleanInput($_POST['phone']);
        $endereco = cleanInput($_POST['address']);
        $pagamento = cleanInput($_POST['payment']);
        $refeicao = isset($_POST['meal']) ? implode(", ", $_POST['meal']) : '';
        $bebida = cleanInput($_POST['drink']);
        $observacoes = cleanInput($_POST['observations']);

        // Verifica se os campos obrigatórios estão preenchidos
        if (empty($nome) || empty($telefone) || empty($endereco) || empty($pagamento) || empty($refeicao) || empty($bebida)) {
            throw new Exception("Por favor, preencha todos os campos obrigatórios!");
        }

        // Prepara e executa a query
        $stmt = $conn->prepare("INSERT INTO pedidos (nome, telefone, endereco, pagamento, refeicao, bebida, observacoes) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        if (!$stmt) {
            throw new Exception("Erro na preparação da query: " . $conn->error);
        }

        $stmt->bind_param("sssssss", $nome, $telefone, $endereco, $pagamento, $refeicao, $bebida, $observacoes);

        if ($stmt->execute()) {
            echo "
            <div style='background-color: #dff0d8; color: #3c763d; padding: 15px; margin: 20px; border-radius: 4px; text-align: center;'>
                <h2>Pedido realizado com sucesso!</h2>
                <p>Seu pedido foi registrado em nosso sistema.</p>
                <a href='realizar_pedido.html' style='display: inline-block; margin-top: 15px; padding: 10px 20px; background-color: #f4b30f; color: white; text-decoration: none; border-radius: 5px;'>Voltar</a>
            </div>";
        } else {
            throw new Exception("Erro ao inserir pedido: " . $stmt->error);
        }

        $stmt->close();
    } else {
        throw new Exception("Método de requisição inválido.");
    }
} catch (Exception $e) {
    echo "
    <div style='background-color: #f2dede; color: #a94442; padding: 15px; margin: 20px; border-radius: 4px; text-align: center;'>
        <h2>Erro!</h2>
        <p>" . $e->getMessage() . "</p>
        <a href='realizar_pedido.html' style='display: inline-block; margin-top: 15px; padding: 10px 20px; background-color: #f4b30f; color: white; text-decoration: none; border-radius: 5px;'>Voltar</a>
    </div>";
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>