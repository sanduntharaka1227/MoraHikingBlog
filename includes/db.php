<?php
// Auto-detect environment (Localhost XAMPP vs Live InfinityFree)
$isLocal = in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1', '::1']) 
           || in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1'])
           || php_sapi_name() === 'cli';

if ($isLocal) {
    // Localhost XAMPP Credentials
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'mora_hiking_blog');
    define('DB_USER', 'root');
    define('DB_PASS', '');
} else {
    // Live InfinityFree Credentials
    define('DB_HOST', 'sql304.infinityfree.com');
    define('DB_NAME', 'if0_42692961_blog');
    define('DB_USER', 'if0_42692961');
    define('DB_PASS', 'pGhIvkFpJpuhw');
}
define('DB_CHARSET', 'utf8mb4');

/**

 *
 * @return PDO
 */
function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            //  database if it doesn't exist on localhost
            if ($e->getCode() == 1049) { 
                try {
                    $rootDsn = "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET;
                    $rootPdo = new PDO($rootDsn, DB_USER, DB_PASS, $options);
                    $rootPdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
                    
                    //  connect to the new DB
                    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
                    
                    // 
                    $schemaFile = __DIR__ . '/../sql/schema.sql';
                    if (file_exists($schemaFile)) {
                        $sql = file_get_contents($schemaFile);
                        $pdo->exec($sql);
                    }
                    return $pdo;
                } catch (PDOException $ex) {
                    die("<div style='font-family: sans-serif; padding: 2rem; background: #fff1f0; border: 1px solid #ffa39e; border-radius: 8px; max-width: 600px; margin: 3rem auto; color: #cf1322;'>
                        <h3>Database Setup Error</h3>
                        <p>Could not connect to MySQL server. Please ensure MySQL is running in XAMPP Control Panel.</p>
                        <small>Error: " . htmlspecialchars($ex->getMessage()) . "</small>
                    </div>");
                }
            }

            die("<div style='font-family: sans-serif; padding: 2rem; background: #fff1f0; border: 1px solid #ffa39e; border-radius: 8px; max-width: 600px; margin: 3rem auto; color: #cf1322;'>
                <h3>Database Connection Failed</h3>
                <p>Please make sure the MySQL module is started in your XAMPP control panel and database <strong>" . DB_NAME . "</strong> is imported.</p>
                <small>Error details: " . htmlspecialchars($e->getMessage()) . "</small>
            </div>");
        }
    }
    return $pdo;
}
