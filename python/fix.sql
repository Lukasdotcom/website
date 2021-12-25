CREATE USER '{username}' IDENTIFIED BY '{password}';
CREATE DATABASE {database};
GRANT ALL PRIVILEGES ON *.* TO '{username}'@localhost IDENTIFIED BY ‘{password}’;