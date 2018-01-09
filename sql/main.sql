-- ---
-- Globals
-- ---

-- SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
-- SET FOREIGN_KEY_CHECKS=0;

-- ---
-- Clean Tables
--
-- ---
ALTER TABLE T_SGL_USER
  DROP FOREIGN KEY FK_SU_PARENT_SU;

DROP TABLE IF EXISTS `T_NOTIFICATION`;
DROP TABLE IF EXISTS `T_TEAM_REQUEST`;
DROP TABLE IF EXISTS `T_GAME_USER`;
DROP TABLE IF EXISTS `T_SGL_TEAM`;
DROP TABLE IF EXISTS `T_GAME`;
DROP TABLE IF EXISTS `T_PLATFORM_USER`;
DROP TABLE IF EXISTS `T_PLATFORM`;
DROP TABLE IF EXISTS `T_SGL_USER`;
DROP TABLE IF EXISTS `T_SCHOOL`;
DROP TABLE IF EXISTS `T_FILE`;

-- ---
-- Table 'T_FILE'
--
-- ---

CREATE TABLE `T_FILE` (
  `F_UID` INTEGER NOT NULL AUTO_INCREMENT,
  `F_NAME` VARCHAR(256) NOT NULL,
  `F_PATH` VARCHAR(512) NOT NULL,
  `F_SIZE` INTEGER NOT NULL,
  `F_TYPE` VARCHAR(64) NOT NULL,
  `F_MD5` VARCHAR(64) NOT NULL,
  PRIMARY KEY (`F_UID`)
)ENGINE=innodb;

-- ---
-- Table 'T_SCHOOL'
--
-- ---

CREATE TABLE `T_SCHOOL` (
  `S_UID` INTEGER NOT NULL AUTO_INCREMENT,
  `S_ID_LOGO_F` INTEGER NULL DEFAULT NULL,
  `S_NAME` VARCHAR(1024) NULL DEFAULT NULL,
  `S_CITY` VARCHAR(256) NULL DEFAULT NULL,
  `S_COUNTRY` VARCHAR(128) NOT NULL DEFAULT 'France',
  `S_WEBSITE` VARCHAR(1024) NULL DEFAULT NULL,
  `S_VALIDATED` INTEGER(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`S_UID`)
)ENGINE=innodb;

-- ---
-- Table 'T_SGL_USER'
--
-- ---

CREATE TABLE `T_SGL_USER` (
  `SU_UID` INTEGER NOT NULL AUTO_INCREMENT,
  `SU_ID_PARENT_SU` INTEGER NULL DEFAULT NULL,
  `SU_ID_CARD_F` INTEGER NULL DEFAULT NULL,
  `SU_ID_S` INTEGER NULL DEFAULT NULL,
  `SU_LOGIN` VARCHAR(32) NOT NULL,
  `SU_PASS` VARCHAR(512) NULL DEFAULT NULL,
  `SU_SALT` VARCHAR(512) NULL DEFAULT NULL,
  `SU_RESETPASS` VARCHAR(512) NULL DEFAULT NULL,
  `SU_MAIL` VARCHAR(256) NOT NULL,
  `SU_ACTIVMAIL` VARCHAR(256) NULL DEFAULT NULL,
  `SU_ACTIVATION` VARCHAR(64) NOT NULL DEFAULT '0',
  `SU_TYPE` INTEGER(2) NOT NULL DEFAULT 0,
  `SU_GENDER` INTEGER(2) NOT NULL DEFAULT 0,
  `SU_BIRTH_DATE` DATE NULL DEFAULT NULL,
  `SU_REGISTER_DATE` DATETIME NOT NULL,
  `SU_FIRST_NAME` VARCHAR(128) NULL DEFAULT NULL,
  `SU_LAST_NAME` VARCHAR(128) NULL DEFAULT NULL,
  `SU_KNOWLEDGE_ORIGIN` INTEGER(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`SU_UID`),
  UNIQUE KEY (`SU_LOGIN`),
  UNIQUE KEY (`SU_MAIL`)
)ENGINE=innodb;

-- ---
-- Table 'T_SGL_TEAM'
--
-- ---

CREATE TABLE `T_SGL_TEAM` (
  `ST_UID` INTEGER NOT NULL AUTO_INCREMENT,
  `ST_ID_PICTURE_F` INTEGER NULL DEFAULT NULL,
  `ST_ID_G` INTEGER NOT NULL,
  `ST_ID_LEAD_SU` INTEGER NOT NULL,
  `ST_TAG` VARCHAR(4) NOT NULL,
  `ST_NAME` VARCHAR(64) NOT NULL,
  `ST_STATUS` INTEGER(1) NOT NULL DEFAULT 0,
  `ST_REGISTER_DATE` DATETIME NOT NULL,
  `ST_RANK` INTEGER NULL DEFAULT NULL,
  PRIMARY KEY (`ST_UID`)
)ENGINE=innodb;

