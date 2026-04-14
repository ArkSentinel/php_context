<?php
function h($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function old($key, $default = '') {
    return $_POST[$key] ?? $default;
}