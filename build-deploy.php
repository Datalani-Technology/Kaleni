#!/usr/bin/env php
<?php
/**
 * Builds a "drop into public_html" deploy package for shared hosting.
 * Run: php build-deploy.php
 *
 * Output: public_html_ready/ — upload its CONTENTS into your public_html folder.
 *
 * Before running:
 * 1. .env must have MySQL (DB_*) configured and working.
 * 2. Run: composer install --no-dev
 *
 * After build:
 * 1. Edit public_html_ready/.env with your hosting DB credentials (and MAIL_PASSWORD if needed).
 * 2. Create MySQL database on host, then import public_html_ready/database/dump.sql via phpMyAdmin.
 * 3. Upload everything inside public_html_ready/ to public_html (File Manager or FTP).
 * 4. Set permissions: storage and bootstrap/cache = 775 (writable).
 */

$root = __DIR__;
$out  = $root . DIRECTORY_SEPARATOR . 'public_html_ready';

$copyDirs  = ['app', 'bootstrap', 'config', 'database', 'resources', 'routes', 'storage', 'vendor'];
$copyFiles = ['artisan', 'composer.json', 'composer.lock'];

function ensureDir(string $path): void {
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }
}

function copyRecursive(string $src, string $dst): void {
    if (!is_dir($src)) return;
    ensureDir($dst);
    $d = dir($src);
    while (($e = $d->read()) !== false) {
        if ($e === '.' || $e === '..') continue;
        $s = $src . DIRECTORY_SEPARATOR . $e;
        $t = $dst . DIRECTORY_SEPARATOR . $e;
        if (is_dir($s)) {
            copyRecursive($s, $t);
        } else {
            copy($s, $t);
        }
    }
    $d->close();
}

function cleanDir(string $path): void {
    if (!is_dir($path)) return;
    $d = dir($path);
    while (($e = $d->read()) !== false) {
        if ($e === '.' || $e === '..') continue;
        $p = $path . DIRECTORY_SEPARATOR . $e;
        if (is_dir($p)) {
            cleanRecursive($p);
            rmdir($p);
        } else {
            unlink($p);
        }
    }
    $d->close();
}

function cleanRecursive(string $path): void {
    $d = dir($path);
    while (($e = $d->read()) !== false) {
        if ($e === '.' || $e === '..') continue;
        $p = $path . DIRECTORY_SEPARATOR . $e;
        if (is_dir($p)) {
            cleanRecursive($p);
            rmdir($p);
        } else {
            unlink($p);
        }
    }
    $d->close();
}

function parseEnv(string $path): array {
    $v = [];
    if (!is_file($path)) return $v;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) continue;
        if (preg_match('/^([A-Za-z_][A-Za-z0-9_]*)=(.*)$/', $line, $m)) {
            $v[$m[1]] = trim($m[2], " \t\"'");
        }
    }
    return $v;
}

echo "Build deploy package (drop into public_html)\n";
echo "Output: public_html_ready/\n\n";

if (!is_dir($root . DIRECTORY_SEPARATOR . 'vendor')) {
    echo "Running: composer install --no-dev --optimize-autoloader\n";
    passthru('composer install --no-dev --optimize-autoloader 2>&1', $c);
    if ($c !== 0) {
        echo "Failed. Run composer install --no-dev first.\n";
        exit(1);
    }
}

if (is_dir($out)) {
    echo "Cleaning existing output...\n";
    cleanRecursive($out);
} else {
    ensureDir($out);
}

echo "Copying project files...\n";
foreach ($copyDirs as $d) {
    $src = $root . DIRECTORY_SEPARATOR . $d;
    $dst = $out . DIRECTORY_SEPARATOR . $d;
    if (!is_dir($src)) {
        echo "  Skip (missing): $d\n";
        continue;
    }
    copyRecursive($src, $dst);
    echo "  $d\n";
}
foreach ($copyFiles as $f) {
    $src = $root . DIRECTORY_SEPARATOR . $f;
    $dst = $out . DIRECTORY_SEPARATOR . $f;
    if (file_exists($src)) {
        copy($src, $dst);
        echo "  $f\n";
    }
}

