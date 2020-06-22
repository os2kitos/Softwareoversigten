-- phpMyAdmin SQL Dump

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kitos`
--
CREATE DATABASE kitos;
USE kitos;


-- CREATE USER
CREATE USER 'kitos'@'localhost' IDENTIFIED BY 'kitos';
-- GRANT PRIVILEGES ON kitos DATABASE
GRANT ALL PRIVILEGES ON kitos.* TO 'kitos'@'localhost';
-- SAVE THE CHANGES;
FLUSH PRIVILEGES;
-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `cached_data`
--

CREATE TABLE `cached_data` (
  `id` int(11) NOT NULL,
  `kitosID` text COLLATE utf8_danish_ci NOT NULL,
  `UUID` text COLLATE utf8_danish_ci NOT NULL,
  `SupplierName` text COLLATE utf8_danish_ci,
  `Name` text COLLATE utf8_danish_ci NOT NULL,
  `LocalName` text COLLATE utf8_danish_ci,
  `Description` text COLLATE utf8_danish_ci,
  `Url` text COLLATE utf8_danish_ci,
  `KleName` text COLLATE utf8_danish_ci,
  `Note` text COLLATE utf8_danish_ci,
  `BusinessType` text COLLATE utf8_danish_ci,
  `SystemOwner_name` text COLLATE utf8_danish_ci,
  `SystemOwner_email` text COLLATE utf8_danish_ci,
  `OperationalResponsible_name` text COLLATE utf8_danish_ci NOT NULL,
  `OperationalResponsible_email` text COLLATE utf8_danish_ci NOT NULL,
  `ContactPerson_name` text COLLATE utf8_danish_ci,
  `ContactPerson_email` text COLLATE utf8_danish_ci,
  `ResponsibleOrganizationalUnit` text COLLATE utf8_danish_ci,
  `TimeOfImport` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `usagelog`
--

CREATE TABLE `usagelog` (
  `id` int(11) NOT NULL,
  `d1UserName` text COLLATE utf8_danish_ci NOT NULL,
  `searchString` text COLLATE utf8_danish_ci NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

--
-- Begrænsninger for dumpede tabeller
--

--
-- Indeks for tabel `cached_data`
--
ALTER TABLE `cached_data`
  ADD UNIQUE KEY `id` (`id`);
ALTER TABLE `cached_data` ADD FULLTEXT KEY `Name` (`Name`,`Description`);
ALTER TABLE `cached_data` ADD FULLTEXT KEY `Name_2` (`Name`,`LocalName`,`SupplierName`,`Description`,`KleName`,`BusinessType`,`SystemOwner_name`,`SystemOwner_email`,`ContactPerson_name`,`ContactPerson_email`,`ResponsibleOrganizationalUnit`);

--
-- Indeks for tabel `usagelog`
--
ALTER TABLE `usagelog`
  ADD PRIMARY KEY (`id`);

--
-- Brug ikke AUTO_INCREMENT for slettede tabeller
--

--
-- Tilføj AUTO_INCREMENT i tabel `cached_data`
--
ALTER TABLE `cached_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=263;
--
-- Tilføj AUTO_INCREMENT i tabel `usagelog`
--
ALTER TABLE `usagelog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1996;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
