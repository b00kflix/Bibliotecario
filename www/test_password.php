<?php
$senha = '12345678';
$hash = '$2y$10$v30lEb4EYp7nrbNo5.7wNeTKvZohLiO1UGOy0EXzDpEWWtYEqNd82';

if (password_verify($senha, $hash)) {
    echo "Senha correta!";
} else {
    echo "Senha incorreta!";
}
?>
