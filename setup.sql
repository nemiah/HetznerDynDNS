CREATE USER 'phpddns'@'localhost' IDENTIFIED BY 'tkwu0xrrF0JqMShc';
CREATE DATABASE IF NOT EXISTS phpddns;
GRANT ALL PRIVILEGES ON phpddns . * TO 'phpddns'@'localhost';

FLUSH PRIVILEGES;

USE phpddns;

CREATE TABLE `NSUser` (
  `NSUserID` int(10) NOT NULL,
  `NSUserName` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `NSUserPassword` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `NSUserDomain` varchar(500) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `NSUser` (`NSUserID`, `NSUserName`, `NSUserPassword`, `NSUserDomain`) VALUES
(1, 'nena', SHA1('Hallo123'), 'nemiah.de');

ALTER TABLE `NSUser`
  ADD PRIMARY KEY (`NSUserID`);

ALTER TABLE `NSUser`
  MODIFY `NSUserID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
