<?php
session_start();
include("conexao.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        header("Location: ../login.php?error=EMPTYFIELDS");
        exit();
    }

    $sql = "SELECT * FROM moura_games.tb_clientes WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        // Handle error in statement preparation
        die("ERRO NA PREPARAÇÃO DA CONSULTA: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row['PASSWORD'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['userid'] = $row['ID'];
            $_SESSION['username'] = $row['USERNAME'];
            $_SESSION['email'] = $row['EMAIL']; // Certifique-se de que o e-mail está disponível
            $_SESSION['online'] = true; // Defina o status de online aqui (geralmente gerenciado dinamicamente)
            header("Location: ../privado/priv.php.?login=SUCCESS");
        } else {
            header("Location: ../login.php?error=WRONGPASSWORD");
        }
    } else {
        header("Location: ../login.php?error=NOUSER");
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
} else {
    header("Location: ../login.php");
    exit();
}
?>

<?php
session_start();
include('php/conexao.php'); // Inclua o arquivo de conexão

// Verificar se o usuário está logado e obter as informações do usuário
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
    $userId = $_SESSION['userid'];
    $sql = "SELECT username, profile_picture FROM moura_games.tb_clientes WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        die("Erro na preparação da consulta: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $username, $profile_picture);
    mysqli_stmt_fetch($stmt);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
} else {
    $username = null;
    $profile_picture = 'default-avatar.png';
}
?>