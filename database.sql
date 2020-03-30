create database if not exists ServerInventory;
	
CREATE USER 'servermanager'@'localhost' IDENTIFIED BY 'servermanager';
GRANT ALL PRIVILEGES ON ServerInventory.Servers TO 'servermanager'@'localhost';

use ServerInventory;

CREATE TABLE IF NOT EXISTS `Servers` (
  `ServerId` varchar(11) NOT NULL UNIQUE,
  `ServerName` varchar(32) NOT NULL,
  `IP` varbinary(16) NOT NULL UNIQUE,
  `Location` varchar(32) NOT NULL,
  `Description` varchar(64) NOT NULL,
  PRIMARY KEY (`ServerId`)
);

INSERT INTO `Servers` (`ServerId`,`ServerName`,`IP`, `Location`,`Description`) VALUES
('Serv1','monitoring',INET_ATON('178.34.234.44'), 'Ottawa','Server health Monitoring'),
('Serv2','Email',INET_ATON('176.92.191.120'), 'Montreal','Email gateway server'),
('Serv3','SMS',INET_ATON('7.97.207.231'),'Toronto', 'SMS gateway server'),
('Serv4','user database',INET_ATON('2.67.132.171'),'Calgary', 'server storing user details'),
('Serv5','product database',INET_ATON('254.200.201.48'),'Edmonton', 'server storing product details'),
('Serv6','company website',INET_ATON('227.230.215.199'),'Vancouver', 'server to company site'),
('Serv7','authentication',INET_ATON('196.102.100.163'),'Halifax', 'user authentication server'),
('Serv8','payment',INET_ATON('188.198.83.210'),'Kingston', 'Payment gateway server'),
('Serv9','merchant database',INET_ATON('132.172.165.88'),'Waterloo', 'merchant details storing server'),
('Serv10','data backup',INET_ATON('63.2.221.10'),'Victoria', 'data backup server'),
('Serv11','FTP',INET_ATON('52.196.132.152'),'London', 'FTP server'),
('Serv12','Proxy',INET_ATON('208.146.135.203'),'Regina', 'Proxy server'),
('Serv13','application site',INET_ATON('1.123.100.161'),'Windsor', 'server hosting application site'),
('Serv14','logging server',INET_ATON('76.128.71.178'),'Guelph', 'transaction logging server'),
('Serv15','News publishing',INET_ATON('29.213.12.133'),'Burnaby', 'server to publish news to users');