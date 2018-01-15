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
DROP PROCEDURE IF EXISTS DELETE_FILE|

-- ************************************************************************************************ --
-- ----------------------------- Create Queries --------------------------------------------------- --
-- ************************************************************************************************ --

-- -------------------------------------------------------------------------------------------------------------------------------------------- --
-- DELETE_FILE
-- ---

CREATE PROCEDURE DELETE_FILE(IN id_file INTEGER)
BEGIN
  DECLARE id_file_check INTEGER DEFAULT NULL;

  DELETE FROM T_FILE WHERE F_UID=id_file;

  SELECT F_UID INTO id_file_check FROM T_FILE WHERE F_UID=id_file;

  IF id_file_check IS NOT NULL THEN
    SELECT FALSE as RESULT;
  ELSE
    SELECT TRUE as RESULT;
  END IF;
END |