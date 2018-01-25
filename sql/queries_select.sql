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
DROP PROCEDURE IF EXISTS SEARCH_SCHOOL|
DROP PROCEDURE IF EXISTS SEARCH_MAIL|

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

CREATE PROCEDURE CONNECT_USER( IN login_or_mail VARCHAR(512) CHARACTER SET utf8 COLLATE utf8_unicode_ci, IN pass VARCHAR(512) CHARACTER SET utf8 COLLATE utf8_unicode_ci, IN config_salt VARCHAR(512) CHARACTER SET utf8 COLLATE utf8_unicode_ci)
BEGIN
  DECLARE id_user_confirmed INTEGER DEFAULT NULL;

  SELECT SU_UID INTO id_user_confirmed FROM T_SGL_USER
    WHERE (LOWER(SU_LOGIN)=LOWER(login_or_mail) OR LOWER(SU_MAIL)=LOWER(login_or_mail)) AND (SU_PASS=SHA1(CONCAT(CONCAT(SU_SALT,pass),config_salt))) AND (SU_ACTIVATION IS NULL OR SU_ACTIVATION = '');

  IF id_user_confirmed IS NOT NULL THEN
    SELECT TRUE as RESULT, SU_UID,SU_LOGIN,SU_MAIL,SU_TYPE FROM T_SGL_USER WHERE SU_UID=id_user_confirmed;
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
    SELECT TRUE as RESULT, SU_UID, SU_LOGIN, SU_ID_CARD_F, SU_ID_S, SU_MAIL, SU_GENDER, SU_BIRTH_DATE, SU_FIRST_NAME, SU_LAST_NAME, S_NAME
    FROM T_SGL_USER
    LEFT JOIN T_SCHOOL ON S_UID=SU_ID_S
    WHERE SU_UID=id_user_confirmed;
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

CREATE PROCEDURE SELECT_GAME_USER_BY_SU( IN id_user INTEGER)
BEGIN
  SELECT PU_PSEUDO, P_UID, P_NAME, P_PSEUDO_NAME, P_COMPANY, G_NAME, G_RANK_DESCRIPTION, G_UID, GU_RANK FROM T_PLATFORM
  LEFT JOIN T_PLATFORM_USER ON PU_ID_P=P_UID AND PU_ID_SU=id_user LEFT JOIN T_GAME ON G_ID_P=P_UID LEFT JOIN T_GAME_USER ON GU_ID_SU=id_user AND GU_ID_G=G_UID ORDER BY P_UID;
END|

-- -------------------------------------------------------------------------------------------------------------------------------------------- --
-- SEARCH_SCHOOL
-- ---

CREATE PROCEDURE SEARCH_SCHOOL( IN school VARCHAR(512) CHARACTER SET utf8 COLLATE utf8_unicode_ci )
BEGIN
  SELECT S_NAME, S_COUNTRY FROM T_SCHOOL WHERE S_NAME LIKE school ORDER BY school LIMIT 0,10;
END|

-- -------------------------------------------------------------------------------------------------------------------------------------------- --
-- SEARCH_MAIL
-- ---

CREATE PROCEDURE SEARCH_MAIL( IN mail VARCHAR(512), IN id_game INTEGER )
BEGIN
  SELECT SU_LOGIN, SU_UID, PU_PSEUDO, GU_RANK, SU_NAME, SU_FIRST_NAME, SU_LAST_NAME FROM T_SGL_USER LEFT JOIN T_SCHOOL ON SU_ID_S=S_UID JOIN T_GAME ON id_game=G_UID
  LEFT JOIN T_PLATFORM_USER ON PU_ID_P=G_ID_P AND PU_ID_SU=SU_UID LEFT JOIN T_GAME_USER ON GU_ID_G=id_game WHERE LOWER(SU_MAIL)=LOWER(mail);
END|