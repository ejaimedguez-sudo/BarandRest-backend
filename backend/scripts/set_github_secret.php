<?php

// Usage: php set_github_secret.php owner repo secret_name secret_file
// Reads GITHUB_TOKEN from environment.
if ($argc < 5) {
    fwrite(STDERR, "Usage: php set_github_secret.php owner repo secret_name secret_file\n");
    exit(1);
}
$owner = $argv[1];
$repo = $argv[2];
$secretName = $argv[3];
$secretFile = $argv[4];
if (! file_exists($secretFile)) {
    fwrite(STDERR, "Secret file not found: $secretFile\n");
    exit(2);
}
$secretValue = file_get_contents($secretFile);
$pat = getenv('GITHUB_TOKEN');
if (! $pat) {
    fwrite(STDERR, "GITHUB_TOKEN environment variable not set.\n");
    exit(3);
}
$headers = [
    "Authorization: token $pat",
    'User-Agent: set-secret-script',
    'Accept: application/vnd.github.v3+json',
];
$url = "https://api.github.com/repos/$owner/$repo/actions/secrets/public-key";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$res = curl_exec($ch);
if ($res === false) {
    fwrite(STDERR, 'Failed to get public key: '.curl_error($ch)."\n");
    exit(4);
}
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
if ($code !== 200) {
    fwrite(STDERR, "GitHub API returned HTTP $code when fetching public key\n");
    fwrite(STDERR, $res."\n");
    exit(5);
}
$obj = json_decode($res, true);
$key = $obj['key'] ?? null;
$keyId = $obj['key_id'] ?? ($obj['id'] ?? null);
if (! $key || ! $keyId) {
    fwrite(STDERR, "Invalid public key response\n");
    exit(6);
}

// Encrypt using libsodium sealed box
if (! function_exists('sodium_crypto_box_seal')) {
    fwrite(STDERR, "libsodium sealed box function not available in PHP.\n");
    exit(7);
}
$publicKey = base64_decode($key);
$cipher = sodium_crypto_box_seal($secretValue, $publicKey);
$encrypted = base64_encode($cipher);

$putUrl = "https://api.github.com/repos/$owner/$repo/actions/secrets/".rawurlencode($secretName);
$body = json_encode(['encrypted_value' => $encrypted, 'key_id' => $keyId]);
$ch = curl_init($putUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge($headers, ['Content-Type: application/json']));
$res2 = curl_exec($ch);
if ($res2 === false) {
    fwrite(STDERR, 'Failed to PUT secret: '.curl_error($ch)."\n");
    exit(8);
}
$code2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
if ($code2 === 201 || $code2 === 204) {
    fwrite(STDOUT, "Secret $secretName set successfully.\n");
    exit(0);
} else {
    fwrite(STDERR, "Failed to set secret, HTTP $code2\n");
    fwrite(STDERR, $res2."\n");
    exit(9);
}
