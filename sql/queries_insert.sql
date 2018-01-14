-- ************************************************************************************************ --
-- ----------------------------- Documentation format --------------------------------------------- --
-- ************************************************************************************************ --

-- ---
-- <Query_Name>
-- Info: <Query_Infos>
-- Parameters: [<IN|OUT|INOUT> <Columns_Name> <TYPE> '<Comment>' ,.. ]
-- Return: <Format_Info> [<Columns_Name>] '<Alias>' ,.. ]
-- ---

-- ---
-- Queries List
-- ---

-- inPhpMyAdmin you have to set delimiter to | in interface
-- Set delimiter to | (unquote to use outside phpMyAdmin)
DELIMITER |

-- ************************************************************************************************ --
-- ----------------------------- Drop existing Queries -------------------------------------------- --
-- ************************************************************************************************ --

-- Insert procedure
DROP PROCEDURE IF EXISTS INSERT_FILE|
DROP PROCEDURE IF EXISTS INSERT_SGL_USER|
DROP PROCEDURE IF EXISTS INSERT_PLATFORM_USER|
DROP PROCEDURE IF EXISTS INSERT_GAME_USER|
DROP PROCEDURE IF EXISTS INSERT_SCHOOL|
DROP PROCEDURE IF EXISTS INSERT_SGL_TEAM|
DROP PROCEDURE IF EXISTS INSERT_TEAM_REQUEST|

-- ************************************************************************************************ --
-- ----------------------------- Create Queries --------------------------------------------------- --
-- ************************************************************************************************ --

-- -------------------------------------------------------------------------------------------------------------------------------------------- --
-- INSERT_FILE
-- ---

CREATE PROCEDURE INSERT_FILE(IN name VARCHAR(256), IN path VARCHAR(512), IN size INTEGER, IN type VARCHAR(64), IN md5 VARCHAR(65))
BEGIN
  INSERT INTO `T_FILE` (`F_NAME`,`F_PATH`,`F_SIZE`,`F_TYPE`,`F_MD5`) VALUES
  (name,path,size,type,md5);

  SELECT F_UID, F_NAME, F_PATH, F_SIZE, F_TYPE, F_MD5 FROM T_FILE WHERE F_UID=LAST_INSERT_ID();
END |

-- -------------------------------------------------------------------------------------------------------------------------------------------- --
-- INSERT_SGL_USER
-- Error:1 <= Login already used
-- Error:2 <= Mail already used
-- ---

CREATE PROCEDURE INSERT_SGL_USER( IN id_su_parent INTEGER, IN id_file_card INTEGER, IN id_school INTEGER,
                                  IN login VARCHAR(32), IN pass VARCHAR(512), IN salt VARCHAR(512), IN config_salt VARCHAR(512), IN mail VARCHAR(256), IN activation VARCHAR(256),
                                  IN type INTEGER(2), IN gender INTEGER(2), IN birth_date DATE,
                                  IN first_name VARCHAR(128), IN last_name VARCHAR(128), IN knowledge INTEGER(4))
query:BEGIN
  DECLARE is_user_existing INTEGER DEFAULT NULL;

  SELECT SU_UID INTO is_user_existing FROM T_SGL_USER WHERE SU_LOGIN=login;
  IF is_user_existing IS NOT NULL THEN
    SELECT FALSE as RESULT, 1 as ERROR;
    LEAVE query;
  END IF;

  SELECT SU_UID INTO is_user_existing FROM T_SGL_USER WHERE SU_MAIL=mail OR SU_ACTIVMAIL=mail LIMIT 1;
  IF is_user_existing IS NOT NULL THEN
    SELECT FALSE as RESULT, 2 as ERROR;
    LEAVE query;
  END IF;

  INSERT INTO `T_SGL_USER` (`SU_ID_PARENT_SU`,`SU_ID_CARD_F`,`SU_ID_S`,`SU_LOGIN`,`SU_PASS`,`SU_SALT`,`SU_MAIL`,`SU_ACTIVATION`,`SU_TYPE`,`SU_GENDER`,`SU_BIRTH_DATE`,`SU_REGISTER_DATE`,`SU_FIRST_NAME`,`SU_LAST_NAME`, `SU_KNOWLEDGE_ORIGIN`) VALUES
  (id_su_parent,id_file_card,id_school,login,SHA1(CONCAT(CONCAT(salt,pass),config_salt)),salt,mail,activation,type,gender,birth_date,NOW(),first_name,last_name,knowledge);
  SELECT SU_UID,SU_ID_PARENT_SU,SU_ID_CARD_F,SU_ID_S,SU_LOGIN,SU_MAIL,SU_TYPE,SU_GENDER,SU_BIRTH_DATE,SU_FIRST_NAME,SU_LAST_NAME FROM T_SGL_USER WHERE SU_UID=LAST_INSERT_ID();
