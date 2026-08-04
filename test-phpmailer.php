<?php

/**
 * PHPMailer SMTP Test Script
 * 
 * This script tests SMTP using PHPMailer library
 * Reads configuration from .env file
 * 
 * Usage: php test-phpmailer.php [test-email@example.com]
 * 
 * Requirements:
 * - PHPMailer library (install via: composer require phpmailer/phpmailer)
 * - Or ensure PHPMailer is available on your server
 */

// ============================================
// Load .env file
// ============================================
function loadEnv($filePath) {
    if (!file_exists($filePath)) {
        return [];
    }
    
    $env = [];
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Parse KEY=VALUE
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remove quotes if present
            if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
                (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
                $value = substr($value, 1, -1);
            }
            
            $env[$key] = $value;
        }
    }
    
    return $env;
}

// ============================================
// Load PHPMailer
// ============================================
$phpmailerLoaded = false;

// Try to load via Composer autoload
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
    $phpmailerLoaded = true;
}

// Try to load PHPMailer directly if not loaded
if (!$phpmailerLoaded) {
    // Try PHPMailer namespace
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        $phpmailerLoaded = true;
    } 
    // Try old PHPMailer class
    elseif (class_exists('PHPMailer')) {
        $phpmailerLoaded = true;
    }
    // Try to include PHPMailer manually (common paths)
    else {
        $phpmailerPaths = [
            __DIR__ . '/vendor/phpmailer/phpmailer/src/PHPMailer.php',
            __DIR__ . '/PHPMailer/PHPMailer.php',
            '/usr/share/php/PHPMailer/PHPMailer.php',
        ];
        
        foreach ($phpmailerPaths as $path) {
            if (file_exists($path)) {
                require_once $path;
                require_once dirname($path) . '/SMTP.php';
                require_once dirname($path) . '/Exception.php';
                $phpmailerLoaded = true;
                break;
            }
        }
    }
}

if (!$phpmailerLoaded) {
    echo "\n❌ ERROR: PHPMailer library not found!\n\n";
    echo "Please install PHPMailer using one of these methods:\n\n";
    echo "1. Via Composer (recommended):\n";
    echo "   composer require phpmailer/phpmailer\n\n";
    echo "2. Or download from: https://github.com/PHPMailer/PHPMailer\n";
    echo "   and place it in the project directory\n\n";
    exit(1);
}

// ============================================
// Load .env configuration
// ============================================
$envFile = __DIR__ . '/.env';
$env = loadEnv($envFile);

if (empty($env)) {
    echo "❌ ERROR: .env file not found or empty!\n";
    echo "   Looking for: $envFile\n\n";
    exit(1);
}

// Get test email from command line
$testEmail = isset($argv[1]) ? $argv[1] : null;

// ============================================
// SMTP Configuration from .env
// ============================================
$smtp_config = [
    'host' => $env['MAIL_HOST'] ?? 'smtppro.zoho.in',
    'port' => (int)($env['MAIL_PORT'] ?? 587),
    'encryption' => strtolower($env['MAIL_ENCRYPTION'] ?? 'tls'),
    'username' => $env['MAIL_USERNAME'] ?? '',
    'password' => $env['MAIL_PASSWORD'] ?? '',
    'from_email' => $env['MAIL_FROM_ADDRESS'] ?? $env['MAIL_USERNAME'] ?? 'noreply@ircc.world',
    'from_name' => $env['MAIL_FROM_NAME'] ?? 'IRCC World',
    'to_email' => $testEmail ?: ($env['MAIL_FROM_ADDRESS'] ?? 'test@example.com'),
];

// Validate required settings
if (empty($smtp_config['username']) || empty($smtp_config['password'])) {
    echo "❌ ERROR: MAIL_USERNAME or MAIL_PASSWORD not set in .env file!\n\n";
    exit(1);
}

if (!$testEmail && $smtp_config['to_email'] === 'test@example.com') {
    echo "⚠️  WARNING: No test email provided. Using default.\n";
    echo "   Usage: php test-phpmailer.php your-email@example.com\n\n";
}

// ============================================
// Test Email with PHPMailer
// ============================================

echo "\n";
echo "═══════════════════════════════════════════════════════════\n";
echo "  PHPMailer SMTP Test Script\n";
echo "═══════════════════════════════════════════════════════════\n";
echo "\n";

// Determine PHPMailer class name
$phpmailerClass = null;
if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    $phpmailerClass = 'PHPMailer\PHPMailer\PHPMailer';
} elseif (class_exists('PHPMailer')) {
    $phpmailerClass = 'PHPMailer';
} else {
    echo "❌ ERROR: PHPMailer class not found after loading!\n\n";
    exit(1);
}

echo "📦 PHPMailer Version: ";
if (defined($phpmailerClass . '::VERSION')) {
    echo constant($phpmailerClass . '::VERSION') . "\n";
} else {
    echo "Unknown\n";
}
echo "\n";

