<?php
function hashPassword($plainPassword) {
    return password_hash($plainPassword, PASSWORD_DEFAULT);
}

function verifyPassword($plainPassword, $hashedPassword) {
    return password_verify($plainPassword, $hashedPassword);
}
?>
