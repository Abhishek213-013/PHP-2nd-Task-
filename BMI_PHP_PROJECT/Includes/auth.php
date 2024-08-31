<?php
session_start();

function login($username, $password) {
    global $conn;
    $stmt = $conn->prepare("SELECT id, password FROM AppUsers WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            return true;
        }
    }
    return false;
}

function register($username, $password) {
    global $conn;
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO AppUsers (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $hashed_password);
    return $stmt->execute();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
?>
