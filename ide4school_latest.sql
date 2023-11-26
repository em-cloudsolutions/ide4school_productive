-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 26. Nov 2023 um 17:00
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
(148, 'SCooper', 'Informatik Rommel 2024', 1),
(149, 'SCooper', 'Lehrer', 1),
(150, '753', 'Informatik Rommel 2024', 1),
(151, '753', 'Lehrer', 1),
(152, '756', 'Testklasse XY', 1),
(153, '753', 'Testklasse XY', 1);

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
(13, 'Informatik Rommel 2024', 'Informatikgruppe Herr Rommel - Abijahrgang 2024', 0, 'files/classes/Informatik_Rommel_2024'),
(14, 'Lehrer', '', 0, 'files/classes/Lehrer'),
(15, 'Testklasse XY', 'fef', 0, 'files/classes/Testklasse_XY');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `compiler_langs`
--

CREATE TABLE `compiler_langs` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `language_id` int(11) NOT NULL,
  `mode` varchar(50) NOT NULL,
  `status` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `compiler_langs`
--

INSERT INTO `compiler_langs` (`id`, `name`, `language_id`, `mode`, `status`) VALUES
(1, 'Bash (5.0.0)', 46, 'shell', 0),
(2, 'C (Clang 7.0.1)', 75, 'c', 0),
(3, 'C (Clang 9.0.1)', 1013, 'c', 0),
(4, 'C (Clang 10.0.1)', 1001, 'c', 0),
(5, 'C (GCC 7.4.0)', 48, 'c', 0),
(6, 'C (GCC 8.3.0)', 49, 'c', 0),
(7, 'C (GCC 9.2.0)', 50, 'c', 1),
(8, 'C# (Mono 6.6.0.161)', 51, 'csharp', 0),
(9, 'C# (Mono 6.10.0.104)', 1022, 'csharp', 0),
(10, 'C# (.NET Core SDK 3.1.302)', 1021, 'csharp', 0),
(11, 'C# Test (.NET Core SDK 3.1.302, NUnit 3.12.0)', 1023, 'csharp', 0),
(12, 'C++ (Clang 7.0.1)', 76, 'cpp', 0),
(13, 'C++ (Clang 9.0.1)', 1014, 'cpp', 0),
(14, 'C++ (Clang 10.0.1)', 1002, 'cpp', 0),
(15, 'C++ (GCC 7.4.0)', 52, 'cpp', 0),
(16, 'C++ (GCC 8.3.0)', 53, 'cpp', 0),
(17, 'C++ (GCC 9.2.0)', 54, 'cpp', 1),
(18, 'C++ Test (Clang 10.0.1, Google Test 1.8.1)', 1015, 'cpp', 0),
(19, 'C++ Test (GCC 8.4.0, Google Test 1.8.1)', 1012, 'cpp', 0),
(20, 'C3 (latest)', 1003, 'c', 0),
(21, 'Clojure (1.10.1)', 86, 'clojure', 0),
(22, 'Executable', 44, 'plaintext', 0),
(23, 'F# (.NET Core SDK 3.1.202)', 87, 'fsharp', 0),
(24, 'F# (.NET Core SDK 3.1.302)', 1024, 'fsharp', 0),
(25, 'Go (1.13.5)', 60, 'go', 0),
(26, 'Java (OpenJDK 13.0.1)', 62, 'java', 0),
(27, 'Java (OpenJDK 14.0.1)', 1004, 'java', 1),
(28, 'Java Test (OpenJDK 14.0.1, JUnit Platform Console Standalone 1.6.2)', 1005, 'java', 0),
(29, 'JavaScript (Node.js 12.14.0)', 63, 'javascript', 1),
(30, 'Kotlin (1.3.70)', 78, 'kotlin', 0),
(31, 'Lua (5.3.5)', 64, 'lua', 0),
(32, 'MPI (OpenRTE 3.1.3) with C (GCC 8.3.0)', 1006, 'c', 0),
(33, 'MPI (OpenRTE 3.1.3) with C++ (GCC 8.3.0)', 1007, 'cpp', 0),
(34, 'MPI (OpenRTE 3.1.3) with Python (3.7.3)', 1008, 'python', 0),
(35, 'Nim (stable)', 1009, 'python', 0),
(36, 'Objective-C (Clang 7.0.1)', 79, 'objective-c', 0),
(37, 'Pascal (FPC 3.0.4)', 67, 'pascal', 0),
(38, 'Perl (5.28.1)', 85, 'perl', 0),
(39, 'PHP (7.4.1)', 68, 'php', 0),
(40, 'Plain Text', 43, 'plaintext', 0),
(41, 'Python (2.7.17)', 70, 'python', 0),
(42, 'Python (3.8.1)', 71, 'python', 1),
(43, 'Python for ML (3.7.3)', 1010, 'python', 0),
(44, 'R (4.0.0)', 80, 'r', 0),
(45, 'Ruby (2.7.0)', 72, 'ruby', 0),
(46, 'Rust (1.40.0)', 73, 'rust', 0),
(47, 'Swift (5.2.3)', 83, 'swift', 0),
(48, 'TypeScript (3.7.4)', 74, 'typescript', 0),
(49, 'Visual Basic.Net (vbnc 0.0.0.5943)', 84, 'vb', 0);

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
(31, 6, '2023-11-21 23:27:09', '2023-11-23 11:45:00', 753, 15, 0, 'GEJZIdDeCjRqX56FNoBTU40ntmxV13', 'ecedqw', 1, 753, '2023-11-21 23:27:09', '2023-11-23 11:00:00', '{\"751\":{\"details\":{\"completed_at\":\"2023-11-22 00:27:18\",\"tasks_completed\":1,\"final_grade\":\"0\",\"final_comment\":\"0\"},\"answers\":{\"answer_for_task\":[1],\"type\":[\"theorie\"],\"answer_text\":[\"files\\/exams\\/ecedqw\\/SCooper\\/theorie.txt\"],\"answered_at\":[\"2023-11-22 00:27:18\"],\"time_needed\":[\"00:00:09\"],\"forced_submission\":[\"0\"]}}}', 1);

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
(1, 'Mitteilungs Funktion', 'mit_funk', 'Ermöglicht es Lehrern Mitteilungen an eine Klasse zu versenden', 0, 'sgb'),
(2, 'Email Funktion', 'mail_funk', 'Ermöglicht es Lehrern und Schülern gegenseitig Nachrichten zu schreiben', 0, 'sgb'),
(3, 'ToDo Funktion', 'todo_funk', 'Ermöglicht es Lehrern einzelnen Schülern oder ganzen Klassen Aufgaben zu erteilen', 0, 'sgb'),
(4, 'Zuordnungs Funktion', 'zuord_funk', 'Ermöglicht es Administratoren einem Lehrer / Lehrerin nur auf bestimmte Klassen Zugriff zu erteilen', 1, 'sgb'),
(5, 'Abgaben / Rückgaben Funktion', 'abru_funk', 'Ermöglicht es Schülern ihre erledigten Arbeiten abzugeben und eine anschließend Rückgabe der korrigierten Arbeit zu erhalten', 1, 'sgb'),
(6, 'Lernspiel Funktion', 'game_funk', 'Ermöglicht es Lehrern Spiel-Sessions für Lernspiele zu erstellen, um den Schülern spielerisch das Programmieren beizubringen.', 0, 'sgb');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fido2_credentials`
--

CREATE TABLE `fido2_credentials` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `credential_id` text NOT NULL,
  `public_key` text NOT NULL,
  `sign_count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

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

