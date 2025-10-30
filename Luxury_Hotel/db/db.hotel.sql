-- Buat database
CREATE DATABASE IF NOT EXISTS hotel_db DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE hotel_db;


-- tabel users
CREATE TABLE users (
id INT AUTO_INCREMENT PRIMARY KEY,
username VARCHAR(50) NOT NULL UNIQUE,
password VARCHAR(255) NOT NULL,
email VARCHAR(100) NOT NULL UNIQUE,
role ENUM('admin','user') NOT NULL DEFAULT 'user',
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


-- tabel rooms
CREATE TABLE rooms (
id INT AUTO_INCREMENT PRIMARY KEY,
room_number VARCHAR(20) NOT NULL UNIQUE,
type VARCHAR(50) NOT NULL,
price DECIMAL(10,2) NOT NULL,
description TEXT,
image VARCHAR(255),
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


-- tabel bookings
CREATE TABLE bookings (
id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT NOT NULL,
room_id INT NOT NULL,
check_in DATE NOT NULL,
check_out DATE NOT NULL,
status ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
) ENGINE=InnoDB;


-- data sample: admin
INSERT INTO users (username, password, email, role) VALUES
('admin', '12345', 'admin@hotel.local', 'admin');
-- password contoh: Admin@123 (hash di atas — pastikan mengganti di produksi)
UPDATE users 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' 
WHERE username = 'admin';
INSERT INTO users (username, password, email, role) VALUES
('john_doe', '12345', 'john@example.com', 'user'),
('jane_smith', '12345', 'jane@example.com', 'user'),
('michael_brown', '12345', 'michael@example.com', 'user'),
('emily_white', '12345', 'emily@example.com', 'user'),
('david_jones', '12345', 'david@example.com', 'user');
-- data sample: rooms
INSERT INTO rooms (room_number, type, price, description, image) VALUES
('103', 'Standard', 100.00, 'Standard room with all basic amenities for a comfortable stay.', 'assets/img/room3.jpg'),
('104', 'Suite', 250.00, 'Spacious suite with separate living area and luxury bathroom.', 'assets/img/room4.jpg'),
('105', 'Executive', 300.00, 'Executive room with workspace, large TV, and complimentary breakfast.', 'assets/img/room5.jpg'),
('106', 'Presidential', 550.00, 'Presidential suite offering panoramic city views and premium facilities.', 'assets/img/room6.jpg'),
('107', 'Family', 220.00, 'Family room with 2 double beds and kids-friendly amenities.', 'assets/img/room7.jpg'),
('108', 'Deluxe', 160.00, 'Deluxe room with balcony and ocean view.', 'assets/img/room8.jpg'),
('109', 'Superior', 125.00, 'Superior room with modern lighting and elegant design.', 'assets/img/room9.jpg'),
('110', 'Standard', 95.00, 'Compact standard room perfect for solo travelers.', 'assets/img/room10.jpg'),
('111', 'Suite', 270.00, 'Luxury suite with jacuzzi and private dining space.', 'assets/img/room11.jpg'),
('112', 'Executive', 320.00, 'Executive room with city skyline view and mini bar.', 'assets/img/room12.jpg'),
('113', 'Deluxe', 155.00, 'Deluxe room overlooking hotel courtyard.', 'assets/img/room13.jpg'),
('114', 'Superior', 130.00, 'Superior room with smart TV and workspace.', 'assets/img/room14.jpg'),
('115', 'Standard', 110.00, 'Comfortable standard room with king-size bed.', 'assets/img/room15.jpg'),
('116', 'Suite', 260.00, 'Elegant suite featuring large sofa and luxury bathroom.', 'assets/img/room16.jpg'),
('117', 'Family', 230.00, 'Family suite with two bedrooms and shared living room.', 'assets/img/room17.jpg'),
('118', 'Deluxe', 165.00, 'Deluxe room with artistic decor and city lights view.', 'assets/img/room18.jpg'),
('119', 'Superior', 140.00, 'Superior room near swimming pool with relaxing ambiance.', 'assets/img/room19.jpg'),
('120', 'Presidential', 600.00, 'Grand presidential suite with private lounge and personal butler.', 'assets/img/room20.jpg');


INSERT INTO bookings (user_id, room_id, check_in, check_out, status) VALUES
(1, 1, '2025-11-01', '2025-11-03', 'confirmed'),
(2, 2, '2025-11-05', '2025-11-07', 'pending'),
(3, 3, '2025-11-10', '2025-11-12', 'confirmed'),
(2, 4, '2025-11-15', '2025-11-18', 'cancelled'),
(4, 5, '2025-11-20', '2025-11-23', 'pending'),
(1, 6, '2025-11-25', '2025-11-27', 'confirmed'),
(5, 7, '2025-12-01', '2025-12-03', 'pending'),
(3, 8, '2025-12-04', '2025-12-07', 'confirmed'),
(2, 9, '2025-12-10', '2025-12-12', 'confirmed'),
(4, 10, '2025-12-15', '2025-12-17', 'cancelled');

SELECT id, room_number FROM rooms;

SHOW TABLES;
SHOW COLUMNS FROM users;

SHOW COLUMNS FROM rooms;
select * FROM users;
SELECT id, username FROM users;
SELECT id, room_number FROM rooms;

SET SQL_SAFE_UPDATES = 0;
select * FROM rooms;
UPDATE rooms SET iage = 'standar.jpg' WHERE type = 'Standard';
UPDATE rooms SET image = 'deluxe.jpg' WHERE type = 'Deluxe';
UPDATE rooms SET image = 'suite.jpg' WHERE type = 'Suite';
UPDATE rooms SET image = 'presidential.jpeg' WHERE type = 'Presidential';
UPDATE rooms SET image = 'family.jpg' WHERE type = 'Family';
UPDATE rooms SET image = 'executive.jpg' WHERE type = 'Executive';

SET SQL_SAFE_UPDATES = 1;

SET SQL_SAFE_UPDATES = 0;

UPDATE rooms 
SET image = CONCAT('assets/foto/rooms/', image);

SET SQL_SAFE_UPDATES = 1;

UPDATE users 
SET password = '$2y$10$Kj9x8LmNpQrStUvWyZx6eO8fGhJkLmNqRtUvWyZx6eO8fGhJkLmNq' 
WHERE username = 'admin';
