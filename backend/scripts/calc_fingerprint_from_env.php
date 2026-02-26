<?php
// Safe fingerprint calculator
// Usage:
// 1) Set PUBLIC_SSH_KEY env var to the public key string (ssh-ed25519 AAAA...)
//    then run: php calc_fingerprint_from_env.php
// 2) Or pass path to a public key file: php calc_fingerprint_from_env.php /path/to/id_ed25519.pub

$input = getenv('PUBLIC_SSH_KEY') ?: ($argv[1] ?? null);
if (!$input) {
    fwrite(STDERR, "Usage: set PUBLIC_SSH_KEY env or pass path to public key file\n");
    exit(2);
}

if (is_file($input)) {
    $k = trim(file_get_contents($input));
} else {
    $k = trim($input);
}

if (empty($k)) {
    fwrite(STDERR, "No key provided\n");
    exit(3);
}

$parts = preg_split('/\s+/', $k, 3);
if (count($parts) < 2) {
    fwrite(STDERR, "Invalid public key format\n");
    exit(4);
}
list($type, $b) = $parts;

$raw = base64_decode($b, true);
if ($raw === false) {
    fwrite(STDERR, "Invalid base64 in key\n");
    exit(5);
}

$fp = base64_encode(hash('sha256', $raw, true));
$fp = rtrim($fp, '=');
echo 'SHA256:' . $fp . PHP_EOL;
exit(0);