echo "Copying deploy overrides...\n";
copy($root . DIRECTORY_SEPARATOR . 'deploy' . DIRECTORY_SEPARATOR . 'index.php', $out . DIRECTORY_SEPARATOR . 'index.php');
copy($root . DIRECTORY_SEPARATOR . 'deploy' . DIRECTORY_SEPARATOR . '.htaccess', $out . DIRECTORY_SEPARATOR . '.htaccess');
copy($root . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'favicon.ico', $out . DIRECTORY_SEPARATOR . 'favicon.ico');
copy($root . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'robots.txt', $out . DIRECTORY_SEPARATOR . 'robots.txt');

echo "Creating uploads (products, logos, gallery, promotion)...\n";
$uploads = $out . DIRECTORY_SEPARATOR . 'uploads';
$upProd = $uploads . DIRECTORY_SEPARATOR . 'products';
$upLogo = $uploads . DIRECTORY_SEPARATOR . 'logos';
$upGallery = $uploads . DIRECTORY_SEPARATOR . 'gallery';
$upPromo = $uploads . DIRECTORY_SEPARATOR . 'promotion';
ensureDir($uploads);
ensureDir($upProd);
ensureDir($upLogo);
ensureDir($upGallery);
ensureDir($upPromo);
$srcPublic = $root . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public';
if (is_dir($srcPublic . DIRECTORY_SEPARATOR . 'products')) {
    copyRecursive($srcPublic . DIRECTORY_SEPARATOR . 'products', $upProd);
    echo "  Copied existing product images.\n";
}
if (is_dir($srcPublic . DIRECTORY_SEPARATOR . 'logos')) {
    copyRecursive($srcPublic . DIRECTORY_SEPARATOR . 'logos', $upLogo);
    echo "  Copied existing logo.\n";
}
if (is_dir($srcPublic . DIRECTORY_SEPARATOR . 'gallery')) {
    copyRecursive($srcPublic . DIRECTORY_SEPARATOR . 'gallery', $upGallery);
    echo "  Copied existing gallery images.\n";
}
if (is_dir($srcPublic . DIRECTORY_SEPARATOR . 'promotion')) {
    copyRecursive($srcPublic . DIRECTORY_SEPARATOR . 'promotion', $upPromo);
    echo "  Copied existing promotion PDF.\n";
}
file_put_contents($upProd . DIRECTORY_SEPARATOR . '.gitkeep', '');
file_put_contents($upLogo . DIRECTORY_SEPARATOR . '.gitkeep', '');
file_put_contents($upGallery . DIRECTORY_SEPARATOR . '.gitkeep', '');
file_put_contents($upPromo . DIRECTORY_SEPARATOR . '.gitkeep', '');

echo "Creating .env from template...\n";
$envTmpl = file_get_contents($root . DIRECTORY_SEPARATOR . 'deploy' . DIRECTORY_SEPARATOR . '.env.flat.example');
$envPath = $out . DIRECTORY_SEPARATOR . '.env';
$local   = parseEnv($root . DIRECTORY_SEPARATOR . '.env');
$deployDb = parseEnv($root . DIRECTORY_SEPARATOR . 'deploy-db.env');
$overrides = array_filter([
    'APP_KEY' => $local['APP_KEY'] ?? null,
    'DB_HOST' => $deployDb['DB_HOST'] ?? $local['DB_HOST'] ?? null,
    'DB_DATABASE' => $deployDb['DB_DATABASE'] ?? $local['DB_DATABASE'] ?? null,
    'DB_USERNAME' => $deployDb['DB_USERNAME'] ?? $local['DB_USERNAME'] ?? null,
    'DB_PASSWORD' => $deployDb['DB_PASSWORD'] ?? $local['DB_PASSWORD'] ?? null,
    'MAIL_PASSWORD' => $deployDb['MAIL_PASSWORD'] ?? $local['MAIL_PASSWORD'] ?? null,
], fn ($v) => $v !== null && $v !== '');

foreach ($overrides as $k => $v) {
    $envTmpl = preg_replace('/^' . preg_quote($k, '/') . '=.*$/m', $k . '=' . $v, $envTmpl);
}
if (!empty($deployDb)) {
    echo "  Using deploy-db.env for DB/MAIL overrides.\n";
}
file_put_contents($envPath, $envTmpl);

