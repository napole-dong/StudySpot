<?php
/**
 * Lớp Model cơ sở: Quản lý kết nối PDO (Singleton) và các phương thức truy vấn dùng chung
 */
class Model
{
    protected static ?PDO $pdo = null;

    public function __construct()
    {
        self::getConnection();
    }

    /**
     * Khởi tạo và trả về kết nối PDO duy nhất trong suốt vòng đời request
     */
    public static function getConnection(): PDO
    {
        if (self::$pdo === null) {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                DB_HOST,
                DB_NAME,
                DB_CHARSET
            );

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                // Không hiển thị chi tiết lỗi SQL nhạy cảm ra giao diện người dùng
                http_response_code(500);
                die('<div style="font-family:sans-serif;padding:40px;text-align:center;">
                        <h2>Không thể kết nối Cơ sở dữ liệu</h2>
                        <p>Vui lòng kiểm tra MySQL trong XAMPP và đảm bảo đã tạo CSDL <strong>studyspot</strong>.</p>
                    </div>');
            }
        }

        return self::$pdo;
    }

    /**
     * Thực thi câu lệnh SQL với Prepared Statement
     */
    protected function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Lấy nhiều dòng dữ liệu
     */
    protected function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    /**
     * Lấy một dòng dữ liệu
     */
    protected function fetchOne(string $sql, array $params = []): array|false
    {
        return $this->query($sql, $params)->fetch();
    }

    /**
     * Lấy giá trị của một cột đơn lẻ (ví dụ COUNT(*), AVG(...))
     */
    protected function fetchColumn(string $sql, array $params = []): mixed
    {
        return $this->query($sql, $params)->fetchColumn();
    }

    /**
     * Thực thi INSERT / UPDATE / DELETE và trả về số dòng bị ảnh hưởng
     */
    protected function execute(string $sql, array $params = []): int
    {
        return $this->query($sql, $params)->rowCount();
    }

    /**
     * Lấy ID vừa được INSERT gần nhất
     */
    protected function lastInsertId(): string
    {
        return self::getConnection()->lastInsertId();
    }
}
