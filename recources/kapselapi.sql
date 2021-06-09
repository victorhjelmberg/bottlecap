-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Vært: 127.0.0.1
-- Genereringstid: 09. 06 2021 kl. 13:33:01
-- Serverversion: 10.4.19-MariaDB
-- PHP-version: 8.0.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kapselapi`
--

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `apikey`
--

CREATE TABLE `apikey` (
  `tabelID` int(11) NOT NULL,
  `identifier` text NOT NULL,
  `reqLeft` int(11) NOT NULL,
  `dateTimeobj` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Data dump for tabellen `apikey`
--

INSERT INTO `apikey` (`tabelID`, `identifier`, `reqLeft`, `dateTimeobj`) VALUES
(8, '$2y$10$5dMbdwUIJ12qI3NE/wpbGe632PLWVMoVNh5zfolZr/0oLLRU3JH4q', 20, '2021-05-30 15:13:07');

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `cap`
--

CREATE TABLE `cap` (
  `capID` int(11) NOT NULL,
  `unformatedText` text NOT NULL,
  `gameclassID` int(11) NOT NULL,
  `drinkAmount` int(11) DEFAULT NULL,
  `difficulty` int(11) DEFAULT NULL,
  `keywordsarray` text NOT NULL,
  `shockcategoryarray` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Data dump for tabellen `cap`
--

INSERT INTO `cap` (`capID`, `unformatedText`, `gameclassID`, `drinkAmount`, `difficulty`, `keywordsarray`, `shockcategoryarray`) VALUES
(1, 'Alle prøver at få {} til at grine, hvis {} griner drikker han 2 tåre', 1, 5, 5, '[\'blabla\', \'blabla2\']', '[\'evil\']'),
(2, '{} drikker', 2, 2, 9, '[\'randomplayer\', \'randomplayer2\']', '[]'),
(3, 'skip denne', 2, 0, 0, '[]', '[]');

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `gameclass`
--

CREATE TABLE `gameclass` (
  `gameclassID` int(11) NOT NULL,
  `color` tinytext NOT NULL,
  `discription` text NOT NULL,
  `name` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Data dump for tabellen `gameclass`
--

INSERT INTO `gameclass` (`gameclassID`, `color`, `discription`, `name`) VALUES
(1, 'sort', 'Alle prøver at få hans hansen til at grine', 'Alle prøver at'),
(2, 'red', 'Something random happens', 'Random');

--
-- Begrænsninger for dumpede tabeller
--

--
-- Indeks for tabel `apikey`
--
ALTER TABLE `apikey`
  ADD PRIMARY KEY (`tabelID`);

--
-- Indeks for tabel `cap`
--
ALTER TABLE `cap`
  ADD PRIMARY KEY (`capID`),
  ADD KEY `gameclassID` (`gameclassID`);

--
-- Indeks for tabel `gameclass`
--
ALTER TABLE `gameclass`
  ADD PRIMARY KEY (`gameclassID`);

--
-- Brug ikke AUTO_INCREMENT for slettede tabeller
--

--
-- Tilføj AUTO_INCREMENT i tabel `apikey`
--
ALTER TABLE `apikey`
  MODIFY `tabelID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Tilføj AUTO_INCREMENT i tabel `cap`
--
ALTER TABLE `cap`
  MODIFY `capID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tilføj AUTO_INCREMENT i tabel `gameclass`
--
ALTER TABLE `gameclass`
  MODIFY `gameclassID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Begrænsninger for dumpede tabeller
--

--
-- Begrænsninger for tabel `cap`
--
ALTER TABLE `cap`
  ADD CONSTRAINT `cap_ibfk_1` FOREIGN KEY (`gameclassID`) REFERENCES `gameclass` (`gameclassID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
