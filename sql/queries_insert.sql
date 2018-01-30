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
DROP PROCEDURE IF EXISTS INSERT_SGL_USER_FROM_PARENT|
DROP PROCEDURE IF EXISTS INSERT_SGL_USER|
DROP PROCEDURE IF EXISTS INSERT_PLATFORM_USER|
DROP PROCEDURE IF EXISTS INSERT_GAME_USER|
DROP PROCEDURE IF EXISTS INSERT_SCHOOL|
DROP PROCEDURE IF EXISTS INSERT_SGL_TEAM|
DROP PROCEDURE IF EXISTS INSERT_TEAM_MAIL|

-- ************************************************************************************************ --
-- ----------------------------- Create Queries --------------------------------------------------- --
-- ************************************************************************************************ --

-- -------------------------------------------------------------------------------------------------------------------------------------------- --
-- INSERT_FILE
-- ---

CREATE PROCEDURE INSERT_FILE(IN name VARCHAR(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci, IN path VARCHAR(512) CHARACTER SET utf8 COLLATE utf8_unicode_ci, IN size INTEGER,
	IN type VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci, IN md5 VARCHAR(65) CHARACTER SET utf8 COLLATE utf8_unicode_ci)
BEGIN
  INSERT INTO `T_FILE` (`F_NAME`,`F_PATH`,`F_SIZE`,`F_TYPE`,`F_MD5`) VALUES
  (name,path,size,type,md5);

  SELECT F_UID, F_NAME, F_PATH, F_SIZE, F_TYPE, F_MD5 FROM T_FILE WHERE F_UID=LAST_INSERT_ID();
END |

-- -------------------------------------------------------------------------------------------------------------------------------------------- --
-- INSERT_SGL_USER_FROM_PARENT
-- Error:1 <= Login already used
-- Error:2 <= Mail already used
-- ---

CREATE PROCEDURE INSERT_SGL_USER_FROM_PARENT( IN id_su_parent INTEGER, IN id_file_card INTEGER, IN id_school INTEGER,
                                  IN login VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci, IN pass VARCHAR(512) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
                                  IN salt VARCHAR(512) CHARACTER SET utf8 COLLATE utf8_unicode_ci, IN config_salt VARCHAR(512) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
                                  IN mail VARCHAR(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci, IN activation VARCHAR(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
                                  IN type INTEGER(2), IN gender INTEGER(2), IN birth_date DATE,
                                  IN first_name VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci, IN last_name VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci, IN knowledge INTEGER(4))
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
-- INSERT_SGL_USER
-- Error:1 <= Login already used
-- Error:2 <= Mail already used
-- ---

CREATE PROCEDURE INSERT_SGL_USER(IN login VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci, IN pass VARCHAR(512) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
	IN salt VARCHAR(512) CHARACTER SET utf8 COLLATE utf8_unicode_ci, IN config_salt VARCHAR(512) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
	IN mail VARCHAR(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci, IN activation VARCHAR(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci, IN school VARCHAR(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci)
query:BEGIN
  DECLARE is_user_existing INTEGER DEFAULT NULL;
  DECLARE school_id INTEGER DEFAULT NULL;

  SELECT SU_UID INTO is_user_existing FROM T_SGL_USER WHERE SU_LOGIN=login;
  IF is_user_existing IS NOT NULL THEN
    SELECT FALSE as RESULT, 1 as ERROR;
    LEAVE query;
  END IF;

  SELECT SU_UID INTO is_user_existing FROM T_SGL_USER WHERE SU_MAIL=mail AND SU_PASS IS NOT NULL;
  IF is_user_existing IS NOT NULL THEN
    SELECT FALSE as RESULT, 2 as ERROR;
    LEAVE query;
  END IF;

  SELECT S_UID INTO school_id FROM T_SCHOOL WHERE LOWER(TRIM(S_NAME))=LOWER(TRIM(school));
  IF school_id IS NULL THEN
    INSERT INTO `T_SCHOOL` (`S_NAME`) VALUES (school);
    SELECT S_UID INTO school_id FROM T_SCHOOL WHERE S_UID=LAST_INSERT_ID();
  END IF;

  SELECT SU_UID INTO is_user_existing FROM T_SGL_USER WHERE SU_MAIL=mail AND SU_PASS IS NULL;
  IF is_user_existing IS NOT NULL THEN
    UPDATE `T_SGL_USER` SET SU_LOGIN=login, SU_PASS=SHA1(CONCAT(CONCAT(salt,pass),config_salt)), SU_SALT=salt, SU_ACTIVATION=activation, SU_ID_S=school_id WHERE SU_MAIL=mail;
  ELSE
    INSERT INTO `T_SGL_USER` (`SU_LOGIN`,`SU_PASS`,`SU_SALT`,`SU_MAIL`,`SU_ACTIVATION`,`SU_ID_S`,`SU_REGISTER_DATE`) VALUES
  (login,SHA1(CONCAT(CONCAT(salt,pass),config_salt)),salt,mail,activation,school_id,NOW());
  END IF;

  
  SELECT TRUE as RESULT;
END |

-- -------------------------------------------------------------------------------------------------------------------------------------------- --
-- INSERT_PLATFORM_USER
-- ---

CREATE PROCEDURE INSERT_PLATFORM_USER(IN id_sgl_user INTEGER, IN id_platform INTEGER, IN pseudo VARCHAR(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci)
BEGIN
  INSERT INTO `T_PLATFORM_USER` (`PU_ID_SU`,`PU_ID_P`,`PU_PSEUDO`) VALUES
  (id_sgl_user,id_platform,pseudo);
  SELECT PU_UID,PU_PSEUDO FROM T_PLATFORM_USER WHERE PU_UID=LAST_INSERT_ID();
END |

-- -------------------------------------------------------------------------------------------------------------------------------------------- --
-- INSERT_GAME_USER
-- ---

CREATE PROCEDURE INSERT_GAME_USER( IN id_p_user INTEGER, IN id_game INTEGER, IN id_team INTEGER, IN pseudo VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci, IN rank INTEGER)
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

CREATE PROCEDURE INSERT_SCHOOL(IN name_school VARCHAR(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci, OUT school_id INTEGER)
BEGIN
  SET school_id = NULL;

  SELECT S_UID INTO school_id FROM T_SCHOOL WHERE LOWER(TRIM(S_NAME))=LOWER(TRIM(name_school));
  IF school_id IS NULL THEN
    INSERT INTO `T_SCHOOL` (`S_NAME`) VALUES (name_school);
    SELECT S_UID INTO school_id FROM T_SCHOOL WHERE S_UID=LAST_INSERT_ID();
  END IF;

  SELECT school_id as S_UID;
END|

-- -------------------------------------------------------------------------------------------------------------------------------------------- --
-- INSERT_SGL_TEAM
-- ---

CREATE PROCEDURE INSERT_SGL_TEAM(IN id_user INTEGER, IN id_game INTEGER)
BEGIN
  DECLARE team_id INTEGER DEFAULT NULL;

  SELECT GU_ID_ST INTO team_id FROM T_GAME_USER WHERE GU_ID_SU=id_user AND GU_ID_G = id_game;

  IF team_id IS NULL THEN
  	INSERT INTO `T_SGL_TEAM` (`ST_ID_G`,`ST_ID_LEAD_SU`,`ST_REGISTER_DATE`) VALUES (id_game,id_user,NOW());
  	SELECT ST_UID INTO team_id FROM T_SGL_TEAM WHERE ST_UID=LAST_INSERT_ID();

  	IF EXISTS (SELECT * FROM T_GAME_USER WHERE GU_ID_SU=id_user AND GU_ID_G=id_game) THEN
  	  UPDATE T_GAME_USER SET GU_ID_ST=team_id WHERE GU_ID_SU=id_user AND GU_ID_G=id_game;
	ELSE
	  INSERT INTO T_GAME_USER (GU_ID_SU, GU_ID_G, GU_ID_ST, GU_TYPE) VALUES (id_user, id_game, team_id, 1);
    END IF;
    SELECT TRUE as RESULT;
  ELSE
    SELECT FALSE as RESULT;
  END IF;
END|

-- -------------------------------------------------------------------------------------------------------------------------------------------- --
-- INSERT_TEAM_MAIL
-- ---
CREATE PROCEDURE INSERT_TEAM_MAIL(IN id_team INTEGER, IN id_lead INTEGER, IN mail VARCHAR(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci, IN type INTEGER, IN id_game INTEGER)
BEGIN
  DECLARE id_user INTEGER DEFAULT NULL;
  DECLARE current_team INTEGER DEFAULT NULL;

  SELECT SU_UID INTO id_user FROM T_SGL_USER WHERE LOWER(SU_MAIL)=LOWER(mail);
  
  IF id_user IS NULL THEN
    INSERT INTO T_SGL_USER (SU_ID_PARENT_SU, SU_MAIL, SU_REGISTER_DATE) VALUES (id_lead, mail, NOW());
    SELECT SU_UID INTO id_user FROM T_SGL_USER WHERE SU_UID=LAST_INSERT_ID();
    INSERT INTO T_GAME_USER (GU_ID_SU, GU_ID_G, GU_ID_ST, GU_TYPE) VALUES (id_user, id_game, id_team, type);
    SELECT TRUE as RESULT;
  ELSE
    SELECT GU_ID_ST INTO current_team FROM T_GAME_USER WHERE GU_ID_SU=id_user AND GU_ID_G=id_game;
    IF current_team IS NULL THEN
      IF EXISTS (SELECT * FROM T_GAME_USER WHERE GU_ID_SU=id_user AND GU_ID_G=id_game) THEN
        UPDATE T_GAME_USER SET GU_ID_ST=id_team, GU_TYPE=type WHERE GU_ID_SU=id_user AND GU_ID_G=id_game;
        SELECT TRUE as RESULT;
  	  ELSE
  	    INSERT INTO T_GAME_USER (GU_ID_SU, GU_ID_G, GU_ID_ST, GU_TYPE) VALUES (id_user, id_game, id_team, type);
        SELECT TRUE as RESULT;
      END IF;
    ELSE
      SELECT FALSE as RESULT;
    END IF;
  END IF;
END|