-- phpMyAdmin SQL Dump
-- version 4.1.13
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vytvořeno: Úte 29. dub 2014, 13:46
-- Verze serveru: 5.5.34
-- Verze PHP: 5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databáze: `lpolak`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `games`
--

CREATE TABLE IF NOT EXISTS `games` (
  `game_id` int(11) NOT NULL AUTO_INCREMENT,
  `game_turn` int(11) NOT NULL,
  `game_team` int(11) NOT NULL,
  `game_size` int(11) NOT NULL DEFAULT '3',
  `game_ended` int(11) NOT NULL DEFAULT '0',
  `game_created` datetime NOT NULL,
  PRIMARY KEY (`game_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=27 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `games_players_xref`
--

CREATE TABLE IF NOT EXISTS `games_players_xref` (
  `game_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `player_team` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`game_id`,`player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `moves`
--

CREATE TABLE IF NOT EXISTS `moves` (
  `move_id` int(11) NOT NULL AUTO_INCREMENT,
  `game_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `row` int(11) NOT NULL,
  `col` int(11) NOT NULL,
  `team` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `state` int(11) NOT NULL DEFAULT '0',
  `turn` int(11) NOT NULL,
  PRIMARY KEY (`move_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=143 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `players`
--

CREATE TABLE IF NOT EXISTS `players` (
  `player_id` int(11) NOT NULL AUTO_INCREMENT,
  `player_name` varchar(32) NOT NULL,
  `player_joined` datetime NOT NULL,
  PRIMARY KEY (`player_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=50 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
