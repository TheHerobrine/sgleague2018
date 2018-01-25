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

-- Update procedure
DROP PROCEDURE IF EXISTS UPDATE_SGL_USER_PASS|
DROP PROCEDURE IF EXISTS UPDATE_SGL_USER_INFORMATION|
DROP PROCEDURE IF EXISTS UPDATE_SGL_USER_GAME|
DROP PROCEDURE IF EXISTS UPDATE_SGL_USER_PLATFORM|
DROP PROCEDURE IF EXISTS UPDATE_SGL_USER_RESET_PASS|
DROP PROCEDURE IF EXISTS UPDATE_SGL_USER_CARD|
DROP PROCEDURE IF EXISTS UPDATE_SGL_TEAM_LOGO|
DROP PROCEDURE IF EXISTS UPDATE_SGL_USER_LEAVES_TEAM|
-- TODO: UPDATE_PLATFORM_USER_INFORMATION
-- TODO: UPDATE_GAME_USER_INFORMATION
-- TODO: UPDATE_TEAM_INFORMATION
-- TODO: UPDATE_TEAM_REQUEST
-- TODO: UPDATE_SCHOOL

-- Other
-- TODO: VALIDATE_USER_MAIL
-- TODO: MARK_READ_TEAM_REQUEST

-- ************************************************************************************************ --
-- ----------------------------- Create Queries --------------------------------------------------- --
-- ************************************************************************************************ --

-- -------------------------------------------------------------------------------------------------------------------------------------------- --
-- UPDATE_SGL_USER_PASS
-- ---
CREATE PROCEDURE UPDATE_SGL_USER_PASS( IN id_user INTEGER, IN old_pass VARCHAR(512), IN reset_pass VARCHAR(512), IN new_pass VARCHAR(512), IN new_salt VARCHAR(512), IN config_salt VARCHAR(512))
BEGIN
  DECLARE id_user_confirmed INTEGER DEFAULT NULL;

  SELECT SU_UID INTO id_user_confirmed FROM T_SGL_USER WHERE SU_UID=id_user AND (SU_PASS=SHA1(CONCAT(CONCAT(SU_SALT,old_pass),config_salt)) OR SU_RESETPASS=reset_pass);

  IF id_user_confirmed IS NOT NULL THEN
    UPDATE T_SGL_USER SET SU_PASS=SHA1(CONCAT(CONCAT(new_salt,new_pass),config_salt)), SU_SALT=new_salt WHERE SU_UID=id_user_confirmed;
    SELECT TRUE as RESULT;
  ELSE
    SELECT FALSE as RESULT;
  END IF;
END |

-- -------------------------------------------------------------------------------------------------------------------------------------------- --
-- UPDATE_SGL_USER_INFORMATION
-- ---
CREATE PROCEDURE UPDATE_SGL_USER_INFORMATION( IN id_user INTEGER, IN id_user_check INTEGER, IN school VARCHAR(128), IN gender INTEGER(2), IN birth_date DATE,
                                              IN first_name VARCHAR(128), IN last_name VARCHAR(128))
BEGIN
  DECLARE id_user_confirmed INTEGER DEFAULT NULL;
  DECLARE school_id INTEGER DEFAULT NULL;

  SELECT SU_UID INTO id_user_confirmed FROM T_SGL_USER WHERE SU_UID=id_user AND (SU_UID=id_user_check OR SU_ID_PARENT_SU=id_user_check);

  SELECT S_UID INTO school_id FROM T_SCHOOL WHERE LOWER(TRIM(S_NAME))=LOWER(TRIM(school));
  IF school_id IS NULL THEN
    INSERT INTO `T_SCHOOL` (`S_NAME`) VALUES (school);
    SELECT S_UID INTO school_id FROM T_SCHOOL WHERE S_UID=LAST_INSERT_ID();
  END IF;

  IF id_user_confirmed IS NOT NULL THEN
    UPDATE T_SGL_USER SET SU_ID_S=school_id, SU_GENDER=gender, SU_BIRTH_DATE=birth_date, SU_FIRST_NAME=first_name, SU_LAST_NAME=last_name WHERE SU_UID=id_user_confirmed;
    SELECT TRUE as RESULT;
  ELSE
    SELECT FALSE as RESULT;
  END IF;
END |

-- -------------------------------------------------------------------------------------------------------------------------------------------- --
-- UPDATE_SGL_USER_GAME
-- ---
CREATE PROCEDURE UPDATE_SGL_USER_GAME( IN id_user INTEGER, IN id_user_check INTEGER, IN id_game INTEGER, IN rank INTEGER)
BEGIN
  DECLARE id_user_confirmed INTEGER DEFAULT NULL;

  SELECT SU_UID INTO id_user_confirmed FROM T_SGL_USER WHERE SU_UID=id_user AND (SU_UID=id_user_check OR SU_ID_PARENT_SU=id_user_check);

  IF id_user_confirmed IS NOT NULL THEN
  	IF EXISTS (SELECT * FROM T_GAME_USER WHERE GU_ID_SU=id_user AND GU_ID_G=id_game) THEN
  	  UPDATE T_GAME_USER SET GU_RANK=rank WHERE GU_ID_SU=id_user AND GU_ID_G=id_game;
	ELSE
	  INSERT INTO T_GAME_USER (GU_ID_SU, GU_ID_G, GU_RANK) VALUES (id_user, id_game, rank);
    END IF;
    SELECT TRUE as RESULT;
  ELSE
    SELECT FALSE as RESULT;
  END IF;
END |

