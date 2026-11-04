<?php
// Lớp cha của mọi Model: giữ 1 kết nối PDO dùng chung (Singleton) và hàm truy vấn chung.
class Model
{
    // Thuộc tính tĩnh giữ kết nối PDO duy nhất: static nghĩa là thuộc về lớp,
    protected static ?PDO $pdo = null;

    // Hàm khởi tạo: mỗi khi tạo một Model con (ví dụ new Place()), hàm này tự chạy và gọi getConnection() để chắc chắn đã có kết nối CSDL trước khi truy vấn.
    public function __construct()
    {
        self::getConnection();
    }

        // getConnection(): tạo 1 lần PDO từ DB_*, lỗi thì báo 500, không lộ chi tiết SQL.
    public static function getConnection(): PDO
    {
        if (self::$pdo === null) {
            // DSN: mysql, DB_HOST, DB_NAME, DB_CHARSET.
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                DB_HOST,
                DB_NAME,
                DB_CHARSET
            );

            // PDO: ERRMODE_EXCEPTION, FETCH_ASSOC, EMULATE_PREPARES=false dùng Prepared Statement thật chống SQL Injection.
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

        // query(): chạy SQL bằng Prepared Statement, tham số chỉ là dữ liệu, không thành code SQL.
    protected function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

        // fetchAll(): lấy nhiều dòng từ query().
    protected function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

        // fetchOne(): lấy 1 dòng, không có thì trả false.
    protected function fetchOne(string $sql, array $params = []): array|false
    {
        return $this->query($sql, $params)->fetch();
    }

        // fetchColumn(): lấy 1 giá trị (COUNT, AVG) để phân trang, tính điểm trung bình.
    protected function fetchColumn(string $sql, array $params = []): mixed
    {
        return $this->query($sql, $params)->fetchColumn();
    }

        // execute(): chạy INSERT/UPDATE/DELETE, trả số dòng bị ảnh hưởng.
    protected function execute(string $sql, array $params = []): int
    {
        return $this->query($sql, $params)->rowCount();
    }

        // lastInsertId(): lấy id vừa INSERT để chèn ảnh, tiện ích kèm theo.
    protected function lastInsertId(): string
    {
        return self::getConnection()->lastInsertId();
    }
}