--
-- Daten für Tabelle `game_sessions`
--

INSERT INTO `game_sessions` (`id`, `game_id`, `token`, `session_name`, `requests`, `status`, `created_at`, `creator`) VALUES
(60, 1, '4bd5502ab6432854ab45c8b1335d55fa', 'CSS Dinner', 6, 1, '2023-04-09 13:06:02', 'Cooper, Sheldon'),
(61, 7, '85eb923441d484818de4510443ac5aa8', 'Bitburner', 2, 1, '2023-05-24 18:04:10', 'Cooper, Sheldon');

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
(12, 751, 'Chrome auf Windows', 'Windows', 0, '2023-05-27 11:50:52'),
(13, 751, 'Chrome auf Windows', 'Windows', 1, '2023-05-27 11:51:38'),
(14, 751, 'Chrome auf Windows', 'Windows', 1, '2023-05-27 11:54:21'),
(15, 751, 'Firefox auf Windows', 'Windows', 1, '2023-05-27 11:56:06'),
(16, 751, 'Firefox auf Windows', 'Windows', 1, '2023-05-27 12:00:40'),
(17, 751, 'Firefox auf Windows', 'Windows', 1, '2023-05-27 14:15:12'),
(18, 751, 'Firefox auf Windows', 'Windows', 1, '2023-05-27 14:15:46'),
(19, 751, 'Firefox auf Windows', 'Windows', 1, '2023-06-14 06:07:20'),
(20, 755, 'Firefox auf Windows', 'Windows', 1, '2023-06-14 06:10:31'),
(21, 753, 'Firefox auf Windows', 'Windows', 0, '2023-07-06 12:36:57'),
(22, 751, 'Firefox auf Windows', 'Windows', 1, '2023-07-06 12:37:03'),
(23, 751, 'Firefox auf Windows', 'Windows', 1, '2023-07-06 12:40:42'),
(24, 751, 'Firefox auf Windows', 'Windows', 1, '2023-07-06 18:07:56'),
(25, 751, 'Firefox auf Windows', 'Windows', 1, '2023-07-10 11:45:39'),
(26, 753, 'Firefox auf Windows', 'Windows', 0, '2023-07-12 08:22:20'),
(27, 753, 'Firefox auf Windows', 'Windows', 0, '2023-07-12 08:22:26'),
(28, 753, 'Firefox auf Windows', 'Windows', 1, '2023-07-12 08:22:35'),
(29, 753, 'Firefox auf Windows', 'Windows', 1, '2023-07-12 13:47:32'),
(30, 753, 'Firefox auf Windows', 'Windows', 1, '2023-07-12 20:21:29'),
(31, 753, 'Firefox auf Windows', 'Windows', 1, '2023-07-12 21:33:18'),
(32, 753, 'Firefox auf Windows', 'Windows', 0, '2023-07-13 07:01:10'),
(33, 753, 'Firefox auf Windows', 'Windows', 1, '2023-07-13 07:01:16'),
(34, 753, 'Firefox auf Windows', 'Windows', 1, '2023-07-13 15:47:04'),
(35, 753, 'Firefox auf Windows', 'Windows', 0, '2023-07-14 07:26:29'),
(36, 753, 'Firefox auf Windows', 'Windows', 1, '2023-07-14 07:26:37'),
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
(81, 753, 'Chrome auf Windows', 'Windows', 1, '2023-11-22 15:39:12');

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
(1, 'Müller, Elias hat das Systemprotokoll geleert.', '2023-11-09 16:24:50'),
(2, 'Müller, Elias hat die Institution in \"Schiller-Gymnasium Bautzen\" umbennant.', '2023-11-09 16:25:04'),
(3, 'Müller, Elias hat sich ausgeloggt.', '2023-11-09 16:27:45'),
(4, 'Müller, Elias hat sich im Benutzerportal angemeldet.', '2023-11-09 16:27:49'),
(5, 'Jemand hat sich versucht mit dem Benutzernamen \"SCooper\" im Benutzerportal einzuloggen.', '2023-11-09 16:52:11'),
(6, 'Cooper, Sheldon hat sich im Benutzerportal angemeldet.', '2023-11-09 16:52:16'),
(7, 'Müller, Elias hat sich im Benutzerportal angemeldet.', '2023-11-11 13:30:00'),
(8, 'Müller, Elias hat eine neue Klasse mit dem Namen \"Testklasse XY\" erstellt.', '2023-11-11 13:30:30'),
(9, 'Müller, Elias hat den Benutzer mit dem Namen \"Sheldon Cooper\" bearbeitet.', '2023-11-11 13:30:40'),
(10, 'Cooper, Sheldon hat sich im Benutzerportal angemeldet.', '2023-11-11 13:30:53'),
(11, 'Müller, Elias hat sich im Benutzerportal angemeldet.', '2023-11-11 23:57:20'),
(12, 'Cooper, Sheldon hat sich im Benutzerportal angemeldet.', '2023-11-12 00:00:27'),
(13, 'Müller, Elias hat sich im Benutzerportal angemeldet.', '2023-11-22 00:01:35'),
(14, 'Cooper, Sheldon hat sich im Benutzerportal angemeldet.', '2023-11-22 00:25:05'),
(15, 'Müller, Elias hat sich ausgeloggt.', '2023-11-22 02:32:02'),
(16, 'Müller, Elias hat sich im Benutzerportal angemeldet.', '2023-11-22 16:39:12');

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
  `project_content` text DEFAULT NULL,
  `category` tinytext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `projects`