$hasKey = !empty($local['APP_KEY']) && preg_match('/^base64:.+/', $local['APP_KEY']);
if (!$hasKey) {
    echo "Generating APP_KEY...\n";
    $prev = getcwd();
    chdir($out);
    passthru('php artisan key:generate --force 2>&1', $code);
    chdir($prev);
    if ($code !== 0) {
        echo "  Warning: key:generate failed. Set APP_KEY in .env manually.\n";
    }
}

echo "Creating database dump (migrate + seed + export)...\n";
$prev = getcwd();
chdir($root);
passthru('php artisan migrate --force 2>&1', $m);
passthru('php artisan db:seed --force 2>&1', $s);
chdir($prev);

$db   = $local['DB_DATABASE'] ?? 'namsa_flora';
$host = $local['DB_HOST'] ?? '127.0.0.1';
$user = $local['DB_USERNAME'] ?? 'root';
$pass = $local['DB_PASSWORD'] ?? '';

$dump = $out . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'dump.sql';
$mysqldump = 'mysqldump';
if (PHP_OS_FAMILY === 'Windows' && file_exists('C:\\xampp\\mysql\\bin\\mysqldump.exe')) {
    $mysqldump = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
}
$redir = PHP_OS_FAMILY === 'Windows' ? ' 2>nul' : ' 2>/dev/null';
$cmd = sprintf('%s -h %s -u %s %s %s > %s%s', $mysqldump, escapeshellarg($host), escapeshellarg($user), $pass !== '' ? '-p' . escapeshellarg($pass) : '', escapeshellarg($db), escapeshellarg($dump), $redir);
exec($cmd, $outLines, $dumpCode);
if ($dumpCode !== 0 || !file_exists($dump) || filesize($dump) < 100) {
    if (file_exists($dump)) @unlink($dump);
    echo "  Warning: mysqldump failed. Export your DB via phpMyAdmin → Export → save as database/dump.sql in the package.\n";
} else {
    echo "  database/dump.sql created.\n";
}

file_put_contents($out . DIRECTORY_SEPARATOR . 'README_DEPLOY.txt', <<<TXT
DEPLOY TO PUBLIC_HTML (cPanel shared hosting)
============================================

.env is pre-filled for namsacomna258_namsa. Edit only if you use a different DB or mail.

1. IMPORT DUMP (you already created the DB)
   - cPanel → phpMyAdmin → open database namsacomna258_namsa
   - Import → Choose database/dump.sql from this folder → Go.

2. UPLOAD
   - Upload ALL files and folders inside this directory into public_html.
   - Do NOT upload this folder itself — upload its CONTENTS so that index.php
     and .htaccess are at the ROOT of public_html.

3. PERMISSIONS (File Manager or FTP)
   - storage → 775 (recursive)
   - bootstrap/cache → 775
   - uploads (and uploads/products, uploads/logos, uploads/gallery, uploads/promotion) → 775 (recursive)

4. DONE
   - Visit https://namsa.com.na
   - Admin: https://namsa.com.na/admin/login
     Email: admin@namsa.com.na
     Password: admin123
   - Change admin password after first login.

If you use existing product images, logo, gallery images, or promotion PDF, also upload
them into uploads/products, uploads/logos, uploads/gallery, uploads/promotion (same
structure as in your current storage/app/public).

5. CONTACT FORM EMAIL
   - Set MAIL_MAILER=smtp, MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD,
     MAIL_ENCRYPTION (tls/ssl), MAIL_FROM_ADDRESS, MAIL_FROM_NAME in .env.
   - CONTACT_EMAIL_INFO=info@namsa.com.na — main inbox for contact form.
   - CONTACT_EMAIL_ENQUIRIES=enquiries@namsa.com.na (optional) — also receives
     contact form. Both info and enquiries get the same email; deduped if same.
   - If neither is set, mail.from.address is used. Test the form and check
     storage/logs/laravel.log if emails do not arrive.
TXT
);

echo "\nDone. Output: public_html_ready/\n";
echo "Next: edit .env, import database/dump.sql, upload contents to public_html, set permissions.\n";