-- ---
-- Table 'T_PLATFORM'
--
-- ---

CREATE TABLE `T_PLATFORM` (
  `P_UID` INTEGER NOT NULL AUTO_INCREMENT,
  `P_ID_LOGO_F` INTEGER NULL DEFAULT NULL,
  `P_NAME` VARCHAR(256) NOT NULL,
  `P_PSEUDO_NAME` VARCHAR(256) NOT NULL,
  `P_COMPANY` VARCHAR(256) NOT NULL,
  `P_WEBSITE` VARCHAR(1024) NOT NULL,
  PRIMARY KEY (`P_UID`)
)ENGINE=innodb;

-- ---
-- Table 'T_GAME'
--
-- ---


CREATE TABLE `T_GAME` (
  `G_UID` INTEGER NOT NULL AUTO_INCREMENT,
  `G_ID_LOGO_F` INTEGER NULL DEFAULT NULL,
  `G_ID_P` INTEGER NOT NULL,
  `G_NAME` VARCHAR(512) NOT NULL,
  `G_DESCRIPTION` MEDIUMTEXT NULL DEFAULT NULL,
  `G_RANK_DESCRIPTION` VARCHAR(128) NOT NULL,
  `G_USE_PLATEFORM_PSEUDO` INTEGER(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`G_UID`)
)ENGINE=innodb;

-- ---
-- Table 'T_PLATFORM_USER'
--
-- ---

CREATE TABLE `T_PLATFORM_USER` (
  `PU_UID` INTEGER NOT NULL AUTO_INCREMENT,
  `PU_ID_SU` INTEGER NOT NULL,
  `PU_ID_P` INTEGER NOT NULL,
  `PU_PSEUDO` VARCHAR(256) NOT NULL,
  PRIMARY KEY (`PU_UID`),
  UNIQUE KEY (`PU_ID_SU`, `PU_ID_P`)
)ENGINE=innodb;

-- ---
-- Table 'T_GAME_USER'
--
-- ---

CREATE TABLE `T_GAME_USER` (
  `GU_UID` INTEGER NOT NULL AUTO_INCREMENT,
  `GU_ID_PU` INTEGER NOT NULL,
  `GU_ID_G` INTEGER NOT NULL,
  `GU_ID_ST` INTEGER NULL DEFAULT NULL,
  `GU_PSEUDO` VARCHAR(64) NULL DEFAULT NULL,
  `GU_RANK` INTEGER NULL DEFAULT NULL,
  PRIMARY KEY (`GU_UID`),
  UNIQUE KEY (`GU_ID_G`, `GU_ID_PU`)
)ENGINE=innodb;

-- ---
-- Table 'T_TEAM_REQUEST'
--
-- ---

CREATE TABLE `T_TEAM_REQUEST` (
  `TR_ID_ST` INTEGER NOT NULL,
  `TR_ID_GU` INTEGER NOT NULL,
  `TR_TYPE` INTEGER(1) NOT NULL,
  `TR_SEND_DATE` DATETIME NOT NULL,
  `TR_VIEW_DATE` DATETIME NOT NULL,
  PRIMARY KEY (`TR_ID_ST`, `TR_ID_GU`)
)ENGINE=innodb;

-- ---
-- Table 'T_NOTIFICATION'
--
-- ---

CREATE TABLE `T_NOTIFICATION` (
  `N_UID` INTEGER NOT NULL AUTO_INCREMENT,
  `N_ID_SU` INTEGER NOT NULL,
  `N_TITLE` VARCHAR(128) NOT NULL,
  `N_CONTENT` MEDIUMTEXT NULL DEFAULT NULL,
  `N_LINK` VARCHAR(1024) NULL DEFAULT NULL,
  `N_DATE` DATETIME NOT NULL,
  `N_READ` DATETIME NOT NULL,
  PRIMARY KEY (`N_UID`)
)ENGINE=innodb;

-- ---
-- Foreign Keys
-- ---