END |

-- -------------------------------------------------------------------------------------------------------------------------------------------- --
-- INSERT_PLATFORM_USER
-- ---

CREATE PROCEDURE INSERT_PLATFORM_USER(IN id_sgl_user INTEGER, IN id_platform INTEGER, IN pseudo VARCHAR(256))
BEGIN
  INSERT INTO `T_PLATFORM_USER` (`PU_ID_SU`,`PU_ID_P`,`PU_PSEUDO`) VALUES
  (id_sgl_user,id_platform,pseudo);
  SELECT PU_UID,PU_PSEUDO FROM T_PLATFORM_USER WHERE PU_UID=LAST_INSERT_ID();
END |

-- -------------------------------------------------------------------------------------------------------------------------------------------- --
-- INSERT_GAME_USER
-- ---

CREATE PROCEDURE INSERT_GAME_USER( IN id_p_user INTEGER, IN id_game INTEGER, IN id_team INTEGER, IN pseudo VARCHAR(64), IN rank INTEGER)
BEGIN
  DECLARE bool_p_pseudo INTEGER DEFAULT 0;
  SELECT G_USE_PLATEFORM_PSEUDO FROM T_GAME WHERE G_UID=id_game;

  IF bool_p_pseudo=0 THEN
    INSERT INTO `T_GAME_USER` (`GU_ID_PU`,`GU_ID_PU`,`GU_ID_ST`,`GU_PSEUDO`,`GU_RANK`) VALUES
    (id_p_user,id_game,id_team,pseudo,rank);
  ELSE
    INSERT INTO `T_GAME_USER` (`GU_ID_PU`,`GU_ID_G`,`GU_ID_ST`,`GU_RANK`) VALUES
      (id_p_user,id_game,id_team,rank);
  END IF;
  SELECT GU_UID, GU_ID_PU, GU_ID_G, GU_ID_ST, GU_RANK FROM T_GAME_USER WHERE GU_UID=LAST_INSERT_ID();
END |

-- -------------------------------------------------------------------------------------------------------------------------------------------- --
-- INSERT_SCHOOL
-- ---

CREATE PROCEDURE INSERT_SCHOOL( IN id_file_logo INTEGER, IN name_school VARCHAR(1024), IN city VARCHAR(256), IN country VARCHAR(128), IN website VARCHAR(1024))
BEGIN
  INSERT INTO `T_SCHOOL` (`S_ID_LOGO_F`,`S_NAME`,`S_CITY`,`S_COUNTRY`,`S_WEBSITE`) VALUES
  (id_file_logo,name_school,city,country,website);
  SELECT S_UID, S_ID_LOGO_F, S_NAME, S_CITY, S_COUNTRY, S_WEBSITE FROM T_SCHOOL WHERE S_UID=LAST_INSERT_ID();
END|

-- -------------------------------------------------------------------------------------------------------------------------------------------- --
-- INSERT_SGL_TEAM
-- ---

CREATE PROCEDURE INSERT_SGL_TEAM( IN id_file_picture INTEGER, IN id_game INTEGER, IN id_lead_user INTEGER,
                                  IN tag VARCHAR(4), IN name_team VARCHAR(64))
BEGIN
  INSERT INTO `T_SGL_TEAM` (`ST_ID_PICTURE_F`,`ST_ID_G`,`ST_ID_LEAD_SU`,`ST_TAG`,`ST_NAME`,`ST_STATUS`,`ST_REGISTER_DATE`) VALUES
  (id_file_picture,id_game,id_lead_user,tag,name_team,0,NOW());
  SELECT ST_UID, ST_ID_PICTURE_F, ST_ID_G, ST_ID_LEAD_SU, ST_TAG, ST_NAME, ST_STATUS, ST_REGISTER_DATE FROM T_SGL_TEAM WHERE ST_UID=LAST_INSERT_ID();
END|

-- -------------------------------------------------------------------------------------------------------------------------------------------- --
-- INSERT_TEAM_REQUEST
-- ---

CREATE PROCEDURE INSERT_TEAM_REQUEST( IN id_sgl_team INTEGER, IN id_game_user INTEGER, IN type INTEGER)
BEGIN
  INSERT INTO `T_TEAM_REQUEST` (`TR_ID_ST`,`TR_ID_GU`,`TR_TYPE`,`TR_SEND_DATE`) VALUES
  (id_sgl_team,id_game_user,type, NOW());
  SELECT TR_ID_ST, TR_ID_GU, TR_TYPE, TR_SEND_DATE FROM T_TEAM_REQUEST WHERE TR_UID=LAST_INSERT_ID();
END|