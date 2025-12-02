<?php
class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct() {
        // Gunakan environment variables untuk Docker, fallback ke default
        $this->host = getenv('DB_HOST') ? getenv('DB_HOST') : "localhost";
        $this->db_name = getenv('DB_NAME') ? getenv('DB_NAME') : "project_akhir";
        $this->username = getenv('DB_USER') ? getenv('DB_USER') : "root";
        $this->password = getenv('DB_PASSWORD') ? getenv('DB_PASSWORD') : "";
        
        // Fallback jika environment variable tidak ada (untuk development tanpa Docker)
        if (empty($this->host)) $this->host = "localhost";
        if (empty($this->db_name)) $this->db_name = "project_akhir";
        if (empty($this->username)) $this->username = "root";
        if (empty($this->password)) $this->password = "";
    }

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch(PDOException $exception) {
            // Log error untuk debugging
            error_log("Database connection error: " . $exception->getMessage());
            
            // Pesan error yang user-friendly
            if (getenv('APP_ENV') === 'development' || getenv('APP_DEBUG') === 'true') {
                echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px;'>
                        <h3>Database Connection Error</h3>
                        <p><strong>Message:</strong> " . htmlspecialchars($exception->getMessage()) . "</p>
                        <p><strong>Host:</strong> " . htmlspecialchars($this->host) . "</p>
                        <p><strong>Database:</strong> " . htmlspecialchars($this->db_name) . "</p>
                        <p>Please check your Docker configuration or database settings.</p>
                      </div>";
            } else {
                echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px;'>
                        <h3>Database Connection Error</h3>
                        <p>Unable to connect to the database. Please contact administrator.</p>
                      </div>";
            }
            exit();
        }
        return $this->conn;
    }

    // Method untuk mendapatkan info koneksi (untuk debugging)
    public function getConnectionInfo() {
        return [
            'host' => $this->host,
            'database' => $this->db_name,
            'username' => $this->username,
            'is_docker' => (getenv('DOCKER_ENV') === 'true' || getenv('DB_HOST') === 'db')
        ];
    }
}
?>