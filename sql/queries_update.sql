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
DROP PROCEDURE IF EXISTS UPDATE_SGL_USER_RESET_PASS|
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
  DECLARE pass_hash VARCHAR(512) DEFAULT NULL;

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
CREATE PROCEDURE UPDATE_SGL_USER_INFORMATION( IN id_user INTEGER, IN id_user_check INTEGER, IN id_file_card INTEGER, IN id_school INTEGER,
                                              IN mail VARCHAR(256), IN gender INTEGER(2), IN birth_date DATE,
                                              IN first_name VARCHAR(128), IN last_name VARCHAR(128))
BEGIN
  DECLARE id_user_confirmed INTEGER DEFAULT NULL;

  SELECT SU_UID INTO id_user_confirmed FROM T_SGL_USER WHERE SU_UID=id_user AND (SU_UID=id_user_check OR SU_ID_PARENT_SU=id_user_check);

  IF id_user_confirmed IS NOT NULL THEN
    UPDATE T_SGL_USER SET SU_ID_CARD_F=id_file_card, SU_ID_S=id_school, SU_MAIL=mail, SU_GENDER=gender, SU_BIRTH_DATE=birth_date, SU_FIRST_NAME=first_name, SU_LAST_NAME=last_name WHERE SU_UID=id_user_confirmed;
    SELECT TRUE as RESULT, SU_UID, SU_ID_CARD_F, SU_ID_S, SU_MAIL, SU_GENDER, SU_BIRTH_DATE, SU_FIRST_NAME, SU_LAST_NAME FROM T_SGL_USER WHERE SU_UID=id_user_confirmed;
  ELSE
    SELECT FALSE as RESULT;
  END IF;
END |

-- -------------------------------------------------------------------------------------------------------------------------------------------- --
-- UPDATE_SGL_USER_RESET_PASS
-- ---
CREATE PROCEDURE UPDATE_SGL_RESET_PASS( IN login VARCHAR(128), IN new_salt VARCHAR(512), IN config_salt VARCHAR(512), IN reset_pass VARCHAR(128), IN new_pass VARCHAR(128))
BEGIN
  DECLARE id_user_confirmed INTEGER DEFAULT NULL;

  SELECT SU_UID INTO id_user_confirmed FROM T_SGL_USER WHERE SU_LOGIN=login AND SU_RESETPASS=reset_pass;

  IF id_user_confirmed IS NOT NULL THEN
    UPDATE T_SGL_USER SET SU_PASS=SHA1(CONCAT(CONCAT(new_salt,new_pass),config_salt)), SU_SALT=new_salt, SU_RESETPASS=NULL WHERE SU_UID=id_user_confirmed;
    SELECT TRUE as RESULT;
  ELSE
    SELECT FALSE as RESULT;
  END IF;
END |