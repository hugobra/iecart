-- Adminer 4.1.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `equipes`;
CREATE TABLE `equipes` (
  `idEquipe` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nomEquipe` varchar(255) NOT NULL,
  PRIMARY KEY (`idEquipe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `fonctions`;
CREATE TABLE `fonctions` (
  `idFonction` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nomFonction` varchar(255) NOT NULL,
  PRIMARY KEY (`idFonction`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `objectifsstage`;
CREATE TABLE `objectifsstage` (
  `idObjectif` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idStage` int(10) unsigned NOT NULL,
  `idFonction` int(10) unsigned NOT NULL,
  `nomObjectif` varchar(255) NOT NULL,
  PRIMARY KEY (`idObjectif`),
  KEY `idStage` (`idStage`),
  KEY `idFonction` (`idFonction`),
  CONSTRAINT `fk_objectif_stage` FOREIGN KEY (`idStage`) REFERENCES `stage` (`idStage`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_objectif_fonction` FOREIGN KEY (`idFonction`) REFERENCES `fonctions` (`idFonction`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `rangs`;
CREATE TABLE `rangs` (
  `idRang` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nomRang` varchar(255) NOT NULL,
  PRIMARY KEY (`idRang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `services`;
CREATE TABLE `services` (
  `idService` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nomService` varchar(255) NOT NULL,
  PRIMARY KEY (`idService`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tranches`;
CREATE TABLE `tranches` (
  `idTranche` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nomTranche` varchar(255) NOT NULL,
  PRIMARY KEY (`idTranche`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `nni` varchar(8) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `idRang` int(10) unsigned NOT NULL,
  `lastco` bigint(20),
  PRIMARY KEY (`nni`),
  KEY `idRang` (`idRang`),
  CONSTRAINT `fk_user_rang` FOREIGN KEY (`idRang`) REFERENCES `rangs` (`idRang`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `fap`;
CREATE TABLE `fap` (
  `idFAP` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idStage` int(10) unsigned NOT NULL,
  `idStagiaire` int(10) unsigned NOT NULL,
  `idSite` int(10) unsigned NOT NULL,
  `dateModif` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `codeAction` tinytext NOT NULL,
  `codeSession` tinytext NOT NULL,
  `dateCreation` date DEFAULT NULL,
  `Formateur` tinytext NOT NULL,
  `fonction` int(10) unsigned NOT NULL,
  `dateDebut` tinytext NOT NULL,
  `dateFin` tinytext NOT NULL,
  PRIMARY KEY (`idFAP`),
  KEY `idStage` (`idStage`),
  KEY `idStagiaire` (`idStagiaire`),
  KEY `idSite` (`idSite`),
  KEY `fonction` (`fonction`),
  CONSTRAINT `fapSite` FOREIGN KEY (`idSite`) REFERENCES `site` (`idSite`) ON UPDATE CASCADE,
  CONSTRAINT `fapStage` FOREIGN KEY (`idStage`) REFERENCES `stage` (`idStage`) ON UPDATE CASCADE,
  CONSTRAINT `fapStagiaire` FOREIGN KEY (`idStagiaire`) REFERENCES `stagiaire` (`idStagiaire`) ON UPDATE CASCADE,
  CONSTRAINT `fapFonction` FOREIGN KEY (`fonction`) REFERENCES `fonctions` (`idFonction`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `fonction`;
CREATE TABLE `fonction` (
  `idFonction` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idStagiaire` int(11) unsigned NOT NULL,
  `nom` int(11) unsigned NOT NULL,
  PRIMARY KEY (`idFonction`),
  KEY `idFonction` (`idFonction`),
  KEY `idStagiaire` (`idStagiaire`),
  KEY `nom` (`nom`),
  CONSTRAINT `fonctionStagiaire` FOREIGN KEY (`idStagiaire`) REFERENCES `stagiaire` (`idStagiaire`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fonctionNom` FOREIGN KEY (`nom`) REFERENCES `fonctions` (`idFonction`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `objectifs`;
CREATE TABLE `objectifs` (
  `idObjectif` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idFap` int(10) unsigned NOT NULL,
  `objectif` int(10) unsigned NOT NULL,
  `pointFort` text NOT NULL,
  `pointAAmeliorer` text NOT NULL,
  PRIMARY KEY (`idObjectif`),
  KEY `idFap` (`idFap`),
  KEY `objectif` (`objectif`),
  KEY `idStage` (`idFap`),
  CONSTRAINT `objectifFAP` FOREIGN KEY (`idFap`) REFERENCES `fap` (`idFAP`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `objectifObjstage` FOREIGN KEY (`objectif`) REFERENCES `objectifsstage` (`idObjectif`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `site`;
CREATE TABLE `site` (
  `idSite` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `motDePasse` tinytext NOT NULL,
  `nomSite` tinytext NOT NULL,
  PRIMARY KEY (`idSite`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `site` (`idSite`, `motDePasse`, `nomSite`) VALUES
(1,	'f1b9702d7ea511aa313e7f159cf8fd763c86611e',	'Golfech');

DROP TABLE IF EXISTS `stage`;
CREATE TABLE `stage` (
  `idStage` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idSite` int(10) unsigned NOT NULL,
  `date` date NOT NULL,
  `titre` tinytext NOT NULL,
  `duree` int(11) NOT NULL,
  `valide` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idStage`),
  KEY `idSite` (`idSite`),
  KEY `idStagiaire` (`idSite`),
  CONSTRAINT `stageSite` FOREIGN KEY (`idSite`) REFERENCES `site` (`idSite`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `stagiaire`;
CREATE TABLE `stagiaire` (
  `idStagiaire` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idSite` int(10) unsigned NOT NULL,
  `nom` tinytext NOT NULL,
  `prenom` tinytext NOT NULL,
  `service` int(10) unsigned NOT NULL,
  `equipe` int(10) unsigned NOT NULL,
  `tranche` int(10) unsigned NOT NULL,
  `fonctionActuelle` int(10) unsigned NOT NULL,
  PRIMARY KEY (`idStagiaire`),
  KEY `idSite` (`idSite`),
  KEY `service` (`service`),
  KEY `equipe` (`equipe`),
  KEY `tranche` (`tranche`),
  KEY `fonctionActuelle` (`fonctionActuelle`),
  CONSTRAINT `stagiaireSite` FOREIGN KEY (`idSite`) REFERENCES `site` (`idSite`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `stagiaireService` FOREIGN KEY (`service`) REFERENCES `services` (`idService`) ON UPDATE CASCADE,
  CONSTRAINT `stagiaireEquipe` FOREIGN KEY (`equipe`) REFERENCES `equipes` (`idEquipe`) ON UPDATE CASCADE,
  CONSTRAINT `stagiaireTranche` FOREIGN KEY (`tranche`) REFERENCES `tranches` (`idTranche`) ON UPDATE CASCADE,
  CONSTRAINT `stagiaireFonction` FOREIGN KEY (`fonctionActuelle`) REFERENCES `fonctions` (`idFonction`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `themes`;
CREATE TABLE `themes` (
  `idTheme` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idStage` int(10) unsigned NOT NULL,
  `theme` tinytext NOT NULL,
  PRIMARY KEY (`idTheme`),
  KEY `idStage` (`idStage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 2015-06-29 13:39:37
