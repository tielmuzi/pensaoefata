<?php
session_start();

// Verifica se está logado
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: login.html");
    exit();
}

// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pedidos";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Função para marcar pedido como concluído
if (isset($_POST['concluir_pedido'])) {
    $id_pedido = $_POST['id_pedido'];
    $sql = "DELETE FROM pedidos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_pedido);
    $stmt->execute();
}

// Buscar pedidos
$sql = "SELECT * FROM pedidos ORDER BY data_pedido DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administração de Pedidos</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Anton:wght@400&display=swap">
    <style>
        body {
            margin: 0;
            font-family: 'Anton', sans-serif;
            background: #f5f5f5;
        }

        .header {
            background: #f4b30f;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logout-btn {
            background: #fff;
            color: #f4b30f;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .container {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .pedido {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .pedido-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .pedido-numero {
            font-size: 1.2em;
            color: #f4b30f;
        }

        .pedido-tempo {
            color: #666;
        }

        .pedido-info {
            margin-bottom: 15px;
        }

        .pedido-info p {
            margin: 5px 0;
        }

        .pedido-actions {
            display: flex;
            justify-content: flex-end;
        }

        .btn-concluir {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-concluir:hover {
            background: #218838;
        }

        @media (max-width: 768px) {
            .pedido-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .pedido-tempo {
                margin-top: 5px;
            }
        }

        .sem-pedidos {
            text-align: center;
            padding: 50px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Pedidos Ativos</h1>
        <a href="logout.php" class="logout-btn">Sair</a>
    </div>

    <div class="container">
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="pedido">
                    <div class="pedido-header">
                        <span class="pedido-numero">Pedido #<?php echo $row['id']; ?></span>
                        <span class="pedido-tempo"><?php echo date('d/m/Y H:i', strtotime($row['data_pedido'])); ?></span>
                    </div>
                    <div class="pedido-info">
                        <p><strong>Cliente:</strong> <?php echo htmlspecialchars($row['nome']); ?></p>
                        <p><strong>Telefone:</strong> <?php echo htmlspecialchars($row['telefone']); ?></p>
                        <p><strong>Endereço:</strong> <?php echo htmlspecialchars($row['endereco']); ?></p>
                        <p><strong>Pagamento:</strong> <?php echo htmlspecialchars($row['pagamento']); ?></p>
                        <p><strong>Refeição:</strong> <?php echo htmlspecialchars($row['refeicao']); ?></p>
                        <p><strong>Bebida:</strong> <?php echo htmlspecialchars($row['bebida']); ?></p>
                        <?php if (!empty($row['observacoes'])): ?>
                            <p><strong>Observações:</strong> <?php echo htmlspecialchars($row['observacoes']); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="pedido-actions">
                        <form method="POST">
                            <input type="hidden" name="id_pedido" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="concluir_pedido" class="btn-concluir">Marcar como Concluído</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="sem-pedidos">
                <h2>Nenhum pedido ativo no momento!</h2>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
<?php
$conn->close();
?>