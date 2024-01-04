-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 04. Jan 2024 um 23:31
-- Server-Version: 10.4.27-MariaDB
-- PHP-Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `ide4school`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `2fa`
--

CREATE TABLE `2fa` (
  `id` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  `type` int(1) NOT NULL,
  `owner` int(5) NOT NULL,
  `secret` text NOT NULL,
  `topt` int(6) DEFAULT NULL,
  `creation_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `assignments`
--

CREATE TABLE `assignments` (
  `id` int(11) NOT NULL,
  `teacher` varchar(100) NOT NULL,
  `class` varchar(100) NOT NULL,
  `access` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `assignments`
--

INSERT INTO `assignments` (`id`, `teacher`, `class`, `access`) VALUES
(151, '753', 'Lehrer', 1),
(154, '753', 'Musterklasse', 1),
(177, '753', 'Musterklasse 2', 1),
(178, '758', 'Musterklasse', 1),
(179, '758', 'Musterklasse 2', 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `focus_mode` int(1) NOT NULL,
  `class_dir` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `classes`
--

INSERT INTO `classes` (`id`, `name`, `description`, `focus_mode`, `class_dir`) VALUES
(14, 'Lehrer', '', 0, 'files/classes/Lehrer'),
(16, 'Musterklasse', 'Musterbeschreibung der Musterklasse', 0, 'files/classes/Musterklasse'),
(22, 'Musterklasse 2', 'Musterbeschreibung der Musterklasse 2', 0, 'files/classes/Musterklasse_2');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `emails`
--

CREATE TABLE `emails` (
  `id` int(11) NOT NULL,
  `sender` varchar(100) NOT NULL,
  `receiver` varchar(100) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `subject` varchar(255) NOT NULL,
  `opened` int(1) NOT NULL,
  `text` text NOT NULL,
  `class` varchar(45) NOT NULL,
  `folder` varchar(5) NOT NULL,
  `viewer` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `emails`
--

INSERT INTO `emails` (`id`, `sender`, `receiver`, `date`, `subject`, `opened`, `text`, `class`, `folder`, `viewer`) VALUES
(22, '753', '757', '2023-12-03 17:45:20', 'Testnachricht', 1, 'Mustertext\r\nMustertext\r\nMustertext\r\nMustertext\r\nMustertext\r\nMustertext\r\nMustertext\r\nMustertext\r\nMustertext\r\nMustertext\r\nMustertext\r\nMuster', 'Musterklasse', 'inbox', 'receiver'),
(23, '753', '757', '2023-12-03 17:45:20', 'Testnachricht', 1, 'Mustertext\r\nMustertext\r\nMustertext\r\nMustertext\r\nMustertext\r\nMustertext\r\nMustertext\r\nMustertext\r\nMustertext\r\nMustertext\r\nMustertext\r\nMuster', 'Musterklasse', 'sent', 'sender');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `exams`
--

CREATE TABLE `exams` (
  `id` int(11) NOT NULL,
  `exam_content_id` int(10) DEFAULT NULL,
  `started_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `finished_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `started_by` int(10) DEFAULT NULL,
  `class` int(10) DEFAULT NULL,
  `reviewed` int(1) DEFAULT 0,
  `token` tinytext NOT NULL,
  `title` varchar(200) NOT NULL,
  `status` int(1) NOT NULL DEFAULT 0,
  `planned_by` int(10) DEFAULT NULL,
  `countdown_started_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `planned_for` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `answer_json` varchar(15000) DEFAULT NULL,
  `exam_redirect` int(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `exams`
--

INSERT INTO `exams` (`id`, `exam_content_id`, `started_at`, `finished_at`, `started_by`, `class`, `reviewed`, `token`, `title`, `status`, `planned_by`, `countdown_started_at`, `planned_for`, `answer_json`, `exam_redirect`) VALUES
(31, 6, '2023-11-21 23:27:09', '2023-11-27 21:11:19', 753, 15, 0, 'GEJZIdDeCjRqX56FNoBTU40ntmxV13', 'ecedqw', 2, 753, '2023-11-21 23:27:09', '2023-11-23 11:00:00', '{\"751\":{\"details\":{\"completed_at\":\"2023-11-22 00:27:18\",\"tasks_completed\":1,\"final_grade\":\"0\",\"final_comment\":\"0\"},\"answers\":{\"answer_for_task\":[1],\"type\":[\"theorie\"],\"answer_text\":[\"files\\/exams\\/ecedqw\\/SCooper\\/theorie.txt\"],\"answered_at\":[\"2023-11-22 00:27:18\"],\"time_needed\":[\"00:00:09\"],\"forced_submission\":[\"0\"]}}}', 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `exam_activities`
--

CREATE TABLE `exam_activities` (
  `id` int(11) NOT NULL,
  `exam` int(10) DEFAULT NULL,
  `typ` int(2) DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `title` varchar(200) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `user` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `exam_activities`
--

INSERT INTO `exam_activities` (`id`, `exam`, `typ`, `time`, `title`, `message`, `user`) VALUES
(35, 31, 1, '2023-11-21 23:27:00', 'Cooper, Sheldon hat die Prüfung betreten.', 'Der Benutzer Cooper, Sheldon hat die Prüfung betreten.', 751);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `exam_content`
--

CREATE TABLE `exam_content` (
  `id` int(11) NOT NULL,
  `created_by` int(10) DEFAULT NULL,
  `title` text DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `json_content` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `exam_content`
--

INSERT INTO `exam_content` (`id`, `created_by`, `title`, `comment`, `json_content`) VALUES
(6, 753, '1. Klausur - INF Rommel 2024', 'Themen: Definition \"Rekursive Funktion\", Programmierung Palindrom Checker, Struktogramm', '{\"settings\":{\"title\":\"1. Klausur im Fach Informatik\",\"message\":\"Liebe Schüler, \\nihr habt nun 45 Minuten Zeit die folgenden Aufgaben zu erledigen. Bitte achtet auf die Zeit und geht eure Aufgaben nacheinander an. \\nMaximale Erfolge!\\n\\nHerr Rommel\",\"exact_order\":true,\"time\":45},\"tasks\":[{\"name\":\"Definiere den Begriff \\\"Rekursive Funktion\\\".\",\"type\":\"theorie\",\"description\":\"Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.1\",\"vof\":false},{\"name\":\"Programmiere einen Palindrom Checker in Python.\",\"type\":\"praxis\",\"description\":\"Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.2\",\"vof\":true}]}'),
(8, 753, '2. Klassenarbeit - 10/3', 'Themen: bla bla', '{\"settings\":{\"title\":\"2. Klassenarbeit\",\"message\":\"Viel Erfolg\",\"exact_order\":true,\"time\":30},\"tasks\":[{\"name\":\"Definiere den Begriff \\\"Rekursive Funktionen\\\"\",\"type\":\"theorie\",\"description\":\"g gwe g wer ger erw gerg erg\",\"vof\":false},{\"name\":\"Programmiere ein Palindrom Checker\",\"type\":\"praxis\",\"description\":\"fggerg\",\"vof\":true}]}');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `features`
--

CREATE TABLE `features` (
  `id` int(11) NOT NULL,
  `feature_name` varchar(100) NOT NULL,
  `short` varchar(10) NOT NULL,
  `feature_description` text NOT NULL,
  `feature_status` int(1) NOT NULL,
  `school_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `features`
--

INSERT INTO `features` (`id`, `feature_name`, `short`, `feature_description`, `feature_status`, `school_id`) VALUES
(1, 'Mitteilungs Funktion', 'mit_funk', 'Ermöglicht es Lehrern Mitteilungen an eine Klasse zu versenden', 1, 'sgb'),
(2, 'Email Funktion', 'mail_funk', 'Ermöglicht es Lehrern und Schülern gegenseitig Nachrichten zu schreiben', 1, 'sgb'),
(3, 'ToDo Funktion', 'todo_funk', 'Ermöglicht es Lehrern einzelnen Schülern oder ganzen Klassen Aufgaben zu erteilen', 1, 'sgb'),
(4, 'Zuordnungs Funktion', 'zuord_funk', 'Ermöglicht es Administratoren einem Lehrer / Lehrerin nur auf bestimmte Klassen Zugriff zu erteilen', 1, 'sgb'),
(6, 'Lernspiel Funktion', 'game_funk', 'Ermöglicht es Lehrern Spiel-Sessions für Lernspiele zu erstellen, um den Schülern spielerisch das Programmieren beizubringen.', 1, 'sgb');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `games`
--

CREATE TABLE `games` (
  `id` int(3) NOT NULL,
  `status` int(1) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `games`
--

INSERT INTO `games` (`id`, `status`, `name`, `description`) VALUES
(1, 1, 'CSS Dinner', 'Ein Spiel, um CSS-Selektoren zu lernen und zu üben.'),
(2, 1, 'ElevatorSaga', 'In diesem Spiel muss der Spieler mit Javascript einen Aufzug programmieren.'),
(3, 1, 'Flexbox Froggy', 'Training von Flexbox CSS Parametern.'),
(4, 1, 'SQL Murder Mysteries', 'Hierbei muss der Schüler mit Hilfe von SQL Statements einen Mordfall lösen.'),
(5, 1, 'The Aviator', 'Entwicklung von Algorithmen mit Hilfe von (Scratch-)Bausteinen.\r\n'),
(6, 1, 'Grid Garden', 'Training von Grid CSS Parametern.'),
(7, 1, 'Bitburner', 'Eine Hacking Simulation zum erlernen von JavaScript. <br/><span style=\"color: red;\">WARNUNG: Spielstände unbedingt sichern! Mitnehmen des Spielstandes nur manuell möglich!</span>');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `game_sessions`
--

CREATE TABLE `game_sessions` (
  `id` int(11) NOT NULL,
  `game_id` int(3) NOT NULL,
  `token` varchar(32) NOT NULL,
  `session_name` varchar(250) NOT NULL,
  `requests` int(10) NOT NULL DEFAULT 0,
  `status` int(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL,
  `creator` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `invitations`
--

CREATE TABLE `invitations` (
  `id` int(11) NOT NULL,
  `teacher` int(5) NOT NULL,
  `student` int(5) NOT NULL,
  `token` int(32) NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `last_logins`
--

CREATE TABLE `last_logins` (
  `id` int(11) NOT NULL,
  `user` int(5) NOT NULL,
  `browser` varchar(100) NOT NULL,
  `device` varchar(100) NOT NULL,
  `success` int(1) NOT NULL,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `last_logins`
--

INSERT INTO `last_logins` (`id`, `user`, `browser`, `device`, `success`, `login_time`) VALUES
(37, 751, 'Firefox auf Windows', 'Windows', 1, '2023-07-14 07:51:35'),
(38, 751, 'Firefox auf Windows', 'Windows', 1, '2023-07-14 08:33:04'),
(39, 751, 'Firefox auf Windows', 'Windows', 1, '2023-07-14 12:22:06'),
(40, 751, 'Firefox auf Windows', 'Windows', 1, '2023-07-14 12:23:10'),
(41, 751, 'Firefox auf Windows', 'Windows', 1, '2023-07-14 12:26:28'),
(42, 751, 'Firefox auf Windows', 'Windows', 1, '2023-07-14 12:27:14'),
(43, 753, 'Firefox auf Windows', 'Windows', 1, '2023-07-14 14:49:01'),
(44, 751, 'Firefox auf Windows', 'Windows', 1, '2023-07-14 14:49:13'),
(45, 751, 'Firefox auf Windows', 'Windows', 1, '2023-07-14 14:59:12'),
(46, 751, 'Firefox auf Windows', 'Windows', 1, '2023-07-14 15:08:20'),
(47, 751, 'Firefox auf Windows', 'Windows', 1, '2023-07-14 15:09:32'),
(48, 753, 'Firefox auf Windows', 'Windows', 1, '2023-07-14 15:18:19'),
(49, 751, 'Firefox auf Windows', 'Windows', 1, '2023-07-14 15:18:25'),
(50, 753, 'Firefox auf Windows', 'Windows', 1, '2023-07-14 15:18:37'),
(51, 751, 'Firefox auf Windows', 'Windows', 1, '2023-07-14 15:46:05'),
(52, 751, 'Firefox auf Windows', 'Windows', 1, '2023-07-14 16:04:01'),
(53, 751, 'Firefox auf Windows', 'Windows', 1, '2023-07-14 16:08:25'),
(54, 753, 'Firefox auf Android', 'Android', 1, '2023-07-15 07:16:08'),
(55, 751, 'Firefox auf Android', 'Android', 1, '2023-07-15 07:17:23'),
(56, 753, 'Firefox auf Windows', 'Windows', 1, '2023-07-16 17:51:11'),
(57, 751, 'Firefox auf Windows', 'Windows', 1, '2023-07-16 18:12:43'),
(58, 751, 'Firefox auf Windows', 'Windows', 1, '2023-07-16 18:57:54'),
(59, 751, 'Firefox auf Windows', 'Windows', 1, '2023-07-16 19:09:13'),
(60, 753, 'Firefox auf Windows', 'Windows', 0, '2023-07-17 07:07:41'),
(61, 753, 'Firefox auf Windows', 'Windows', 1, '2023-07-17 07:07:49'),
(62, 751, 'Firefox auf Windows', 'Windows', 1, '2023-07-17 07:10:43'),
(63, 751, 'Firefox auf Windows', 'Windows', 1, '2023-07-17 10:02:26'),
(64, 751, 'Firefox auf Windows', 'Windows', 1, '2023-07-17 10:07:12'),
(65, 751, 'Firefox auf Windows', 'Windows', 1, '2023-07-17 10:10:14'),
(66, 751, 'Firefox auf Windows', 'Windows', 1, '2023-07-17 10:11:39'),
(67, 751, 'Firefox auf Windows', 'Windows', 1, '2023-07-17 10:12:58'),
(68, 751, 'Firefox auf Windows', 'Windows', 1, '2023-07-17 10:52:45'),
(69, 751, 'Firefox auf Windows', 'Windows', 1, '2023-07-17 10:52:55'),
(70, 753, 'Chrome auf Windows', 'Windows', 0, '2023-11-09 15:22:28'),
(71, 753, 'Chrome auf Windows', 'Windows', 1, '2023-11-09 15:22:35'),
(72, 753, 'Chrome auf Windows', 'Windows', 1, '2023-11-09 15:27:49'),
(73, 751, 'Chrome auf Windows', 'Windows', 0, '2023-11-09 15:52:11'),
(74, 751, 'Chrome auf Windows', 'Windows', 1, '2023-11-09 15:52:16'),
(75, 753, 'Chrome auf Windows', 'Windows', 1, '2023-11-11 12:30:00'),
(76, 751, 'Chrome auf Windows', 'Windows', 1, '2023-11-11 12:30:53'),
(77, 753, 'Chrome auf Windows', 'Windows', 1, '2023-11-11 22:57:20'),
(78, 751, 'Chrome auf Windows', 'Windows', 1, '2023-11-11 23:00:27'),
(79, 753, 'Chrome auf Windows', 'Windows', 1, '2023-11-21 23:01:35'),
(80, 751, 'Chrome auf Windows', 'Windows', 1, '2023-11-21 23:25:05'),
(81, 753, 'Chrome auf Windows', 'Windows', 1, '2023-11-22 15:39:12'),
(82, 753, 'Chrome auf Windows', 'Windows', 1, '2023-11-26 20:28:12'),
(83, 753, 'Chrome auf Windows', 'Windows', 1, '2023-11-27 18:56:14'),
(84, 751, 'Chrome auf Windows', 'Windows', 1, '2023-11-27 21:11:07'),
(85, 753, 'Chrome auf Windows', 'Windows', 1, '2023-11-29 21:03:37'),
(86, 751, 'Chrome auf Windows', 'Windows', 1, '2023-11-29 21:18:24'),
(87, 753, 'Chrome auf Windows', 'Windows', 1, '2023-11-29 22:29:10'),
(88, 753, 'Chrome auf Windows', 'Windows', 1, '2023-12-03 15:19:07'),
(89, 757, 'Chrome auf Windows', 'Windows', 1, '2023-12-03 15:20:27'),
(90, 753, 'Chrome auf Windows', 'Windows', 1, '2023-12-03 15:30:53'),
(91, 757, 'Chrome auf Windows', 'Windows', 1, '2023-12-03 15:31:15'),
(92, 758, 'Chrome auf Windows', 'Windows', 1, '2023-12-03 15:33:00'),
(93, 753, 'Chrome auf Windows', 'Windows', 1, '2023-12-03 16:35:46'),
(94, 757, 'Chrome auf Windows', 'Windows', 0, '2023-12-03 16:40:15'),
(95, 757, 'Chrome auf Windows', 'Windows', 1, '2023-12-03 16:40:20'),
(96, 0, 'Chrome auf Windows', 'Windows', 0, '2023-12-03 19:45:07'),
(97, 0, 'Chrome auf Windows', 'Windows', 0, '2023-12-03 19:45:15'),
(98, 0, 'Chrome auf Windows', 'Windows', 0, '2023-12-03 19:45:26'),
(99, 0, 'Chrome auf Windows', 'Windows', 0, '2023-12-03 19:45:39'),
(100, 753, 'Chrome auf Windows', 'Windows', 1, '2023-12-03 19:46:05'),
(101, 0, 'Chrome auf Windows', 'Windows', 0, '2024-01-04 16:50:08'),
(102, 0, 'Chrome auf Windows', 'Windows', 0, '2024-01-04 16:50:14'),
(103, 0, 'Chrome auf Windows', 'Windows', 0, '2024-01-04 16:50:37'),
(104, 0, 'Chrome auf Windows', 'Windows', 0, '2024-01-04 16:50:48'),
(105, 753, 'Chrome auf Windows', 'Windows', 1, '2024-01-04 16:51:03'),
(106, 0, 'Chrome auf Windows', 'Windows', 0, '2024-01-04 20:15:27'),
(107, 757, 'Chrome auf Windows', 'Windows', 1, '2024-01-04 20:15:51');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `text` text NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `logs`
--

INSERT INTO `logs` (`id`, `text`, `date`) VALUES
(1, 'Müller, Elias hat das Systemprotokoll geleert.', '2023-11-29 23:29:49'),
(2, 'Müller, Elias hat sich ausgeloggt.', '2023-11-29 23:29:51'),
(3, 'Müller, Elias hat sich im Benutzerportal angemeldet.', '2023-12-03 16:19:07'),
(4, 'Müller, Elias hat eine neue Klasse mit dem Namen \"Musterklasse\" erstellt.', '2023-12-03 16:19:42'),
(5, 'Müller, Elias hat einen neuen Benutzer mit dem Namen \"Max Mustermann\" erstellt.', '2023-12-03 16:20:14'),
(6, 'Müller, Elias hat sich ausgeloggt.', '2023-12-03 16:20:17'),
(7, 'Mustermann, Max hat sich im Benutzerportal angemeldet.', '2023-12-03 16:20:27'),
(8, 'Mustermann, Max hat sich das erste mal angemeldet, die Nutzungsbedingungen und Datenschutzrichtlinien akzeptiert und das Passwort geändert.', '2023-12-03 16:21:53'),
(9, 'Mustermann, Max hat sich ausgeloggt.', '2023-12-03 16:30:48'),
(10, 'Müller, Elias hat sich im Benutzerportal angemeldet.', '2023-12-03 16:30:53'),
(11, 'Mustermann, Max hat sich im Benutzerportal angemeldet.', '2023-12-03 16:31:15'),
(12, 'Müller, Elias hat einen neuen Benutzer mit dem Namen \"Muster Lehrer\" erstellt.', '2023-12-03 16:32:25'),
(13, 'Müller, Elias hat sich ausgeloggt.', '2023-12-03 16:32:54'),
(14, 'Lehrer, Muster hat sich im Benutzerportal angemeldet.', '2023-12-03 16:33:00'),
(15, 'Lehrer, Muster hat sich das erste mal angemeldet, die Nutzungsbedingungen und Datenschutzrichtlinien akzeptiert und das Passwort geändert.', '2023-12-03 16:33:07'),
(16, 'Lehrer, Muster hat sich ausgeloggt.', '2023-12-03 17:35:44'),
(17, 'Müller, Elias hat sich im Benutzerportal angemeldet.', '2023-12-03 17:35:46'),
(18, 'Müller, Elias hat den Benutzer mit dem Namen \"Muster Administrator\" bearbeitet.', '2023-12-03 17:36:04'),
(19, 'Mustermann, Max hat sich ausgeloggt.', '2023-12-03 17:40:11'),
(20, 'Jemand hat sich versucht mit dem Benutzernamen \"MMustermann\" im Benutzerportal einzuloggen.', '2023-12-03 17:40:15'),
(21, 'Mustermann, Max hat sich im Benutzerportal angemeldet.', '2023-12-03 17:40:20'),
(22, 'Administrator, Muster hat eine neue Direktnachricht mit dem Betreff \"Testnachricht\" verschickt.', '2023-12-03 17:45:20'),
(23, 'Administrator, Muster hat eine neue Nachricht mit dem Titel \"Testmitteilung\" erstellt.', '2023-12-03 17:48:29'),
(24, 'Administrator, Muster hat sich ausgeloggt.', '2023-12-03 20:43:47'),
(25, 'Mustermann, Max hat sich ausgeloggt.', '2023-12-03 20:44:36'),
(26, 'Jemand hat sich versucht mit dem Benutzernamen \"EMueller\" im Benutzerportal einzuloggen.', '2023-12-03 20:45:07'),
(27, 'Jemand hat sich versucht mit dem Benutzernamen \"EMueller\" im Benutzerportal einzuloggen.', '2023-12-03 20:45:15'),
(28, 'Jemand hat sich versucht mit dem Benutzernamen \"MAdministrator\" im Benutzerportal einzuloggen.', '2023-12-03 20:45:26'),
(29, 'Jemand hat sich versucht mit dem Benutzernamen \"MAdministrator\" im Benutzerportal einzuloggen.', '2023-12-03 20:45:39'),
(30, 'Administrator, Muster hat sich im Benutzerportal angemeldet.', '2023-12-03 20:46:05'),
(31, 'Administrator, Muster hat sich ausgeloggt.', '2023-12-03 20:47:07'),
(32, 'Jemand hat sich versucht mit dem Benutzernamen \"EMueller\" im Benutzerportal einzuloggen.', '2024-01-04 17:50:08'),
(33, 'Jemand hat sich versucht mit dem Benutzernamen \"SCooper\" im Benutzerportal einzuloggen.', '2024-01-04 17:50:14'),
(34, 'Jemand hat sich versucht mit dem Benutzernamen \"MAdministrator\" im Benutzerportal einzuloggen.', '2024-01-04 17:50:37'),
(35, 'Jemand hat sich versucht mit dem Benutzernamen \"MAdministrator\" im Benutzerportal einzuloggen.', '2024-01-04 17:50:48'),
(36, 'Administrator, Muster hat sich im Benutzerportal angemeldet.', '2024-01-04 17:51:03'),
(37, 'Administrator, Muster hat einen neuen Benutzer mit dem Namen \"Hanna Berg\" erstellt.', '2024-01-04 17:54:39'),
(38, 'Administrator, Muster hat den Benutzer \"Berg, Hanna\" gelöscht.', '2024-01-04 17:54:49'),
(39, 'Administrator, Muster hat einen neuen Benutzer mit dem Namen \"Hanna Berg\" erstellt.', '2024-01-04 17:55:11'),
(40, 'Administrator, Muster hat den Benutzer \"Berg, Hanna\" gelöscht.', '2024-01-04 17:55:44'),
(41, 'Administrator, Muster hat einen neuen Benutzer mit dem Namen \"Hanna Berg\" erstellt.', '2024-01-04 18:32:38'),
(42, 'Administrator, Muster hat einen neuen Benutzer mit dem Namen \"Andreas Guro\" erstellt.', '2024-01-04 18:32:38'),
(43, 'Administrator, Muster hat einen neuen Benutzer mit dem Namen \"Samantha Smith\" erstellt.', '2024-01-04 18:32:38'),
(44, 'Administrator, Muster hat einen neuen Benutzer mit dem Namen \"Günter Jauch\" erstellt.', '2024-01-04 18:32:38'),
(45, 'Administrator, Muster hat den Benutzer \"Berg, Hanna\" gelöscht.', '2024-01-04 18:32:46'),
(46, 'Administrator, Muster hat den Benutzer \"Jauch, Günter\" gelöscht.', '2024-01-04 18:32:49'),
(47, 'Administrator, Muster hat den Benutzer \"Smith, Samantha\" gelöscht.', '2024-01-04 18:32:53'),
(48, 'Administrator, Muster hat den Benutzer \"Guro, Andreas\" gelöscht.', '2024-01-04 18:32:55'),
(49, 'Administrator, Muster hat einen neuen Benutzer mit dem Namen \"Hanna Berg\" erstellt.', '2024-01-04 18:33:33'),
(50, 'Administrator, Muster hat einen neuen Benutzer mit dem Namen \"Andreas Guro\" erstellt.', '2024-01-04 18:33:33'),
(51, 'Administrator, Muster hat einen neuen Benutzer mit dem Namen \"Samantha Smith\" erstellt.', '2024-01-04 18:33:33'),
(52, 'Administrator, Muster hat einen neuen Benutzer mit dem Namen \"Günter Jauch\" erstellt.', '2024-01-04 18:33:33'),
(53, 'Administrator, Muster hat den Benutzer \"Berg, Hanna\" gelöscht.', '2024-01-04 18:35:08'),
(54, 'Administrator, Muster hat den Benutzer \"Guro, Andreas\" gelöscht.', '2024-01-04 18:35:10'),
(55, 'Administrator, Muster hat den Benutzer \"Jauch, Günter\" gelöscht.', '2024-01-04 18:35:12'),
(56, 'Administrator, Muster hat den Benutzer \"Smith, Samantha\" gelöscht.', '2024-01-04 18:35:15'),
(57, 'Administrator, Muster hat einen neuen Benutzer mit dem Namen \"Hanna Berg\" erstellt.', '2024-01-04 18:35:28'),
(58, 'Administrator, Muster hat einen neuen Benutzer mit dem Namen \"Andreas Guro\" erstellt.', '2024-01-04 18:35:28'),
(59, 'Administrator, Muster hat einen neuen Benutzer mit dem Namen \"Samantha Smith\" erstellt.', '2024-01-04 18:35:29'),
(60, 'Administrator, Muster hat einen neuen Benutzer mit dem Namen \"Günter Jauch\" erstellt.', '2024-01-04 18:35:29'),
(61, 'Administrator, Muster hat den Benutzer \"Berg, Hanna\" gelöscht.', '2024-01-04 18:35:47'),
(62, 'Administrator, Muster hat den Benutzer \"Guro, Andreas\" gelöscht.', '2024-01-04 18:35:49'),
(63, 'Administrator, Muster hat den Benutzer \"Jauch, Günter\" gelöscht.', '2024-01-04 18:35:51'),
(64, 'Administrator, Muster hat den Benutzer \"Smith, Samantha\" gelöscht.', '2024-01-04 18:35:53'),
(65, 'Administrator, Muster hat einen neuen Benutzer mit dem Namen \"Hanna Berg\" erstellt.', '2024-01-04 18:36:04'),
(66, 'Administrator, Muster hat einen neuen Benutzer mit dem Namen \"Andreas Guro\" erstellt.', '2024-01-04 18:36:04'),
(67, 'Administrator, Muster hat einen neuen Benutzer mit dem Namen \"Samantha Smith\" erstellt.', '2024-01-04 18:36:05'),
(68, 'Administrator, Muster hat einen neuen Benutzer mit dem Namen \"Günter Jauch\" erstellt.', '2024-01-04 18:36:05'),
(69, 'Administrator, Muster hat den Benutzer \"Berg, Hanna\" gelöscht.', '2024-01-04 18:39:18'),
(70, 'Administrator, Muster hat den Benutzer \"Guro, Andreas\" gelöscht.', '2024-01-04 18:39:21'),
(71, 'Administrator, Muster hat den Benutzer \"Jauch, Günter\" gelöscht.', '2024-01-04 18:39:24'),
(72, 'Administrator, Muster hat den Benutzer \"Smith, Samantha\" gelöscht.', '2024-01-04 18:39:27'),
(73, 'Administrator, Muster hat einen neuen Benutzer mit dem Namen \"Hanna Berg\" erstellt.', '2024-01-04 18:39:41'),
(74, 'Administrator, Muster hat einen neuen Benutzer mit dem Namen \"Andreas Guro\" erstellt.', '2024-01-04 18:39:41'),
(75, 'Administrator, Muster hat einen neuen Benutzer mit dem Namen \"Samantha Smith\" erstellt.', '2024-01-04 18:39:41'),
(76, 'Administrator, Muster hat einen neuen Benutzer mit dem Namen \"Günter Jauch\" erstellt.', '2024-01-04 18:39:41'),
(77, 'Administrator, Muster hat eine neue Klasse mit dem Namen \"Nschwarz\" erstellt.', '2024-01-04 18:40:40'),
(78, 'Administrator, Muster hat einen neuen Benutzer mit dem Namen \"Nick Schwarz\" erstellt.', '2024-01-04 18:40:40'),
(79, 'Administrator, Muster hat eine neue Klasse mit dem Namen \"Nschwarz\" erstellt.', '2024-01-04 18:40:55'),
(80, 'Administrator, Muster hat einen neuen Benutzer mit dem Namen \"Nick Schwarz\" erstellt.', '2024-01-04 18:40:55'),
(81, 'Administrator, Muster hat eine neue Klasse mit dem Namen \"Nschwarz\" erstellt.', '2024-01-04 18:43:41'),
(82, 'Administrator, Muster hat einen neuen Benutzer mit dem Namen \"Nick Schwarz\" erstellt.', '2024-01-04 18:43:41'),
(83, 'Administrator, Muster hat den Benutzer \"Berg, Hanna\" gelöscht.', '2024-01-04 18:43:55'),
(84, 'Administrator, Muster hat den Benutzer \"Guro, Andreas\" gelöscht.', '2024-01-04 18:43:58'),
(85, 'Administrator, Muster hat den Benutzer \"Schwarz, Nick\" gelöscht.', '2024-01-04 18:44:09'),
(86, 'Administrator, Muster hat den Benutzer \"Schwarz, Nick\" gelöscht.', '2024-01-04 18:44:12'),
(87, 'Administrator, Muster hat den Benutzer \"Schwarz, Nick\" gelöscht.', '2024-01-04 18:44:14'),
(88, 'Administrator, Muster hat eine neue Klasse mit dem Namen \"Nschwarz\" erstellt.', '2024-01-04 18:45:05'),
(89, 'Administrator, Muster hat einen neuen Benutzer mit dem Namen \"Nick Schwarz\" erstellt.', '2024-01-04 18:45:05'),
(90, 'Administrator, Muster hat den Benutzer \"Schwarz, Nick\" gelöscht.', '2024-01-04 18:47:03'),
(91, 'Administrator, Muster hat eine neue Klasse mit dem Namen \"TheaterAG\" erstellt.', '2024-01-04 18:49:11'),
(92, 'Administrator, Muster hat einen neuen Benutzer mit dem Namen \"Nick Schwarz\" erstellt.', '2024-01-04 18:49:11'),
(93, 'Jemand hat sich versucht mit dem Benutzernamen \"MSchueler\" im Benutzerportal einzuloggen.', '2024-01-04 21:15:27'),
(94, 'Administrator, Muster hat den Benutzer \"Jauch, Günter\" gelöscht.', '2024-01-04 21:15:34'),
(95, 'Administrator, Muster hat den Benutzer \"Schwarz, Nick\" gelöscht.', '2024-01-04 21:15:37'),
(96, 'Administrator, Muster hat den Benutzer \"Smith, Samantha\" gelöscht.', '2024-01-04 21:15:39'),
(97, 'Mustermann, Max hat sich im Benutzerportal angemeldet.', '2024-01-04 21:15:51'),
(98, 'Administrator, Muster hat eine neue Klasse mit dem Namen \"Musterklasse 2\" erstellt.', '2024-01-04 22:13:09');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `sender` varchar(100) NOT NULL,
  `receiver` varchar(100) NOT NULL,
  `class` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `messages`
--

INSERT INTO `messages` (`id`, `title`, `text`, `date`, `sender`, `receiver`, `class`) VALUES
(9, 'Testmitteilung', 'Mustermitteilung\r\nMustermitteilung\r\nMustermitteilung', '2023-12-03 17:48:29', 'Administrator, Muster', 'Musterklasse', 'Musterklasse');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user` varchar(255) NOT NULL,
  `heading` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `notifications`
--

INSERT INTO `notifications` (`id`, `user`, `heading`, `text`, `date`, `status`) VALUES
(26, '757', 'Du hast eine neue Direktnachricht! ✉', 'Schau in deinem Postfach nach!', '2023-12-03 17:45:20', 1),
(27, 'Musterklasse', 'Du hast eine neue Mitteilung! 🗪', 'Schau im Mitteilungsbereich nach!', '2023-12-03 17:48:29', 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `owner` int(6) DEFAULT NULL,
  `shared` int(1) DEFAULT NULL,
  `completed` int(1) DEFAULT 0,
  `submitted` int(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `submitted_at` datetime DEFAULT NULL,
  `return_at` datetime DEFAULT NULL,
  `return_grade` varchar(100) DEFAULT NULL,
  `return_note` text DEFAULT NULL,
  `return_from` int(6) DEFAULT NULL,
  `reviewed` int(1) DEFAULT NULL,
  `project_content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `category` tinytext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `projects`
--

INSERT INTO `projects` (`id`, `name`, `description`, `owner`, `shared`, `completed`, `submitted`, `created_at`, `submitted_at`, `return_at`, `return_grade`, `return_note`, `return_from`, `reviewed`, `project_content`, `category`) VALUES
(82, 'Essensmenü', 'Stundenaufgabe von Frau Musterfrau', 757, 0, 1, 1, '2023-12-03 15:28:32', '2023-12-03 16:29:49', '2023-12-03 17:34:48', '13', '- Verwende keine Umlaute in Variablennamen\r\n- Schreibe deinen Code leserlich und verwende Leerzeilen zur besseren Strukturierung', 758, 1, '%7B%22identifier%22%3A82%2C%22project_type%22%3A%22python%22%2C%22name%22%3A%22Essensmen%5Cu00fc%22%2C%22locale%22%3Anull%2C%22components%22%3A%5B%7B%22extension%22%3A%22py%22%2C%22name%22%3A%22main%22%2C%22content%22%3A%22%22%2C%22default%22%3Atrue%7D%5D%2C%22image_list%22%3A%5B%5D%2C%22to_review%22%3Afalse%7D', 'python'),
(83, 'Neues Projekt', 'Neues über Editor erstelltes Projekt.', NULL, 0, 0, 0, '2024-01-04 20:14:49', NULL, NULL, NULL, NULL, NULL, 0, '%7B%22identifier%22%3A%22new%22%2C%22project_type%22%3A%22python%22%2C%22name%22%3A%22Neues%20Projekt%22%2C%22locale%22%3Anull%2C%22components%22%3A%5B%7B%22extension%22%3A%22py%22%2C%22name%22%3A%22main%22%2C%22content%22%3A%22%22%2C%22default%22%3Atrue%7D%5D%2C%22image_list%22%3A%5B%5D%2C%22to_review%22%3Afalse%7D', 'python'),
(84, 'Neues Projekt', 'Neues über Editor erstelltes Projekt.', NULL, 0, 0, 0, '2024-01-04 20:14:52', NULL, NULL, NULL, NULL, NULL, 0, '%7B%22identifier%22%3A%22new%22%2C%22project_type%22%3A%22python%22%2C%22name%22%3A%22Neues%20Projekt%22%2C%22locale%22%3Anull%2C%22components%22%3A%5B%7B%22extension%22%3A%22py%22%2C%22name%22%3A%22main%22%2C%22content%22%3A%22%22%2C%22default%22%3Atrue%7D%5D%2C%22image_list%22%3A%5B%5D%2C%22to_review%22%3Afalse%7D', 'python'),
(85, 'Neues Projekt', 'Neues über Editor erstelltes Projekt.', NULL, 0, 0, 0, '2024-01-04 20:14:54', NULL, NULL, NULL, NULL, NULL, 0, '%7B%22identifier%22%3A%22new%22%2C%22project_type%22%3A%22python%22%2C%22name%22%3A%22Neues%20Projekt%22%2C%22locale%22%3Anull%2C%22components%22%3A%5B%7B%22extension%22%3A%22py%22%2C%22name%22%3A%22main%22%2C%22content%22%3A%22%22%2C%22default%22%3Atrue%7D%5D%2C%22image_list%22%3A%5B%5D%2C%22to_review%22%3Afalse%7D', 'python'),
(86, 'Neues Projekt', 'Neues über Editor erstelltes Projekt.', NULL, 0, 0, 0, '2024-01-04 20:15:04', NULL, NULL, NULL, NULL, NULL, 0, '%7B%22identifier%22%3A%22new%22%2C%22project_type%22%3A%22python%22%2C%22name%22%3A%22Neues%20Projekt%22%2C%22locale%22%3Anull%2C%22components%22%3A%5B%7B%22extension%22%3A%22py%22%2C%22name%22%3A%22main%22%2C%22content%22%3A%22%22%2C%22default%22%3Atrue%7D%5D%2C%22image_list%22%3A%5B%5D%2C%22to_review%22%3Afalse%7D', 'python'),
(87, 'Neues Projekt', 'Neues über Editor erstelltes Projekt.', NULL, 0, 0, 0, '2024-01-04 20:15:12', NULL, NULL, NULL, NULL, NULL, 0, '%7B%22identifier%22%3A%22new%22%2C%22project_type%22%3A%22python%22%2C%22name%22%3A%22Neues%20Projekt%22%2C%22locale%22%3Anull%2C%22components%22%3A%5B%7B%22extension%22%3A%22py%22%2C%22name%22%3A%22main%22%2C%22content%22%3A%22%22%2C%22default%22%3Atrue%7D%5D%2C%22image_list%22%3A%5B%5D%2C%22to_review%22%3Afalse%7D', 'python');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `allow_login` int(11) NOT NULL DEFAULT 1,
  `connect_repo` int(1) NOT NULL,
  `institution_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `settings`
--

INSERT INTO `settings` (`id`, `allow_login`, `connect_repo`, `institution_name`) VALUES
(1, 1, 0, 'Schiller-Gymnasium Bautzen');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `todos`
--

CREATE TABLE `todos` (
  `id` int(11) NOT NULL,
  `sender` varchar(120) NOT NULL,
  `receiver` varchar(120) NOT NULL,
  `title` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `creation_date` datetime NOT NULL DEFAULT current_timestamp(),
  `due_date` datetime NOT NULL,
  `class` varchar(50) NOT NULL,
  `folder` varchar(25) NOT NULL DEFAULT 'inbox'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `secondName` varchar(255) NOT NULL,
  `avatar` int(11) NOT NULL DEFAULT 9,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  `class` varchar(100) NOT NULL,
  `institution` varchar(100) NOT NULL,
  `status` int(1) NOT NULL,
  `focus_mode` int(1) NOT NULL,
  `user_dir` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_logout` datetime DEFAULT NULL,
  `agb_confirmed` int(11) DEFAULT NULL,
  `agb_confirmed_at` datetime DEFAULT NULL,
  `in_exam` int(2) NOT NULL DEFAULT 0,
  `exam_id` int(6) NOT NULL DEFAULT 0,
  `exam_redirect` int(1) DEFAULT 0,
  `exam_join_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `firstName`, `secondName`, `avatar`, `username`, `password`, `role`, `class`, `institution`, `status`, `focus_mode`, `user_dir`, `created_at`, `last_logout`, `agb_confirmed`, `agb_confirmed_at`, `in_exam`, `exam_id`, `exam_redirect`, `exam_join_timestamp`) VALUES
(753, 'Muster', 'Administrator', 3, 'MAdminstrator', '$2y$10$cZMPbY6.KUNIS8X.Vt1mfOPSRyDjJBfK/JRmf0JWo7OyOQJ72qff2', 'Administrator', 'Lehrer', 'Schiller-Gymnasium Bautzen', 1, 0, 'files/users/EMueller', '2023-05-24 17:16:54', '2023-12-03 20:47:07', 1, '2023-05-24 17:18:28', 0, 0, 0, '0000-00-00 00:00:00'),
(757, 'Max', 'Mustermann', 9, 'MMustermann', '$2y$10$Sas9VWRfPs84HYlEttKlDuC63j.gbmH/YrfUw5m7Q0kKhjmsa0Uj6', 'Schüler', 'Musterklasse', 'Schiller-Gymnasium Bautzen', 1, 0, '', '2023-12-03 16:20:14', '2023-12-03 20:44:36', 1, '2023-12-03 16:21:53', 0, 0, 0, '0000-00-00 00:00:00'),
(758, 'Muster', 'Lehrer', 9, 'MLehrer', '$2y$10$mcqhAg5y3XQxkPXGvGxGCOz/BuxwWxb47zDzCsk9WOPLmzxY9vAVy', 'Lehrer', 'Lehrer', 'Schiller-Gymnasium Bautzen', 0, 0, '', '2023-12-03 16:32:25', '2023-12-03 17:35:44', 1, '2023-12-03 16:33:07', 0, 0, 0, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_tokens`
--

CREATE TABLE `user_tokens` (
  `id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `2fa`
--
ALTER TABLE `2fa`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `emails`
--
ALTER TABLE `emails`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `exam_activities`
--
ALTER TABLE `exam_activities`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `exam_content`
--
ALTER TABLE `exam_content`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `features`
--
ALTER TABLE `features`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `game_sessions`
--
ALTER TABLE `game_sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `invitations`
--
ALTER TABLE `invitations`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `last_logins`
--
ALTER TABLE `last_logins`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `todos`
--
ALTER TABLE `todos`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `2fa`
--
ALTER TABLE `2fa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT für Tabelle `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=180;

--
-- AUTO_INCREMENT für Tabelle `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT für Tabelle `emails`
--
ALTER TABLE `emails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT für Tabelle `exams`
--
ALTER TABLE `exams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT für Tabelle `exam_activities`
--
ALTER TABLE `exam_activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT für Tabelle `exam_content`
--
ALTER TABLE `exam_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT für Tabelle `features`
--
ALTER TABLE `features`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT für Tabelle `games`
--
ALTER TABLE `games`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT für Tabelle `game_sessions`
--
ALTER TABLE `game_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT für Tabelle `invitations`
--
ALTER TABLE `invitations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `last_logins`
--
ALTER TABLE `last_logins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT für Tabelle `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT für Tabelle `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT für Tabelle `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT für Tabelle `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT für Tabelle `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `todos`
--
ALTER TABLE `todos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=786;

--
-- AUTO_INCREMENT für Tabelle `user_tokens`
--
ALTER TABLE `user_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