ALTER TABLE `T_SGL_USER` ADD CONSTRAINT FK_SU_CARD_F FOREIGN KEY (SU_ID_CARD_F) REFERENCES `T_FILE` (`F_UID`);
ALTER TABLE `T_SGL_USER` ADD CONSTRAINT FK_SU_S FOREIGN KEY (SU_ID_S) REFERENCES `T_SCHOOL` (`S_UID`);
ALTER TABLE `T_SGL_USER` ADD CONSTRAINT FK_SU_PARENT_SU FOREIGN KEY (SU_ID_PARENT_SU) REFERENCES `T_SGL_USER` (`SU_UID`);
ALTER TABLE `T_SGL_TEAM` ADD CONSTRAINT FK_ST_PICTURE_F FOREIGN KEY (ST_ID_PICTURE_F) REFERENCES `T_FILE` (`F_UID`);
ALTER TABLE `T_SGL_TEAM` ADD CONSTRAINT FK_ST_G FOREIGN KEY (ST_ID_G) REFERENCES `T_GAME` (`G_UID`);
ALTER TABLE `T_SGL_TEAM` ADD CONSTRAINT FK_LEAD_SU FOREIGN KEY (ST_ID_LEAD_SU) REFERENCES `T_SGL_USER` (`SU_UID`);
ALTER TABLE `T_GAME` ADD CONSTRAINT FK_G_LOGO_F FOREIGN KEY (G_ID_LOGO_F) REFERENCES `T_FILE` (`F_UID`);
ALTER TABLE `T_GAME` ADD CONSTRAINT FK_G_P FOREIGN KEY (G_ID_P) REFERENCES `T_PLATFORM` (`P_UID`);
ALTER TABLE `T_SCHOOL` ADD CONSTRAINT FK_S_LOGO_F FOREIGN KEY (S_ID_LOGO_F) REFERENCES `T_FILE` (`F_UID`);
ALTER TABLE `T_GAME_USER` ADD CONSTRAINT FK_GU_PU FOREIGN KEY (GU_ID_PU) REFERENCES `T_PLATFORM_USER` (`PU_UID`);
ALTER TABLE `T_GAME_USER` ADD CONSTRAINT FK_GU_G FOREIGN KEY (GU_ID_G) REFERENCES `T_GAME` (`G_UID`);
ALTER TABLE `T_GAME_USER` ADD CONSTRAINT FK_GU_ST FOREIGN KEY (GU_ID_ST) REFERENCES `T_SGL_TEAM` (`ST_UID`);
ALTER TABLE `T_TEAM_REQUEST` ADD CONSTRAINT FK_TR_ST FOREIGN KEY (TR_ID_ST) REFERENCES `T_SGL_TEAM` (`ST_UID`);
ALTER TABLE `T_TEAM_REQUEST` ADD CONSTRAINT FK_TR_GU FOREIGN KEY (TR_ID_GU) REFERENCES `T_GAME_USER` (`GU_UID`);
ALTER TABLE `T_PLATFORM_USER` ADD CONSTRAINT FK_PU_SU FOREIGN KEY (PU_ID_SU) REFERENCES `T_SGL_USER` (`SU_UID`);
ALTER TABLE `T_PLATFORM_USER` ADD CONSTRAINT FK_PU_P FOREIGN KEY (PU_ID_P) REFERENCES `T_PLATFORM` (`P_UID`);
ALTER TABLE `T_PLATFORM` ADD CONSTRAINT FK_P_LOGO_F FOREIGN KEY (P_ID_LOGO_F) REFERENCES `T_FILE` (`F_UID`);
ALTER TABLE `T_NOTIFICATION` ADD CONSTRAINT FK_N_SU FOREIGN KEY (N_ID_SU) REFERENCES `T_SGL_USER` (`SU_UID`);

-- ---
-- Basic data
-- ---

INSERT INTO `T_PLATFORM` (`P_UID`,`P_ID_LOGO_F`,`P_NAME`,`P_PSEUDO_NAME`,`P_COMPANY`,`P_WEBSITE`) VALUES -- BattleNet
(null,null,'Battle.net','BattleTag','Blizzard','https://www.battle.net');
INSERT INTO `T_PLATFORM` (`P_UID`,`P_ID_LOGO_F`,`P_NAME`,`P_PSEUDO_NAME`,`P_COMPANY`,`P_WEBSITE`) VALUES -- Steam
(null,null,'Steam','SteamID','Valve','https://store.steampowered.com');
INSERT INTO `T_PLATFORM` (`P_UID`,`P_ID_LOGO_F`,`P_NAME`,`P_PSEUDO_NAME`,`P_COMPANY`,`P_WEBSITE`) VALUES -- Steam
(null,null,'Discord','Username','Discord Inc.','https://discordapp.com');
INSERT INTO `T_PLATFORM` (`P_UID`,`P_ID_LOGO_F`,`P_NAME`,`P_PSEUDO_NAME`,`P_COMPANY`,`P_WEBSITE`) VALUES -- Steam
(null,null,'Riot Games','Invocateur','Riot','https://euw.leagueoflegends.com/fr');