--

INSERT INTO `projects` (`id`, `name`, `description`, `owner`, `shared`, `completed`, `submitted`, `created_at`, `submitted_at`, `return_at`, `return_grade`, `return_note`, `return_from`, `reviewed`, `project_content`, `category`) VALUES
(22, 'forihgo', 'ofirjgoirj', 753, 0, 0, 0, '2023-11-21 23:23:25', NULL, NULL, NULL, NULL, NULL, 0, '{\"identifier\":\"22\",\"project_type\":\"python\",\"name\":\"AGB1\",\"locale\":null,\"components\":[{\"extension\":\"py\",\"name\":\"main\",\"content\":\"print()\",\"default\":true}],\"image_list\":[]}', 'python'),
(23, 'degewgwe', 'fewfewfew', 753, 1, 0, 0, '2023-11-21 23:23:34', NULL, NULL, NULL, NULL, NULL, 0, '{\"identifier\":\"blank-html-starter\",\"project_type\":\"html\",\"locale\":\"en\",\"name\":\"Schülerarbeit 1\",\"user_id\":null,\"components\":[{\"id\":\"e732f181-933f-4324-844a-c05cedd9c56c\",\"name\":\"index\",\"extension\":\"html\",\"content\":\"<p>Indexseite</p>\\n<a href=\\\"kontakt.html\\\">Zu Kontakt</a>\"},{\"id\":\"b06d109f-71e4-4227-8bce-fb67a9599381\",\"name\":\"styles\",\"extension\":\"css\",\"content\":\"\"},{\"name\":\"kontakt\",\"extension\":\"html\",\"content\":\"<p>Kontaktseite</p>\\n<a href=\\\"index.html\\\">Zu Index</a>\"},{\"name\":\"test\",\"extension\":\"html\",\"content\":\"moinsen\"}],\"image_list\":[]}', 'website');

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
-- Tabellenstruktur für Tabelle `submissions`
--

