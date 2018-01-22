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

-- Other
DROP PROCEDURE IF EXISTS SELECT_FILE|
DROP PROCEDURE IF EXISTS CONNECT_USER|
DROP PROCEDURE IF EXISTS SELECT_SGL_USER_INFORMATION|
DROP PROCEDURE IF EXISTS SELECT_SONS_SGL_USER_INFORMATION|
DROP PROCEDURE IF EXISTS SELECT_GAMES_WITH_PLATFORM|
DROP PROCEDURE IF EXISTS SELECT_GAME_USER_BY_SU|

-- ************************************************************************************************ --
-- ----------------------------- Create Queries --------------------------------------------------- --
-- ************************************************************************************************ --

-- -------------------------------------------------------------------------------------------------------------------------------------------- --
-- SELECT_FILE
-- ---

CREATE PROCEDURE SELECT_FILE( IN id_file INTEGER)
BEGIN
  SELECT F_UID, F_NAME, F_PATH,F_SIZE, F_TYPE, F_MD5 FROM T_FILE WHERE F_UID=id_file;
END|

-- -------------------------------------------------------------------------------------------------------------------------------------------- --
-- CONNECT_USER
-- Error:1 <= Bad login or password
-- ---

CREATE PROCEDURE CONNECT_USER( IN login_or_mail VARCHAR(512), IN pass VARCHAR(512), IN config_salt VARCHAR(512))
BEGIN
  DECLARE id_user_confirmed INTEGER DEFAULT NULL;

  SELECT SU_UID INTO id_user_confirmed FROM T_SGL_USER
    WHERE (LOWER(SU_LOGIN)=LOWER(login_or_mail) OR LOWER(SU_MAIL)=LOWER(login_or_mail)) AND (SU_PASS=SHA1(CONCAT(CONCAT(SU_SALT,pass),config_salt))) AND (SU_ACTIVATION IS NULL OR SU_ACTIVATION = '');

  IF id_user_confirmed IS NOT NULL THEN
    SELECT TRUE as RESULT, SU_UID,SU_ID_PARENT_SU,SU_ID_CARD_F,SU_ID_S,SU_LOGIN,SU_MAIL,SU_TYPE,SU_GENDER,SU_BIRTH_DATE,SU_FIRST_NAME,SU_LAST_NAME FROM T_SGL_USER WHERE SU_UID=id_user_confirmed;
  ELSE
    SELECT FALSE as RESULT, 1 as ERROR;
  END IF;
END|

-- -------------------------------------------------------------------------------------------------------------------------------------------- --
-- SELECT_SGL_USER_INFORMATION
-- Error:1 <= Access denied
-- ---

CREATE PROCEDURE SELECT_SGL_USER_INFORMATION( IN id_user INTEGER, IN id_user_check INTEGER)
BEGIN
  DECLARE id_user_confirmed INTEGER DEFAULT NULL;

  SELECT SU_UID INTO id_user_confirmed FROM T_SGL_USER WHERE SU_UID=id_user AND (SU_UID=id_user_check OR SU_ID_PARENT_SU=id_user_check);

  IF id_user_confirmed IS NOT NULL THEN
    SELECT TRUE as RESULT, SU_UID, SU_ID_CARD_F, SU_ID_S, SU_MAIL, SU_GENDER, SU_BIRTH_DATE, SU_FIRST_NAME, SU_LAST_NAME FROM T_SGL_USER WHERE SU_UID=id_user_confirmed;
  ELSE
    SELECT FALSE as RESULT;
  END IF;
END|

-- -------------------------------------------------------------------------------------------------------------------------------------------- --
-- SELECT_SONS_SGL_USER_INFORMATION
-- ---

CREATE PROCEDURE SELECT_SONS_SGL_USER_INFORMATION( IN id_user INTEGER )
BEGIN
  SELECT SU_UID, SU_ID_CARD_F, SU_ID_S, SU_MAIL, SU_GENDER, SU_BIRTH_DATE, SU_FIRST_NAME, SU_LAST_NAME FROM T_SGL_USER WHERE SU_ID_PARENT_SU=id_user;
END|

-- -------------------------------------------------------------------------------------------------------------------------------------------- --
-- SELECT_GAMES_WITH_PLATFORM
-- ---

CREATE PROCEDURE SELECT_GAMES_WITH_PLATFORM()
BEGIN
  SELECT G_UID, G_ID_LOGO_F, G_NAME, G_DESCRIPTION, G_RANK_DESCRIPTION, G_USE_PLATEFORM_PSEUDO, P_UID, P_ID_LOGO_F, P_NAME, P_PSEUDO_NAME, P_COMPANY, P_WEBSITE
  FROM T_GAME LEFT JOIN T_PLATFORM ON G_ID_P=P_UID
  ORDER BY P_UID;
END |

-- -------------------------------------------------------------------------------------------------------------------------------------------- --
-- SELECT_PLATFORM_USER
-- ---

CREATE PROCEDURE SELECT_GAME_USER_BY_SU( IN id_user INTEGER, IN id_user_check INTEGER)
BEGIN
  DECLARE id_user_confirmed INTEGER DEFAULT NULL;

  SELECT SU_UID INTO id_user_confirmed FROM T_SGL_USER WHERE SU_UID=id_user AND (SU_UID=id_user_check OR SU_ID_PARENT_SU=id_user_check);

  IF id_user_confirmed IS NOT NULL THEN
    SELECT TRUE as RESULT, PU_UID, PU_ID_P, PU_PSEUDO, GU_UID, GU_ID_G, GU_PSEUDO, GU_RANK FROM T_PLATFORM_USER
    LEFT JOIN T_GAME_USER ON PU_UID=GU_ID_PU
    WHERE PU_ID_SU=id_user_confirmed
    ORDER BY PU_ID_P;
  ELSE
    SELECT FALSE as RESULT;
  END IF;
END|