INSERT INTO `T_GAME` (`G_UID`,`G_ID_LOGO_F`,`G_ID_P`,`G_NAME`,`G_DESCRIPTION`,`G_RANK_DESCRIPTION`,`G_USE_PLATEFORM_PSEUDO`) VALUES
(null,null,1,'Overwatch','','Rang',1);
INSERT INTO `T_GAME` (`G_UID`,`G_ID_LOGO_F`,`G_ID_P`,`G_NAME`,`G_DESCRIPTION`,`G_RANK_DESCRIPTION`,`G_USE_PLATEFORM_PSEUDO`) VALUES
(null,null,2,'Counter Strick','','Rang',1);
INSERT INTO `T_GAME` (`G_UID`,`G_ID_LOGO_F`,`G_ID_P`,`G_NAME`,`G_DESCRIPTION`,`G_RANK_DESCRIPTION`,`G_USE_PLATEFORM_PSEUDO`) VALUES
(null,null,4,'League Of Legends','','League',1);
INSERT INTO `T_GAME` (`G_UID`,`G_ID_LOGO_F`,`G_ID_P`,`G_NAME`,`G_DESCRIPTION`,`G_RANK_DESCRIPTION`,`G_USE_PLATEFORM_PSEUDO`) VALUES
(null,null,1,'Hearthstone','','Rang',1);



-- INSERT INTO `T_SGL_USER` (`SU_UID`, `SU_ID_PARENT_SU`,`SU_ID_CARD_F`,`SU_ID_S`,`SU_LOGIN`,`SU_PASS`,`SU_SALT`,`SU_RESETPASS`,`SU_MAIL`,`SU_ACTIVMAIL`,`SU_ACTIVATION`,`SU_TYPE`,`SU_GENDER`,`SU_BIRTH_DATE`,`SU_REGISTER_DATE`,`SU_FIRST_NAME`,`SU_LAST_NAME`) VALUES
-- ('','','','','','','','','','','','','','','','','');
-- INSERT INTO `T_SGL_TEAM` (`ST_UID`,`ST_ID_PICTURE_F`,`ST_ID_G`,`ST_ID_LEAD_SU`,`ST_TAG`,`ST_NAME`,`ST_STATUS`,`ST_REGISTER_DATE`,`ST_RANK`) VALUES
-- ('','','','','','','','','');
-- INSERT INTO `T_PLATFORM` (`P_UID`,`P_ID_LOGO_F`,`P_NAME`,`P_PSEUDO_NAME`,`P_COMPANY`,`P_WEBSITE`) VALUES -- Steam
-- ('','','','','','');
-- INSERT INTO `T_GAME` (`G_UID`,`G_ID_LOGO_F`,`G_ID_P`,`G_NAME`,`G_DESCRIPTION`,`G_RANK_DESCRIPTION`,`G_USE_PLATEFORM_PSEUDO`) VALUES
-- ('','','','','','');
-- INSERT INTO `T_SCHOOL` (`S_UID`,`S_ID_LOGO_F`,`S_NAME`,`S_CITY`,`S_COUNTRY`,`S_WEBSITE`,`S_VALIDATED`) VALUES
-- ('','','','','','','');
-- INSERT INTO `T_GAME_USER` (`GU_UID`,`GU_ID_PU`,`GU_ID_G`,`GU_ID_ST`,`GU_PSEUDO`,`GU_RANK`) VALUES
-- ('','','','','','');
-- INSERT INTO `T_FILE` (`F_UID`,`F_NAME`,`F_PATH`,`F_SIZE`,`F_TYPE`,`F_MD5`) VALUES
-- ('','','','','','');
-- INSERT INTO `T_TEAM_REQUEST` (`TR_ID_ST`,`TR_ID_GU`,`TR_TYPE`,`TR_SEND_DATE`,`TR_VIEW_DATE`) VALUES
-- ('','','','','');
-- INSERT INTO `T_PLATFORM_USER` (`PU_UID`,`PU_ID_SU`,`PU_ID_P`,`PU_PSEUDO`) VALUES
-- ('','','','');
-- INSERT INTO `T_NOTIFICATION` (`N_UID`,`N_ID_SU`,`N_TITLE`,`N_CONTENT`,`N_LINK`,`N_DATE`,`N_READ`) VALUES
-- ('','','','','','','');