CREATE TABLE `submissions` (
  `id` int(11) NOT NULL,
  `owner` int(5) NOT NULL,
  `name` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` int(11) NOT NULL DEFAULT 0,
  `return_date` datetime DEFAULT NULL,
  `return_from` varchar(255) DEFAULT NULL,
  `return_grade` int(11) DEFAULT NULL,
  `return_comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(751, 'Sheldon', 'Cooper', 3, 'SCooper', '$2y$10$PJGy6/xRHVZCrLenic8/vOHCx/3I907J/w3L4PhtTqgIuQRMi7okG', 'Schüler', 'Testklasse XY', 'Schiller-Gymnasium Bautzen', 1, 0, 'files/users/SCooper', '2023-04-08 22:57:30', '2023-07-17 14:52:48', 1, '2023-04-09 00:57:55', 1, 31, 1, '2023-11-21 23:27:00'),
(753, 'Elias', 'Müller', 3, 'EMueller', '$2y$10$cZMPbY6.KUNIS8X.Vt1mfOPSRyDjJBfK/JRmf0JWo7OyOQJ72qff2', 'Administrator', 'Informatik Rommel 2024', 'Schiller-Gymnasium Bautzen', 1, 0, 'files/users/EMueller', '2023-05-24 17:16:54', '2023-11-22 02:32:02', 1, '2023-05-24 17:18:28', 0, 0, 0, '0000-00-00 00:00:00'),
(756, 'Anastasia', 'Ishchenko', 9, 'AIshchenko', '$2y$10$Xb//i3pxAP.RzaCPYkRHLup2y43QT8Fru7XrwBxPDiZFXuFUYTM9S', 'Administrator', 'Informatik Rommel 2024', 'Schiller-Gymnasium Bautzen', 0, 0, 'files/users/AIshchenko', '2023-07-06 22:08:16', NULL, NULL, NULL, 0, 0, 0, '0000-00-00 00:00:00');

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
-- Indizes für die Tabelle `compiler_langs`
--
ALTER TABLE `compiler_langs`
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
-- Indizes für die Tabelle `fido2_credentials`
--
ALTER TABLE `fido2_credentials`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `credential_id` (`credential_id`(255)) USING HASH,
  ADD KEY `user_id` (`user_id`);

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
-- Indizes für die Tabelle `submissions`
--
ALTER TABLE `submissions`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=154;

--
-- AUTO_INCREMENT für Tabelle `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT für Tabelle `compiler_langs`
--
ALTER TABLE `compiler_langs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT für Tabelle `emails`
--
ALTER TABLE `emails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

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
-- AUTO_INCREMENT für Tabelle `fido2_credentials`
--
ALTER TABLE `fido2_credentials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `games`
--
ALTER TABLE `games`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT für Tabelle `game_sessions`
--
ALTER TABLE `game_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT für Tabelle `invitations`
--
ALTER TABLE `invitations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `last_logins`
--
ALTER TABLE `last_logins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT für Tabelle `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT für Tabelle `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT für Tabelle `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT für Tabelle `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT für Tabelle `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `submissions`
--
ALTER TABLE `submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT für Tabelle `todos`
--
ALTER TABLE `todos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=757;

--
-- AUTO_INCREMENT für Tabelle `user_tokens`
--
ALTER TABLE `user_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `fido2_credentials`
--
ALTER TABLE `fido2_credentials`
  ADD CONSTRAINT `fido2_credentials_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
