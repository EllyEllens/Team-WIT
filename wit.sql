-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Gegenereerd op: 23 mrt 2025 om 20:20
-- Serverversie: 8.4.3
-- PHP-versie: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wit`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `aanwezigheid`
--

CREATE TABLE `aanwezigheid` (
  `aanwezigheid_id` int NOT NULL,
  `student_id` int DEFAULT NULL,
  `lesblok_id` int DEFAULT NULL,
  `klas_id` int DEFAULT NULL,
  `presentie_code` enum('Aanwezig','Afwezig','Laat','Laatbrief','Vrijstelling') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `opmerking` varchar(255) DEFAULT NULL,
  `aanwezigheid_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Gegevens worden geëxporteerd voor tabel `aanwezigheid`
--

INSERT INTO `aanwezigheid` (`aanwezigheid_id`, `student_id`, `lesblok_id`, `klas_id`, `presentie_code`, `opmerking`, `aanwezigheid_date`) VALUES
(8, 12, 1, 3, 'Aanwezig', '---', NULL),
(9, 11, 1, 3, 'Aanwezig', '---', NULL),
(10, 13, 2, 7, 'Aanwezig', '---', NULL),
(11, 14, 2, 7, 'Laat', 'Verslapen', NULL),
(12, 10, 2, 7, 'Afwezig', 'Ziek', NULL);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `gemuteerde`
--

