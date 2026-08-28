<?php
/**
 * Plesk Windows Laravel Temporary Directory & Tempnam Diagnostic Tool
 * Upload this file to public/diagnose.php and visit https://aadhyasilks.in/diagnose.php
 * DELETE THIS FILE AFTER DIAGNOSIS FOR SECURITY REASONS!
 */

header('Content-Type: text/html; charset=utf-8');

// Try to load Laravel framework version if available
$laravelVersion = 'Unknown (Bootstrap not loaded)';
$basePath = dirname(__DIR__);
if (file_exists($basePath . '/vendor/autoload.php')) {
    require $basePath . '/vendor/autoload.php';
    if (file_exists($basePath . '/bootstrap/app.php')) {
        $app = require_once $basePath . '/bootstrap/app.php';
        $laravelVersion = $app->version();
    }
}

$dirsToCheck = [
    'storage'                   => $basePath . DIRECTORY_SEPARATOR . 'storage',
    'storage/framework'         => $basePath . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'framework',
    'storage/framework/views'   => $basePath . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'views',
    'storage/framework/cache'   => $basePath . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'cache',
    'storage/framework/sessions'=> $basePath . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'sessions',
    'storage/logs'              => $basePath . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'logs',
    'bootstrap/cache'           => $basePath . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'cache',
    'sys_get_temp_dir()'        => sys_get_temp_dir(),
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Plesk Windows Laravel Diagnostic</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; margin: 30px; background: #f4f6f9; color: #333; }
        h1 { color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        th, td { padding: 12px 15px; text-align: left; border: 1px solid #e2e8f0; }
        th { background: #edf2f7; color: #2d3748; }
        .pass { background: #d4edda; color: #155724; font-weight: bold; }
        .fail { background: #f8d7da; color: #721c24; font-weight: bold; }
        .warn { background: #fff3cd; color: #856404; font-weight: bold; }
        .code { font-family: monospace; background: #eee; padding: 2px 6px; border-radius: 4px; }
    </style>
</head>
<body>

<h1>Laravel & Server Diagnostic Tool (Windows / Plesk)</h1>

<h2>Environment Configuration</h2>
<table>
    <tr><th>Property</th><th>Value</th></tr>
    <tr><td>PHP Version</td><td><span class="code"><?php echo PHP_VERSION; ?></span></td></tr>
    <tr><td>Laravel Version</td><td><span class="code"><?php echo htmlspecialchars($laravelVersion); ?></span></td></tr>
    <tr><td>PHP OS / SAPI</td><td><span class="code"><?php echo PHP_OS_FAMILY . ' (' . php_sapi_name() . ')'; ?></span></td></tr>
    <tr><td>sys_get_temp_dir()</td><td><span class="code"><?php echo sys_get_temp_dir(); ?></span></td></tr>
    <tr><td>ini_get('upload_tmp_dir')</td><td><span class="code"><?php echo ini_get('upload_tmp_dir') ?: '(Not set - default)'; ?></span></td></tr>
    <tr><td>ini_get('sys_temp_dir')</td><td><span class="code"><?php echo ini_get('sys_temp_dir') ?: '(Not set - default)'; ?></span></td></tr>
    <tr><td>getenv('TEMP')</td><td><span class="code"><?php echo getenv('TEMP') ?: '(Not set)'; ?></span></td></tr>
    <tr><td>getenv('TMP')</td><td><span class="code"><?php echo getenv('TMP') ?: '(Not set)'; ?></span></td></tr>
    <tr><td>open_basedir</td><td><span class="code"><?php echo ini_get('open_basedir') ?: '(Disabled)'; ?></span></td></tr>
    <tr><td>Process User (whoami)</td><td><span class="code"><?php echo function_exists('exec') ? @exec('whoami') ?: get_current_user() : get_current_user(); ?></span></td></tr>
</table>

<h2>Directory Permissions & tempnam() Tests</h2>
<table>
    <thead>
        <tr>
            <th>Directory / Identifier</th>
            <th>Path</th>
            <th>Exists</th>
            <th>Writable</th>
            <th>tempnam() Test Result</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($dirsToCheck as $label => $path): ?>
        <?php
        $exists = file_exists($path) && is_dir($path);
        $writable = $exists ? is_writable($path) : false;
        
        $tempnamNotice = null;
        $tempnamPath = false;
        
        if ($exists) {
            set_error_handler(function($errno, $errstr) use (&$tempnamNotice) {
                $tempnamNotice = $errstr;
            });
            
            $tempnamPath = @tempnam($path, 'test_');
            
            restore_error_handler();
            
            if ($tempnamPath && file_exists($tempnamPath)) {
                @unlink($tempnamPath);
            }
        }
        ?>
        <tr>
            <td><strong><?php echo htmlspecialchars($label); ?></strong></td>
            <td><span class="code"><?php echo htmlspecialchars($path); ?></span></td>
            <td class="<?php echo $exists ? 'pass' : 'fail'; ?>"><?php echo $exists ? 'YES' : 'NO'; ?></td>
            <td class="<?php echo $writable ? 'pass' : 'fail'; ?>"><?php echo $writable ? 'YES' : 'NO'; ?></td>
            <td>
                <?php if (!$exists): ?>
                    <span class="fail">FAILED (Directory does not exist)</span>
                <?php elseif ($tempnamNotice): ?>
                    <span class="fail">E_NOTICE: <?php echo htmlspecialchars($tempnamNotice); ?></span>
                <?php elseif ($tempnamPath !== false): ?>
                    <span class="pass">SUCCESS (<span class="code"><?php echo htmlspecialchars(basename($tempnamPath)); ?></span> created)</span>
                <?php else: ?>
                    <span class="fail">FAILED (tempnam returned false)</span>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<p><strong>Note:</strong> Delete <span class="code">public/diagnose.php</span> when testing is complete.</p>

</body>
</html>
