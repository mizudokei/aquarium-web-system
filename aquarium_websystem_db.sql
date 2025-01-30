-- データベース作成
CREATE DATABASE IF NOT EXISTS aquarium_websystem_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE aquarium_websystem_db;

-- 管理者テーブル
CREATE TABLE admins (
	id INT AUTO_INCREMENT PRIMARY KEY,
	email VARCHAR(100) NOT NULL UNIQUE,
	password VARCHAR(255) NOT NULL,
	role ENUM('superadmin', 'manager') DEFAULT 'manager',
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ユーザーテーブル
CREATE TABLE users (
	id INT AUTO_INCREMENT PRIMARY KEY,
	email VARCHAR(100) NOT NULL UNIQUE,
	password VARCHAR(255) NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 営業日管理テーブル
CREATE TABLE operating_days (
	id INT AUTO_INCREMENT PRIMARY KEY,
	date DATE NOT NULL UNIQUE,
	price_type ENUM('A', 'B', 'C', 'D') DEFAULT 'A',
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- チケット料金テーブル
CREATE TABLE admission_fee_types (
	id INT AUTO_INCREMENT PRIMARY KEY,
	type_name ENUM('adult', 'child', 'infant') NOT NULL,
	price_A INT NOT NULL,
	price_B INT NOT NULL,
	price_C INT NOT NULL,
	price_D INT NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- チケット予約テーブル
CREATE TABLE ticket_reservations (
	id INT AUTO_INCREMENT PRIMARY KEY,
	user_id INT NOT NULL,
	reservation_code VARCHAR(100) NOT NULL UNIQUE,
	reservation_date DATE NOT NULL,
	total_price INT NOT NULL,
	status ENUM('reserved', 'cancelled') DEFAULT 'reserved',
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- チケット分配テーブル
CREATE TABLE ticket_distribution (
	id INT AUTO_INCREMENT PRIMARY KEY,
	reservation_id INT NOT NULL,
	ticket_type_id INT NOT NULL,
	quantity INT NOT NULL,
	qr_code VARCHAR(255) NOT NULL UNIQUE,
	used_at TIMESTAMP NULL DEFAULT NULL,
	FOREIGN KEY (reservation_id) REFERENCES ticket_reservations(id) ON DELETE CASCADE,
	FOREIGN KEY (ticket_type_id) REFERENCES admission_fee_types(id) ON DELETE CASCADE
);

-- 入退場記録テーブル
CREATE TABLE visit_logs (
	id INT AUTO_INCREMENT PRIMARY KEY,
	qr_code VARCHAR(255) NOT NULL,
	status ENUM('entry', 'exit') NOT NULL,
	timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY (qr_code) REFERENCES ticket_distribution(qr_code) ON DELETE CASCADE
);

-- 初期データ挿入
-- チケット料金のデータ
INSERT INTO admission_fee_types (type_name, price_A, price_B, price_C, price_D) VALUES
('adult', 2700, 2900, 3200, 3500),
('child', 1400, 1500, 1650, 1800),
('infant', 700, 700, 800, 900);



-- 営業日テーブル
CREATE TABLE sales_days (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    is_operational BOOLEAN DEFAULT TRUE,  -- 営業日かどうか
    price_id INT DEFAULT 1,  -- デフォルトはprice_A
    working_hours VARCHAR(255) DEFAULT '09:00 - 17:00',  -- 営業時間
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 営業日と料金形態のリレーションテーブル
CREATE TABLE price_schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sales_day_id INT NOT NULL,  -- 営業日
    price_id INT NOT NULL,  -- 料金形態
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sales_day_id) REFERENCES sales_days(id),
    FOREIGN KEY (price_id) REFERENCES price_list(id)
);