CREATE TABLE `gemuteerde` (
  `gemuteerd_id` int NOT NULL,
  `student_id` int DEFAULT NULL,
  `klas_id` int DEFAULT NULL,
  `leerjaar_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `klas`
--

CREATE TABLE `klas` (
  `klas_id` int NOT NULL,
  `leerjaar_id` int DEFAULT NULL,
  `naam` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `klas`
--

INSERT INTO `klas` (`klas_id`, `leerjaar_id`, `naam`) VALUES
(3, NULL, '4.06.11'),
(7, NULL, '4.06.21'),
(8, NULL, '1.06.01'),
(9, NULL, '1.06.02'),
(10, NULL, '2.06.01'),
(11, NULL, '2.06.02'),
(12, NULL, '3.06.11'),
(13, NULL, '3.06.21');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `leerjaar`
--

CREATE TABLE `leerjaar` (
  `leerjaar_id` int NOT NULL,
  `naam` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `leerjaar`
--

INSERT INTO `leerjaar` (`leerjaar_id`, `naam`) VALUES
(1, 'Leerjaar 1'),
(2, 'Leerjaar 2'),
(3, 'Leerjaar 3'),
(4, 'Leerjaar 4');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `leerjaar_periode`
--

CREATE TABLE `leerjaar_periode` (
  `id` int NOT NULL,
  `leerjaar_id` int DEFAULT NULL,
  `periode_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `lesblok`
--

CREATE TABLE `lesblok` (
  `lesblok_id` int NOT NULL,
  `periode_id` int DEFAULT NULL,
  `dag` enum('Ma','Di','Wo','Do','Vr','Za','Zo') COLLATE utf8mb4_general_ci NOT NULL,
  `start` time NOT NULL,
  `eind` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `lesblok`
--

INSERT INTO `lesblok` (`lesblok_id`, `periode_id`, `dag`, `start`, `eind`) VALUES
(1, 11, 'Ma', '07:00:00', '08:30:00'),
(2, 11, 'Ma', '08:45:00', '10:15:00'),
(3, 11, 'Ma', '10:30:00', '12:00:00'),
(4, 11, 'Ma', '12:15:00', '13:45:00'),
(9, 11, 'Di', '07:00:00', '08:30:00'),
(10, 11, 'Di', '08:45:00', '10:15:00'),
(11, 11, 'Di', '10:30:00', '12:00:00'),
(12, 11, 'Di', '12:15:00', '13:45:00'),
(13, 11, 'Wo', '07:00:00', '08:30:00'),
(14, 11, 'Wo', '08:45:00', '10:15:00'),
(15, 11, 'Wo', '10:30:00', '12:00:00'),
(16, 11, 'Wo', '12:15:00', '13:45:00'),
(17, 11, 'Do', '07:00:00', '08:30:00'),
(18, 11, 'Do', '08:45:00', '10:15:00'),
(19, 11, 'Do', '10:30:00', '12:00:00'),
(20, 11, 'Do', '12:15:00', '13:45:00'),
(21, 11, 'Vr', '07:00:00', '08:30:00'),
(22, 11, 'Vr', '08:45:00', '10:15:00'),
(23, 11, 'Vr', '10:30:00', '12:00:00'),
(24, 11, 'Vr', '12:15:00', '13:45:00');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `opmerking`
--

CREATE TABLE `opmerking` (
  `opmerking_id` int NOT NULL,
  `opmerking` text COLLATE utf8mb4_general_ci,
  `paragraaf` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `periode`
--

CREATE TABLE `periode` (
  `periode_id` int NOT NULL,
  `periode` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `periode`
--

INSERT INTO `periode` (`periode_id`, `periode`) VALUES
(1, 'Periode 1'),
(2, 'Periode 2'),
(3, 'Periode 3'),
(4, 'Periode 4'),
(5, 'Periode 5'),
(6, 'Periode 6'),
(7, 'Periode 7'),
(8, 'Periode 8'),
(9, 'Periode 9'),
(10, 'Periode 10'),
(11, 'Periode 11'),
(12, 'Periode 12');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `personen`
--

CREATE TABLE `personen` (
  `person_id` int NOT NULL,
  `rol_id` int DEFAULT NULL,
  `voornaam` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `achternaam` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `geboortedatum` date DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telefoon` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `adres` text COLLATE utf8mb4_general_ci,
  `geslacht` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('actief','inactief') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `wachtwoord` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `reset_token` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `personen`
--

INSERT INTO `personen` (`person_id`, `rol_id`, `voornaam`, `achternaam`, `geboortedatum`, `email`, `telefoon`, `adres`, `geslacht`, `status`, `wachtwoord`, `reset_token`) VALUES
(6, 4, 'Irwin', 'Noordwijk', '2025-03-01', 'irwin.noordwijk@natin.sr', '+597 7612345', 'Zanderijweg #11', 'Man', 'actief', '$2y$10$gTiBygWAMb.WJ/.Turc7EeOJq.Z9ONzAf32QR.BWOTclItekYtJj6', NULL),
(7, 3, 'Ivette', 'Oostburg', '2025-03-02', 'ivette.oostburg@natin.sr', '+597 8234567', 'Brownsweg #15', 'Vrouw', 'actief', '$2y$10$gXaKfvEKENffGZjrYnnFAevlJkdc1HLc5ERwKXSrIm0iaX11EvnCS', NULL),
(8, 3, 'Sharrol Vliese', 'Vliese', '2025-03-03', 'sharrol.vliese@natin.sr', '+597 4337890', 'Zeedijkweg #20', 'Vrouw', 'actief', '$2y$10$5bpFsKbt1AzHY45sQXxiVuMtPojHJTVsv4pGviZ4R2OgKtOfdh06C', NULL),
(9, 4, 'Sardha', 'Raghosing', '2025-03-04', 'sardha.raghosing@natin.sr', '+597 3156789', 'Meerzorgweg #102', 'Vrouw', 'actief', '$2y$10$CcC0PH6a2lUOtlY2hBKTje4PpTn1G2FW0Q3HpK91D/0wYjgRFv8EG', NULL),
(10, 3, 'Carol', 'Arsomedjo', '2025-03-05', 'carol.arsomedjo@natin.sr', '+597 7456789', 'Hannaslustweg #77', 'Vrouw', 'actief', '$2y$10$BIQESl2YE9jctDTzTumsx.gOz2YZtSOuDw.UShne57Bk0rNJ4rN7O', NULL),
(11, 3, 'Clifton', 'Jozefzoon', '2025-03-06', 'clifton.jozefzoon@natin.sr', '+597 8187654', 'Magentakanaalweg #20', 'Man', 'actief', '$2y$10$DzOPuawb7dEGXkwZDGlcOumUViRcyyriLSYzfV5mG/3zs7QbT6EK2', NULL);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `presentie`
--

CREATE TABLE `presentie` (
  `status_id` int NOT NULL,
  `type` char(1) COLLATE utf8mb4_general_ci NOT NULL,
  `presentie` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `richting`
--

CREATE TABLE `richting` (
  `richting_id` int NOT NULL,
  `naam` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `rollen`
--

CREATE TABLE `rollen` (
  `rol_id` int NOT NULL,
  `soort` enum('docent','admin') COLLATE utf8mb4_general_ci NOT NULL,
  `rol` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `rollen`
--

INSERT INTO `rollen` (`rol_id`, `soort`, `rol`) VALUES
(3, 'docent', 'Docent'),
(4, 'admin', 'Beheerder');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `rol_persoon`
--

CREATE TABLE `rol_persoon` (
  `rp_id` int NOT NULL,
  `rol_id` int DEFAULT NULL,
  `person_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `rol_persoon`
--

INSERT INTO `rol_persoon` (`rp_id`, `rol_id`, `person_id`) VALUES
(6, 4, 6),
(7, 3, 7),
(8, 3, 8),
(9, 4, 9),
(10, 3, 10),
(11, 3, 11);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `studenten`
--

CREATE TABLE `studenten` (
  `student_id` int NOT NULL,
  `voornaam` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `achternaam` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `geboortedatum` date DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `adres` text COLLATE utf8mb4_general_ci,
  `geslacht` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('actief','deactief') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telefoon` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `wachtwoord` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `reset_token` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `studenten`
--

INSERT INTO `studenten` (`student_id`, `voornaam`, `achternaam`, `geboortedatum`, `email`, `adres`, `geslacht`, `status`, `telefoon`, `wachtwoord`, `reset_token`) VALUES
(10, 'Ielaiza', 'Ellensburg', '2025-03-01', 'ielaiza.ellensburg@student.natin.sr', 'Konsaistraat #6', 'Vrouw', 'actief', '+597 867 3083', '$2y$10$bvmHBkYuGS3uMOaPufLL9OT8gdGUGd/CJ5EIaNsqIc0tf8CnraLnO', NULL),
(11, 'Fizario', 'Dollart', '2025-03-02', 'fizario.dollart@student.natin.sr', 'Keizerstraat #3', 'Man', 'actief', '+597 8123456', '$2y$10$cET9jZ.NIfBNCzMeZ9wmq.roF1u34cABUN4TOAopGr1bjbDA3cIJG', NULL),
(12, 'Vyaas', 'Kalloe', '2025-03-03', 'vyaas.kalloe@student.natin.sr', 'Prinsstraat #11', 'Man', 'actief', '+597 4216789', '$2y$10$r0ZdV2tqEbQJQE55Uw1FHeE7mvc8fp5YiBlOkLCbg.i1FJiaARVw6', NULL),
(13, 'Kajol', 'Soekha', '2025-03-04', 'kajol.soekha@student.natin.sr', 'Appelstraar #19', 'Vrouw', 'actief', '+597 3023456', '$2y$10$HMx1sHDcYyhyiETWkmR6jO7ezpYKrzVudnwUXBs.DWTqNYJ5tFoOG', NULL),
(14, 'Max', 'Soerohardjo', '2025-03-05', 'max.soerohardjo@student.natin.sr', 'Peperpotweg #2', 'Man', 'actief', '+597 5512345', '$2y$10$wvqEPwUcCTlHxifoRN3tV.4ZZAyEPf8a840cOFR9XFy4aHGm9yrRy', NULL),
(15, 'Lukas', 'Jansen', '2025-03-06', 'lukas.jansen@student.natin.sr', 'Maagdenstraat #9', 'Man', 'actief', '+597 5512345', '$2y$10$cblbTpnlelKsxU2ZWlTLcOW3T5cDvnj5Bmo/p/epj7if4fW3Pkbwu', NULL),
(16, 'Emma', 'Meijer', '2025-03-07', 'emma.meijer@student.natin.sr', 'Arronstraat #2', 'Vrouw', 'actief', '+597 2256789', '$2y$10$Uh0jsrNHfTH5FN0ayG8hu.198oZD.qhoqkZpsp9z6ZUmfQ3.CY.Fu', NULL),
(17, 'Maximum', 'de Vries', '2025-03-08', 'maximum.devries@student.natin.sr', 'Nassylaan #10', 'Man', 'actief', '+597 7523456', '$2y$10$oZir5epiPMi8xjii9cmX8eMZCPjMUlW0vvDgHVKzSAVQpX.gYA3zK', NULL),
(18, 'Sophie', 'Janssen', '2025-03-09', 'sophie.janssen@student.natin.sr', 'Pengelstraat #44', 'Vrouw', 'actief', '+597 8734567', '$2y$10$FeUEHnIwp7W/4mCEWLTrVuoYFeA8ZzZu6DwjTUv/KDkr/T55i9H1.', NULL),
(19, 'Noah', 'Bakker', '2025-03-10', 'noah.bakker@student.natin.sr', 'Afobakaweg #112', 'Man', 'actief', '+597 4367890', '$2y$10$fJ7BHG3OabHJUrfjKFwh0u1AOAuC/EJyz28ixvNnPBUn04WjZPvze', NULL),
(20, 'Mila', 'de Groot', '2025-03-11', 'mila.degroot@student.natin.sr', 'Molenpad #30', 'Vrouw', 'actief', '+597 3102345', '$2y$10$4HL78ba5u3SMEUzwle9ALuhmLNFCorf2k1CKMr4pcbsss05xDzSzy', NULL),
(21, 'Milan', 'Smit', '2025-03-12', 'milan.smit@student.natin.sr', 'Koestraat #25', 'Man', 'actief', '+597 7689012', '$2y$10$yuZFBrH5RWI.zwE91lvdsernOJ/LCBW37nXO0UgGUJzlbkhHscZpa', NULL),
(22, 'Lisa', 'van den Berg', '2025-03-13', 'lisa.vandenberg@student.natin.sr', 'Albinaweg #31', 'Vrouw', 'actief', '+597 8412345', '$2y$10$yL3pM2DvkKD5y9PvhswWH.5WTls7wTq4YkQWX57ydot7tj0Bxlo4S', NULL),
(23, 'Thomas', 'Bos', '2025-03-14', 'thomas.bos@student.natin.sr', 'Lokaalweg #7', 'Man', 'actief', '+597 4234567', '$2y$10$AwI5IOsPGlBQhNloux3qhO6EHdamJX0PmbV8EMAuOZJGcvcfny.Bu', NULL),
(24, 'Nora', 'Willems', '2025-03-15', 'nora.willems@student.natin.sr', 'Drankweg #23', 'Vrouw', 'actief', '+597 3087654', '$2y$10$ADDT36IVfJkYJebmC8ArsOXYKLzTW9sFeE9520Mq1BNdfezaSho6i', NULL),
(25, 'Finn', 'Mulder', '2025-03-16', 'finn.mulder@student.natin.sr', 'Heerenstraat #6', 'Man', 'actief', '+597 7823456', '$2y$10$1.2Y4ik.I5vq6wio3i2X0uTyfPj97IbINF3PTATigLXntDhY8oDy6', NULL),
(26, 'Zoe', 'Kramer', '2025-03-17', 'zoe.kramer@student.natin.sr', 'Kraagweg #22', 'Vrouw', 'actief', '+597 8512345', '$2y$10$pde9x7wSI5YBt9orOOq5LOu9K0jKgwDA/vj67Gh83XGwAnhFup4ri', NULL);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `student_klas`
--

CREATE TABLE `student_klas` (
  `student_id` int NOT NULL,
  `klas_id` int NOT NULL,
  `status` enum('actief','deactief') COLLATE utf8mb4_general_ci DEFAULT 'actief'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `student_klas`
--

INSERT INTO `student_klas` (`student_id`, `klas_id`, `status`) VALUES
(10, 7, 'actief'),
(11, 3, 'actief'),
(12, 3, 'actief'),
(13, 7, 'actief'),
(14, 7, 'actief'),
(15, 8, 'actief'),
(16, 8, 'actief'),
(17, 9, 'actief'),
(18, 9, 'actief'),
(19, 10, 'actief'),
(20, 10, 'actief'),
(21, 10, 'actief'),
(22, 11, 'actief'),
(23, 12, 'actief'),
(24, 12, 'actief'),
(25, 13, 'actief'),
(26, 13, 'actief');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `vak`
--

CREATE TABLE `vak` (
  `vak_id` int NOT NULL,
  `periode_id` int DEFAULT NULL,
  `vak` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `aanwezigheid`
--
ALTER TABLE `aanwezigheid`
  ADD PRIMARY KEY (`aanwezigheid_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `lesblok_id` (`lesblok_id`),
  ADD KEY `klas_id` (`klas_id`);

--
-- Indexen voor tabel `gemuteerde`
--
ALTER TABLE `gemuteerde`
  ADD PRIMARY KEY (`gemuteerd_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `klas_id` (`klas_id`),
  ADD KEY `leerjaar_id` (`leerjaar_id`);

--
-- Indexen voor tabel `klas`
--
ALTER TABLE `klas`
  ADD PRIMARY KEY (`klas_id`),
  ADD KEY `leerjaar_id` (`leerjaar_id`);

--
-- Indexen voor tabel `leerjaar`
--
ALTER TABLE `leerjaar`
  ADD PRIMARY KEY (`leerjaar_id`);

--
-- Indexen voor tabel `leerjaar_periode`
--
ALTER TABLE `leerjaar_periode`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leerjaar_id` (`leerjaar_id`),
  ADD KEY `periode_id` (`periode_id`);

--
-- Indexen voor tabel `lesblok`
--
ALTER TABLE `lesblok`
  ADD PRIMARY KEY (`lesblok_id`),
  ADD KEY `periode_id` (`periode_id`);

--
-- Indexen voor tabel `opmerking`
--
ALTER TABLE `opmerking`
  ADD PRIMARY KEY (`opmerking_id`);

--
-- Indexen voor tabel `periode`
--
ALTER TABLE `periode`
  ADD PRIMARY KEY (`periode_id`);

--
-- Indexen voor tabel `personen`
--
ALTER TABLE `personen`
  ADD PRIMARY KEY (`person_id`),
  ADD KEY `rol_id` (`rol_id`);

--
-- Indexen voor tabel `presentie`
--
ALTER TABLE `presentie`
  ADD PRIMARY KEY (`status_id`);

--
-- Indexen voor tabel `richting`
--
ALTER TABLE `richting`
  ADD PRIMARY KEY (`richting_id`);

--
-- Indexen voor tabel `rollen`
--
ALTER TABLE `rollen`
  ADD PRIMARY KEY (`rol_id`);

--
-- Indexen voor tabel `rol_persoon`
--
ALTER TABLE `rol_persoon`
  ADD PRIMARY KEY (`rp_id`),
  ADD KEY `rol_id` (`rol_id`),
  ADD KEY `person_id` (`person_id`);

--
-- Indexen voor tabel `studenten`
--
ALTER TABLE `studenten`
  ADD PRIMARY KEY (`student_id`);

--
-- Indexen voor tabel `student_klas`
--
ALTER TABLE `student_klas`
  ADD PRIMARY KEY (`student_id`,`klas_id`),
  ADD KEY `klas_id` (`klas_id`);

--
-- Indexen voor tabel `vak`
--
ALTER TABLE `vak`
  ADD PRIMARY KEY (`vak_id`),
  ADD KEY `periode_id` (`periode_id`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `aanwezigheid`
--
ALTER TABLE `aanwezigheid`
  MODIFY `aanwezigheid_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT voor een tabel `gemuteerde`
--
ALTER TABLE `gemuteerde`
  MODIFY `gemuteerd_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `klas`
--
ALTER TABLE `klas`
  MODIFY `klas_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT voor een tabel `leerjaar`
--
ALTER TABLE `leerjaar`
  MODIFY `leerjaar_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT voor een tabel `leerjaar_periode`
--
ALTER TABLE `leerjaar_periode`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `lesblok`
--
ALTER TABLE `lesblok`
  MODIFY `lesblok_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT voor een tabel `opmerking`
--
ALTER TABLE `opmerking`
  MODIFY `opmerking_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `periode`
--
ALTER TABLE `periode`
  MODIFY `periode_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT voor een tabel `personen`
--
ALTER TABLE `personen`
  MODIFY `person_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT voor een tabel `presentie`
--
ALTER TABLE `presentie`
  MODIFY `status_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `richting`
--
ALTER TABLE `richting`
  MODIFY `richting_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `rol_persoon`
--
ALTER TABLE `rol_persoon`
  MODIFY `rp_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT voor een tabel `studenten`
--
ALTER TABLE `studenten`
  MODIFY `student_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT voor een tabel `vak`
--
ALTER TABLE `vak`
  MODIFY `vak_id` int NOT NULL AUTO_INCREMENT;

--
-- Beperkingen voor geëxporteerde tabellen
--

--
-- Beperkingen voor tabel `aanwezigheid`
--
ALTER TABLE `aanwezigheid`
  ADD CONSTRAINT `aanwezigheid_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `studenten` (`student_id`),
  ADD CONSTRAINT `aanwezigheid_ibfk_2` FOREIGN KEY (`lesblok_id`) REFERENCES `lesblok` (`lesblok_id`),
  ADD CONSTRAINT `aanwezigheid_ibfk_3` FOREIGN KEY (`klas_id`) REFERENCES `klas` (`klas_id`),
  ADD CONSTRAINT `aanwezigheid_ibfk_4` FOREIGN KEY (`klas_id`) REFERENCES `klas` (`klas_id`);

--
-- Beperkingen voor tabel `gemuteerde`
--
ALTER TABLE `gemuteerde`
  ADD CONSTRAINT `gemuteerde_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `studenten` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `gemuteerde_ibfk_2` FOREIGN KEY (`klas_id`) REFERENCES `klas` (`klas_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `gemuteerde_ibfk_3` FOREIGN KEY (`leerjaar_id`) REFERENCES `leerjaar` (`leerjaar_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Beperkingen voor tabel `klas`
--
ALTER TABLE `klas`
  ADD CONSTRAINT `klas_ibfk_1` FOREIGN KEY (`leerjaar_id`) REFERENCES `leerjaar` (`leerjaar_id`);

--
-- Beperkingen voor tabel `leerjaar_periode`
--
ALTER TABLE `leerjaar_periode`
  ADD CONSTRAINT `leerjaar_periode_ibfk_1` FOREIGN KEY (`leerjaar_id`) REFERENCES `leerjaar` (`leerjaar_id`),
  ADD CONSTRAINT `leerjaar_periode_ibfk_2` FOREIGN KEY (`periode_id`) REFERENCES `periode` (`periode_id`);

--
-- Beperkingen voor tabel `lesblok`
--
ALTER TABLE `lesblok`
  ADD CONSTRAINT `lesblok_ibfk_1` FOREIGN KEY (`periode_id`) REFERENCES `periode` (`periode_id`);

--
-- Beperkingen voor tabel `personen`
--
ALTER TABLE `personen`
  ADD CONSTRAINT `personen_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `rollen` (`rol_id`);

--
-- Beperkingen voor tabel `rol_persoon`
--
ALTER TABLE `rol_persoon`
  ADD CONSTRAINT `rol_persoon_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `rollen` (`rol_id`),
  ADD CONSTRAINT `rol_persoon_ibfk_2` FOREIGN KEY (`person_id`) REFERENCES `personen` (`person_id`) ON DELETE CASCADE;

--
-- Beperkingen voor tabel `student_klas`
--
ALTER TABLE `student_klas`
  ADD CONSTRAINT `fk_student_id` FOREIGN KEY (`student_id`) REFERENCES `studenten` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_klas_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `studenten` (`student_id`),
  ADD CONSTRAINT `student_klas_ibfk_2` FOREIGN KEY (`klas_id`) REFERENCES `klas` (`klas_id`);

--
-- Beperkingen voor tabel `vak`
--
ALTER TABLE `vak`
  ADD CONSTRAINT `vak_ibfk_1` FOREIGN KEY (`periode_id`) REFERENCES `periode` (`periode_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
