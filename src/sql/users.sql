# Database
CREATE DATABASE IF NOT EXISTS delivery CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
# Utwórz użytkownika
GRANT ALL ON `delivery`.* TO 'delivery'@'localhost' IDENTIFIED BY 'toor';
GRANT ALL ON `delivery`.* TO 'delivery'@'127.0.0.1' IDENTIFIED BY 'toor';
FLUSH PRIVILEGES;

# Utwórz bazę danych
# CREATE DATABASE IF NOT EXISTS delivery CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
# Uprawnienia do odczytu, zapisu
# GRANT ALL ON *.* TO 'delivery'@'localhost' IDENTIFIED BY 'toor';
# Uprawnienia do tworzenia tabel
# GRANT ALL ON *.* TO 'delivery'@'127.0.0.1' IDENTIFIED BY 'toor' WITH GRANT OPTION;

# Import users !!!
# mysql -u root -p < delivery.sql
# mysql -u root -p < users.sql

