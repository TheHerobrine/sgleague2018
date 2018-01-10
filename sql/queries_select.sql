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
DROP PROCEDURE IF EXISTS CONNECT_USER;

-- ************************************************************************************************ --
-- ----------------------------- Create Queries --------------------------------------------------- --
-- ************************************************************************************************ --

-- -------------------------------------------------------------------------------------------------------------------------------------------- --
-- CONNECT_USER
-- Error:1 <= Bad login or password
-- ---

CREATE PROCEDURE CONNECT_USER( IN login_or_mail INTEGER, IN pass VARCHAR(512), IN config_salt VARCHAR(512))
BEGIN
  DECLARE id_user_confirmed INTEGER DEFAULT NULL;

  SELECT SU_UID INTO id_user_confirmed FROM T_SGL_USER
    WHERE (SU_LOGIN=login_or_mail OR SU_MAIL=login_or_mail) AND (SU_PASS=SHA1(CONCAT(CONCAT(SU_SALT,pass),config_salt)));

  IF id_user_confirmed IS NOT NULL THEN
    SELECT TRUE as RESULT, SU_UID,SU_ID_PARENT_SU,SU_ID_CARD_F,SU_ID_S,SU_LOGIN,SU_MAIL,SU_TYPE,SU_GENDER,SU_BIRTH_DATE,SU_FIRST_NAME,SU_LAST_NAME FROM T_SGL_USER WHERE SU_UID=id_user_confirmed;
  ELSE
    SELECT FALSE as RESULT, 1 as ERROR;
  END IF;
END |