// Display configuration
echo "📧 SMTP Configuration:\n";
echo "   Host:       {$smtp_config['host']}\n";
echo "   Port:       {$smtp_config['port']}\n";
echo "   Encryption: {$smtp_config['encryption']}\n";
echo "   Username:   {$smtp_config['username']}\n";
echo "   From:       {$smtp_config['from_email']} ({$smtp_config['from_name']})\n";
echo "   To:         {$smtp_config['to_email']}\n";
echo "\n";

// Test connection first
echo "🔌 Testing SMTP Connection...\n";
$connection = @fsockopen($smtp_config['host'], $smtp_config['port'], $errno, $errstr, 10);

if ($connection) {
    echo "   ✅ Connection successful to {$smtp_config['host']}:{$smtp_config['port']}\n";
    fclose($connection);
} else {
    echo "   ⚠️  Connection test failed: $errstr ($errno)\n";
    echo "   Continuing anyway...\n";
}
echo "\n";

// Create PHPMailer instance
try {
    $mail = new $phpmailerClass(true);
    
    // Enable verbose debug output (level 2 = client and server messages)
    $mail->SMTPDebug = 2;
    $mail->Debugoutput = function($str, $level) {
        // Only show important messages, filter out verbose output
        if (strpos($str, 'SERVER -> CLIENT') !== false || 
            strpos($str, 'CLIENT -> SERVER') !== false ||
            strpos($str, 'SMTP') !== false ||
            $level > 1) {
            echo "   [SMTP] " . trim($str) . "\n";
        }
    };
    
    // Server settings
    $mail->isSMTP();
    $mail->Host = $smtp_config['host'];
    $mail->SMTPAuth = true;
    $mail->Username = $smtp_config['username'];
    $mail->Password = $smtp_config['password'];
    $mail->SMTPSecure = $smtp_config['encryption']; // 'tls' or 'ssl'
    $mail->Port = $smtp_config['port'];
    
    // Optional: Set timeout
    $mail->Timeout = 30;
    
    // Recipients
    $mail->setFrom($smtp_config['from_email'], $smtp_config['from_name']);
    $mail->addAddress($smtp_config['to_email']);
    
    // Optional: Add reply-to
    $mail->addReplyTo($smtp_config['from_email'], $smtp_config['from_name']);
    
    // Content
    $mail->isHTML(false);
    $mail->Subject = 'IRCC World - SMTP Test (' . date('Y-m-d H:i:s') . ')';
    
    $mail->Body = "This is a test email from IRCC World SMTP test script.\n\n";
    $mail->Body .= "If you receive this email, your SMTP configuration is working correctly!\n\n";
    $mail->Body .= "Test Details:\n";
    $mail->Body .= "- Server: " . gethostname() . "\n";
    $mail->Body .= "- Time: " . date('Y-m-d H:i:s') . "\n";
    $mail->Body .= "- PHP Version: " . PHP_VERSION . "\n";
    $mail->Body .= "- SMTP Host: {$smtp_config['host']}\n";
    $mail->Body .= "- SMTP Port: {$smtp_config['port']}\n";
    $mail->Body .= "- Encryption: {$smtp_config['encryption']}\n\n";
    $mail->Body .= "This is an automated test. Please ignore if you did not request this test.\n";
    
    // AltBody for non-HTML email clients
    $mail->AltBody = $mail->Body;
    
    echo "📤 Sending email...\n";
    echo "   Please wait...\n\n";
    
    $start = microtime(true);
    
    // Send email
    $mail->send();
    
    $duration = round((microtime(true) - $start) * 1000, 2);
    
    echo "\n";
    echo "═══════════════════════════════════════════════════════════\n";
    echo "✅ SUCCESS!\n";
    echo "═══════════════════════════════════════════════════════════\n";
    echo "\n";
    echo "Email sent successfully!\n";
    echo "   Duration: {$duration}ms\n";
    echo "   Recipient: {$smtp_config['to_email']}\n\n";
    
    echo "📋 Next Steps:\n";
    echo "   1. Check your inbox: {$smtp_config['to_email']}\n";
    echo "   2. Check spam/junk folder if not in inbox\n";
    echo "   3. If email received → SMTP is working correctly! ✓\n";
    echo "   4. If not received → Check error messages above\n\n";
    
} catch (Exception $e) {
    echo "\n";
    echo "═══════════════════════════════════════════════════════════\n";
    echo "❌ ERROR: Failed to send email\n";
    echo "═══════════════════════════════════════════════════════════\n";
    echo "\n";
    
    echo "Error Message:\n";
    echo "   {$e->getMessage()}\n\n";
    
    echo "Error Details:\n";
    echo "   File: " . basename($e->getFile()) . "\n";
    echo "   Line: " . $e->getLine() . "\n\n";
    
    echo "🔧 Troubleshooting:\n";
    echo "   1. Verify SMTP credentials in .env file\n";
    echo "   2. Check if port {$smtp_config['port']} is open\n";
    echo "   3. Try different encryption (tls/ssl)\n";
    echo "   4. Check firewall settings\n";
    echo "   5. Verify SMTP host: {$smtp_config['host']}\n";
    echo "   6. Review SMTP debug output above\n\n";
    
    exit(1);
}

echo "═══════════════════════════════════════════════════════════\n";
echo "✅ Test completed successfully!\n";
echo "═══════════════════════════════════════════════════════════\n\n";