-- -------------------------------------------------------------------------------------------------------------------------------------------- --
-- UPDATE_SGL_USER_PLATFORM
-- ---
CREATE PROCEDURE UPDATE_SGL_USER_PLATFORM( IN id_user INTEGER, IN id_user_check INTEGER, IN id_platform INTEGER, IN pseudo VARCHAR(128))
BEGIN
  DECLARE id_user_confirmed INTEGER DEFAULT NULL;

  SELECT SU_UID INTO id_user_confirmed FROM T_SGL_USER WHERE SU_UID=id_user AND (SU_UID=id_user_check OR SU_ID_PARENT_SU=id_user_check);

  IF id_user_confirmed IS NOT NULL THEN
    IF EXISTS (SELECT * FROM T_PLATFORM_USER WHERE PU_ID_SU=id_user AND PU_ID_P=id_platform) THEN
      UPDATE T_PLATFORM_USER SET PU_PSEUDO=pseudo WHERE PU_ID_SU=id_user AND PU_ID_P=id_platform;
	ELSE
	  INSERT INTO T_PLATFORM_USER (PU_ID_SU, PU_ID_P, PU_PSEUDO) VALUES (id_user, id_platform, pseudo);
    END IF;
    SELECT TRUE as RESULT;
  ELSE
    SELECT FALSE as RESULT;
  END IF;
END |

-- -------------------------------------------------------------------------------------------------------------------------------------------- --
-- UPDATE_SGL_USER_RESET_PASS
-- ---
CREATE PROCEDURE UPDATE_SGL_USER_RESET_PASS( IN login VARCHAR(128), IN new_salt VARCHAR(512), IN config_salt VARCHAR(512), IN reset_pass VARCHAR(128), IN new_pass VARCHAR(128))
BEGIN
  DECLARE id_user_confirmed INTEGER DEFAULT NULL;

  SELECT SU_UID INTO id_user_confirmed FROM T_SGL_USER WHERE SU_LOGIN=login AND SU_RESETPASS=reset_pass;

  IF id_user_confirmed IS NOT NULL THEN
    UPDATE T_SGL_USER SET SU_PASS=SHA1(CONCAT(CONCAT(new_salt,new_pass),config_salt)), SU_SALT=new_salt, SU_RESETPASS=NULL WHERE SU_UID=id_user_confirmed;
    SELECT TRUE as RESULT, SU_MAIL FROM T_SGL_USER WHERE SU_UID=id_user_confirmed;
  ELSE
    SELECT FALSE as RESULT;
  END IF;
END |

-- -------------------------------------------------------------------------------------------------------------------------------------------- --
-- UPDATE_SGL_USER_CARD
-- ---

CREATE PROCEDURE UPDATE_SGL_USER_CARD( IN id_user INTEGER, IN id_card INTEGER)
  BEGIN
    DECLARE id_old_card INTEGER DEFAULT 0;

    SELECT SU_ID_CARD_F INTO id_old_card FROM T_SGL_USER WHERE SU_UID=id_user;

    UPDATE T_SGL_USER SET SU_ID_CARD_F=id_card WHERE SU_UID=id_user;

    IF id_old_card > 0 THEN
      SELECT TRUE as RESULT, TRUE as TO_DELETE, id_old_card as FILE;
    ELSE
      SELECT TRUE as RESULT, FALSE as TO_DELETE;
    END IF;
  END|

-- -------------------------------------------------------------------------------------------------------------------------------------------- --
-- UPDATE_SGL_TEAM_LOGO
-- ---

CREATE PROCEDURE UPDATE_SGL_TEAM_LOGO( IN id_user INTEGER, IN id_game INTEGER, IN id_logo INTEGER)
  BEGIN
    DECLARE id_old_logo INTEGER DEFAULT 0;

    SELECT ST_ID_PICTURE_F INTO id_old_logo FROM T_SGL_TEAM WHERE ST_ID_LEAD_SU=id_user AND ST_ID_G=id_game;

    UPDATE T_SGL_TEAM SET ST_ID_PICTURE_F=id_logo WHERE ST_ID_LEAD_SU=id_user AND ST_ID_G=id_game;

    IF id_old_logo > 0 THEN
      SELECT TRUE as RESULT, TRUE as TO_DELETE, id_old_logo as FILE;
    ELSE
      SELECT TRUE as RESULT, FALSE as TO_DELETE;
    END IF;
  END|

-- -------------------------------------------------------------------------------------------------------------------------------------------- --
-- UPDATE_SGL_USER_LEAVES_TEAM
-- ---

CREATE PROCEDURE UPDATE_SGL_USER_LEAVES_TEAM( IN id_user INTEGER, IN id_game INTEGER)
  BEGIN
    DECLARE is_lead BOOL DEFAULT FALSE;
    DECLARE id_team INTEGER DEFAULT NULL;
    DECLARE id_file INTEGER DEFAULT NULL;

    SELECT ST_UID, ST_ID_PICTURE_F,(ST_ID_LEAD_SU=id_user) INTO id_team, id_file, is_lead FROM T_SGL_TEAM
    JOIN T_GAME_USER ON GU_ID_ST=ST_UID
    WHERE GU_ID_SU=id_user AND GU_ID_G=id_game;

    IF is_lead IS TRUE THEN

      UPDATE T_GAME_USER SET GU_ID_ST=NULL WHERE GU_ID_ST=id_team;
      DELETE FROM T_SGL_TEAM WHERE ST_UID=id_team;

      IF id_file IS NOT NULL THEN
        SELECT TRUE as RESULT, TRUE as TO_DELETE, id_file as FILE;
      ELSE
        SELECT TRUE as RESULT, FALSE as TO_DELETE;
      END IF ;

    ELSE
      SELECT TRUE as RESULT, FALSE as TO_DELETE;
    END IF;
  END |