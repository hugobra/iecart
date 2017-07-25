-- phpMyAdmin SQL Dump
-- version 4.3.11
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Ven 03 Juillet 2015 à 14:00
-- Version du serveur :  5.6.24
-- Version de PHP :  5.6.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `testsql`
--

-- --------------------------------------------------------

--
-- Structure de la table `equipes`
--

CREATE TABLE IF NOT EXISTS `equipes` (
  `idEquipe` int(10) unsigned NOT NULL,
  `nomEquipe` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `equipes`
--

INSERT INTO `equipes` (`idEquipe`, `nomEquipe`) VALUES
(1, 'A'),
(2, 'B'),
(3, 'C'),
(4, 'D'),
(5, 'E'),
(6, 'F'),
(7, 'G'),
(8, 'Sans');

-- --------------------------------------------------------

--
-- Structure de la table `fap`
--

CREATE TABLE IF NOT EXISTS `fap` (
  `idFAP` int(10) unsigned NOT NULL,
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
  `dateFin` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `fonction`
--

CREATE TABLE IF NOT EXISTS `fonction` (
  `idFonction` int(10) unsigned NOT NULL,
  `idStagiaire` int(11) unsigned NOT NULL,
  `nom` int(11) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `fonctions`
--

CREATE TABLE IF NOT EXISTS `fonctions` (
  `idFonction` int(10) unsigned NOT NULL,
  `nomFonction` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `fonctions`
--

INSERT INTO `fonctions` (`idFonction`, `nomFonction`) VALUES
(1, 'OpÃ©rateur en formation'),
(2, 'OpÃ©rateur'),
(3, 'Cadre technique'),
(4, 'Chef d''exploitation'),
(5, 'IngÃ©nieur sÃ»retÃ©'),
(6, 'Autre');

-- --------------------------------------------------------

--
-- Structure de la table `objectifs`
--

CREATE TABLE IF NOT EXISTS `objectifs` (
  `idObjectif` int(10) unsigned NOT NULL,
  `idFap` int(10) unsigned NOT NULL,
  `objectif` int(10) unsigned NOT NULL,
  `pointFort` text NOT NULL,
  `pointAAmeliorer` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `objectifsstage`
--

CREATE TABLE IF NOT EXISTS `objectifsstage` (
  `idObjectif` int(10) unsigned NOT NULL,
  `idStage` int(10) unsigned NOT NULL,
  `idFonction` int(10) unsigned NOT NULL,
  `nomObjectif` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `rangs`
--

CREATE TABLE IF NOT EXISTS `rangs` (
  `idRang` int(10) unsigned NOT NULL,
  `nomRang` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `rangs`
--

INSERT INTO `rangs` (`idRang`, `nomRang`) VALUES
(3, 'Stagiaire'),
(4, 'Formateur'),
(5, 'Manager 1'),
(6, 'Manager 2'),
(7, 'Admin Site'),
(8, 'Super Admin'),
(9, 'Developpeur');

-- --------------------------------------------------------

--
-- Structure de la table `services`
--

CREATE TABLE IF NOT EXISTS `services` (
  `idService` int(10) unsigned NOT NULL,
  `nomService` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `site`
--

CREATE TABLE IF NOT EXISTS `site` (
  `idSite` int(10) unsigned NOT NULL,
  `motDePasse` tinytext NOT NULL,
  `nomSite` tinytext NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `site`
--

INSERT INTO `site` (`idSite`, `motDePasse`, `nomSite`) VALUES
(1, 'f1b9702d7ea511aa313e7f159cf8fd763c86611e', 'Golfech');

-- --------------------------------------------------------

--
-- Structure de la table `stage`
--

CREATE TABLE IF NOT EXISTS `stage` (
  `idStage` int(10) unsigned NOT NULL,
  `idSite` int(10) unsigned NOT NULL,
  `date` date NOT NULL,
  `titre` tinytext NOT NULL,
  `duree` int(11) NOT NULL,
  `valide` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `stagiaire`
--

CREATE TABLE IF NOT EXISTS `stagiaire` (
  `idStagiaire` int(10) unsigned NOT NULL,
  `idSite` int(10) unsigned NOT NULL,
  `nom` tinytext NOT NULL,
  `prenom` tinytext NOT NULL,
  `service` int(10) unsigned NOT NULL,
  `equipe` int(10) unsigned NOT NULL,
  `tranche` int(10) unsigned NOT NULL,
  `fonctionActuelle` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `themes`
--

CREATE TABLE IF NOT EXISTS `themes` (
  `idTheme` int(10) unsigned NOT NULL,
  `idStage` int(10) unsigned NOT NULL,
  `theme` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `tranches`
--

CREATE TABLE IF NOT EXISTS `tranches` (
  `idTranche` int(10) unsigned NOT NULL,
  `nomTranche` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `tranches`
--

INSERT INTO `tranches` (`idTranche`, `nomTranche`) VALUES
(1, '1'),
(2, '2'),
(3, '1-2');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `nni` varchar(8) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `idRang` int(10) unsigned NOT NULL,
  `lastco` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `equipes`
--
ALTER TABLE `equipes`
  ADD PRIMARY KEY (`idEquipe`);

--
-- Index pour la table `fap`
--
ALTER TABLE `fap`
  ADD PRIMARY KEY (`idFAP`), ADD KEY `idStage` (`idStage`), ADD KEY `idStagiaire` (`idStagiaire`), ADD KEY `idSite` (`idSite`), ADD KEY `fonction` (`fonction`);

--
-- Index pour la table `fonction`
--
ALTER TABLE `fonction`
  ADD PRIMARY KEY (`idFonction`), ADD KEY `idFonction` (`idFonction`), ADD KEY `idStagiaire` (`idStagiaire`), ADD KEY `nom` (`nom`);

--
-- Index pour la table `fonctions`
--
ALTER TABLE `fonctions`
  ADD PRIMARY KEY (`idFonction`);

--
-- Index pour la table `objectifs`
--
ALTER TABLE `objectifs`
  ADD PRIMARY KEY (`idObjectif`), ADD KEY `idFap` (`idFap`), ADD KEY `objectif` (`objectif`), ADD KEY `idStage` (`idFap`);

--
-- Index pour la table `objectifsstage`
--
ALTER TABLE `objectifsstage`
  ADD PRIMARY KEY (`idObjectif`), ADD KEY `idStage` (`idStage`), ADD KEY `idFonction` (`idFonction`);

--
-- Index pour la table `rangs`
--
ALTER TABLE `rangs`
  ADD PRIMARY KEY (`idRang`);

--
-- Index pour la table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`idService`);

--
-- Index pour la table `site`
--
ALTER TABLE `site`
  ADD PRIMARY KEY (`idSite`);

--
-- Index pour la table `stage`
--
ALTER TABLE `stage`
  ADD PRIMARY KEY (`idStage`), ADD KEY `idSite` (`idSite`), ADD KEY `idStagiaire` (`idSite`);

--
-- Index pour la table `stagiaire`
--
ALTER TABLE `stagiaire`
  ADD PRIMARY KEY (`idStagiaire`), ADD KEY `idSite` (`idSite`), ADD KEY `service` (`service`), ADD KEY `equipe` (`equipe`), ADD KEY `tranche` (`tranche`), ADD KEY `fonctionActuelle` (`fonctionActuelle`);

--
-- Index pour la table `themes`
--
ALTER TABLE `themes`
  ADD PRIMARY KEY (`idTheme`), ADD KEY `idStage` (`idStage`);

--
-- Index pour la table `tranches`
--
ALTER TABLE `tranches`
  ADD PRIMARY KEY (`idTranche`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`nni`), ADD KEY `idRang` (`idRang`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `equipes`
--
ALTER TABLE `equipes`
  MODIFY `idEquipe` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT pour la table `fap`
--
ALTER TABLE `fap`
  MODIFY `idFAP` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `fonction`
--
ALTER TABLE `fonction`
  MODIFY `idFonction` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `fonctions`
--
ALTER TABLE `fonctions`
  MODIFY `idFonction` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT pour la table `objectifs`
--
ALTER TABLE `objectifs`
  MODIFY `idObjectif` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `objectifsstage`
--
ALTER TABLE `objectifsstage`
  MODIFY `idObjectif` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `rangs`
--
ALTER TABLE `rangs`
  MODIFY `idRang` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT pour la table `services`
--
ALTER TABLE `services`
  MODIFY `idService` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `site`
--
ALTER TABLE `site`
  MODIFY `idSite` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pour la table `stage`
--
ALTER TABLE `stage`
  MODIFY `idStage` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `stagiaire`
--
ALTER TABLE `stagiaire`
  MODIFY `idStagiaire` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `themes`
--
ALTER TABLE `themes`
  MODIFY `idTheme` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `tranches`
--
ALTER TABLE `tranches`
  MODIFY `idTranche` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `fap`
--
ALTER TABLE `fap`
ADD CONSTRAINT `fapFonction` FOREIGN KEY (`fonction`) REFERENCES `fonctions` (`idFonction`) ON UPDATE CASCADE,
ADD CONSTRAINT `fapSite` FOREIGN KEY (`idSite`) REFERENCES `site` (`idSite`) ON UPDATE CASCADE,
ADD CONSTRAINT `fapStage` FOREIGN KEY (`idStage`) REFERENCES `stage` (`idStage`) ON UPDATE CASCADE,
ADD CONSTRAINT `fapStagiaire` FOREIGN KEY (`idStagiaire`) REFERENCES `stagiaire` (`idStagiaire`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `fonction`
--
ALTER TABLE `fonction`
ADD CONSTRAINT `fonctionNom` FOREIGN KEY (`nom`) REFERENCES `fonctions` (`idFonction`) ON UPDATE CASCADE,
ADD CONSTRAINT `fonctionStagiaire` FOREIGN KEY (`idStagiaire`) REFERENCES `stagiaire` (`idStagiaire`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `objectifs`
--
ALTER TABLE `objectifs`
ADD CONSTRAINT `objectifFAP` FOREIGN KEY (`idFap`) REFERENCES `fap` (`idFAP`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `objectifObjstage` FOREIGN KEY (`objectif`) REFERENCES `objectifsstage` (`idObjectif`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `objectifsstage`
--
ALTER TABLE `objectifsstage`
ADD CONSTRAINT `fk_objectif_fonction` FOREIGN KEY (`idFonction`) REFERENCES `fonctions` (`idFonction`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `fk_objectif_stage` FOREIGN KEY (`idStage`) REFERENCES `stage` (`idStage`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `stage`
--
ALTER TABLE `stage`
ADD CONSTRAINT `stageSite` FOREIGN KEY (`idSite`) REFERENCES `site` (`idSite`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `stagiaire`
--
ALTER TABLE `stagiaire`
ADD CONSTRAINT `stagiaireEquipe` FOREIGN KEY (`equipe`) REFERENCES `equipes` (`idEquipe`) ON UPDATE CASCADE,
ADD CONSTRAINT `stagiaireFonction` FOREIGN KEY (`fonctionActuelle`) REFERENCES `fonctions` (`idFonction`) ON UPDATE CASCADE,
ADD CONSTRAINT `stagiaireService` FOREIGN KEY (`service`) REFERENCES `services` (`idService`) ON UPDATE CASCADE,
ADD CONSTRAINT `stagiaireSite` FOREIGN KEY (`idSite`) REFERENCES `site` (`idSite`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `stagiaireTranche` FOREIGN KEY (`tranche`) REFERENCES `tranches` (`idTranche`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `users`
--
ALTER TABLE `users`
ADD CONSTRAINT `fk_user_rang` FOREIGN KEY (`idRang`) REFERENCES `rangs` (`idRang`) ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
