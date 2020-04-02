# Database
CREATE DATABASE IF NOT EXISTS payu CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
# Utwórz użytkownika
GRANT ALL ON `payu`.* TO 'payu'@'localhost' IDENTIFIED BY 'toor';
GRANT ALL ON `payu`.* TO 'payu'@'127.0.0.1' IDENTIFIED BY 'toor';
FLUSH PRIVILEGES;

# Utwórz bazę danych
# CREATE DATABASE IF NOT EXISTS payu CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
# Uprawnienia do odczytu, zapisu
# GRANT ALL ON *.* TO 'payu'@'localhost' IDENTIFIED BY 'toor';
# Uprawnienia do tworzenia tabel
# GRANT ALL ON *.* TO 'payu'@'127.0.0.1' IDENTIFIED BY 'toor' WITH GRANT OPTION;

# Import users !!!
# mysql -u root -p < payu.sql
# mysql -u root -p < payu_users.sql

