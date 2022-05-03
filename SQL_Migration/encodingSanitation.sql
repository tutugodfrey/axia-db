SELECT 'SELECT id, '||column_name||'::bytea FROM '||table_name||' WHERE '||column_name|| ' SIMILAR TO ' || E'\'%[^\\x20-\\x7e]+%\';' from information_schema.columns where table_catalog = 'axia' and (data_type = 'character varying' or data_type = 'text') and table_schema = 'public' and column_name != 'id' and column_name not like '%\_id'
CREATE OR REPLACE FUNCTION dbSatinationNotEmptyBYTEA()
RETURNS VOID
AS $$
DECLARE
    queriesArr text[] := array(SELECT 'SELECT id, '||column_name||'::bytea FROM '||table_name||' WHERE '||column_name|| ' SIMILAR TO ' || E'\'%[^\\x20-\\x7e]+%\';' from information_schema.columns where table_catalog = 'axia' and (data_type = 'character varying' or data_type = 'text') and table_schema = 'public' and column_name != 'id' and column_name not like '%\_id');    
    i int;
    qryResultCOL1 RECORD;
    qryResultCOL2 RECORD; 
    col1Data Text;
    col2Data text;

BEGIN    
    FOR i IN array_lower(queriesArr,1) .. array_upper(queriesArr,1)
    LOOP       
       BEGIN
            -- IF we want to append 'limit 2' to speed up the search in the the resulting queries but without actually modifying them 
            -- generate a modified version of the resulting queries on the fly and then EXECUTE them.
            -- (not recommended if needing trap errors during execution)
            -- EXECUTE overlay(queriesArr[i] placing ' limit 2;' from position(';' in queriesArr[i]) for 1) 
            EXECUTE queriesArr[i] into qryResultCOL1;
            IF qryResultCOL1 IS NOT null THEN
                 --col2Data := qryResultCOL;
                 --RAISE NOTICE ' %', regexp_matches(qryResultCOL2,'\\([0-9]{1,3})', 'g');
                 RAISE NOTICE ' %', qryResultCOL1;
                 --RAISE NOTICE ' %', regexp_matches(tst,'\\([0-9]{1,3})', 'g');
            END IF;
            exception when others then 
               RAISE NOTICE 'Invalid input syntax for bytea for query: %', queriesArr[i];
       END;--exception
    END LOOP;
END;
$$ LANGUAGE plpgsql;

SELECT regexp_matches(qryResultCOL1,'\\([0-9]{1,3})', 'g');


CREATE OR REPLACE FUNCTION dbSatinationNotEmptyBYTEA()
RETURNS TEXT
AS $$
DECLARE
    queriesArr text[] := array(SELECT 'SELECT '||column_name||'::bytea FROM '||table_name||' WHERE '||column_name|| ' SIMILAR TO ' || E'\'%[^\\x20-\\x7e]+%\';' from information_schema.columns where table_catalog = 'axia' and (data_type = 'character varying' or data_type = 'text') and table_schema = 'public' and column_name != 'id' and column_name not like '%\_id');    
    i int;
    x text := '';         
    result Text := '';
BEGIN    
    FOR i IN array_lower(queriesArr,1) .. array_upper(queriesArr,1)
        LOOP        
           BEGIN
              FOR x IN EXECUTE(queriesArr[i]) LOOP                
                IF result > '' THEN
                   result := result || '~.~' || regexp_matches(x,'\\([0-9]{1,3})', 'g');
                ELSE
                   result := regexp_matches(x,'\\([0-9]{1,3})', 'g');
                END IF;                
              END LOOP;
             exception when others then 
                   RAISE NOTICE 'Invalid input syntax for bytea for query: %', queriesArr[i];
          END;--exception
    END LOOP;
RETURN result;
END;
$$ LANGUAGE plpgsql;
-------------------------------------------------------------------------------------------------------------
-------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION findAllOCTL_LATIN1Chars()
RETURNS TEXT
AS $$
DECLARE
    queriesArr text[] := array(SELECT 'SELECT '||column_name||'::bytea FROM '||table_name||' WHERE '||column_name|| ' SIMILAR TO ' || E'\'%[^\\x20-\\x7e]+%\';' from information_schema.columns where table_catalog = 'axia' and (data_type = 'character varying' or data_type = 'text') and table_schema = 'public' and column_name != 'id' and column_name not like '%\_id');    
    i int;
    x text := '';         
    result TEXT := '';
    code TEXT:='';
    finalResult text[];
BEGIN    
    FOR i IN array_lower(queriesArr,1) .. array_upper(queriesArr,1)
        LOOP        
           BEGIN
              FOR x IN EXECUTE(queriesArr[i]) LOOP                    
                    FOR code in SELECT regexp_matches(x,'\\([0-9]{1,3})', 'g') LOOP
                        IF result > '' THEN
                            result := result || ',' || trim(leading '{' from trim(trailing '}'from code));
                        ELSE
                            result := trim(leading '{' from trim(trailing '}'from code));
                        END IF;
                    END LOOP;                                
              END LOOP;
             exception when others then 
                   RAISE NOTICE 'Invalid input syntax for bytea for query: %', queriesArr[i];
          END;--exception
    END LOOP;
finalResult := ARRAY(
  select unnest(string_to_array(result, ',')) as e
   union
  select unnest(string_to_array(result, ',')) as e
   order by e);
RETURN finalResult;
END;
$$ LANGUAGE plpgsql;
-------------------------------------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION replaceOCTALCHAR()
RETURNS text
AS $$
DECLARE
    queriesArr text[] := array(SELECT 'SELECT id, '||column_name||'::bytea FROM '||table_name||' WHERE '||column_name|| ' SIMILAR TO ' || E'\'%[^\\x20-\\x7e]+%\';' from information_schema.columns where table_catalog = 'axia' and (data_type = 'character varying' or data_type = 'text') and table_schema = 'public' and column_name != 'id' and column_name not like '%\_id');    
    queryIndex int;
    dataSet text := '';         
    idCol text := ''; 
    result Text;
    code TEXT:='';
    finalResult text;
    cntReplaced bigint:= 0;
    cntRecords bigint:= 0;
    rowsCnt int := 0;
    octalVal INTEGER; --Doesn't have to be in the octal numerical system.
    runOnce BOOLEAN := false;
    updateSuccess BOOLEAN;
BEGIN
    RAISE NOTICE 'Searching using % queries...', array_upper(queriesArr,1);
    FOR queryIndex IN array_lower(queriesArr,1) .. array_upper(queriesArr,1)
        LOOP        
           BEGIN              
              RAISE NOTICE ' >> Executed Query: %', queriesArr[queryIndex];
              FOR idCol, dataSet IN EXECUTE(queriesArr[queryIndex]) LOOP                  
                rowsCnt := rowsCnt + 1;
                    FOR code in SELECT regexp_matches(dataSet,'\\([0-9]{1,3})', 'g') LOOP
                        octalVal := trim(leading '{' from trim(trailing '}'from code)) ::INTEGER;
                        result := getValidChar(octalVal);
                        IF result != 'skip' THEN
                            If runOnce = false then
                                --finalResult := replace( dataSet::bytea::text, E'\\' || octalVal, result);
                                finalResult := dataSet::bytea::text;
                                runOnce := true;
                            END IF;
                            finalResult := replace( finalResult, E'\\' || octalVal, result);
                            cntReplaced := cntReplaced + 1;                            
                        END IF;                      
                    END LOOP;  -- octate codes loop                  
               -- RAISE NOTICE '>> Row % data: %', rowsCnt, dataSet;
               -- RAISE NOTICE '>> Modified data: %', finalResult; --select replace( '~', E'\\'|| '176', chr(126))::bytea;                 
                runOnce := false;
                cntRecords := cntRecords + 1;
                updateSuccess := updateCorruptedRecordsEncoding(queryIndex, finalResult, idCol);
                if updateSuccess = false then
                    RETURN 'FATAL ERROR!: Update failed for table containing UUID: '||idCol||'. Related query: ' || queriesArr[queryIndex];
                ELSE
                    RAISE NOTICE '>> Updated data: %', finalResult;
                end if;
              END LOOP; --dataSet Loop
             exception when others then 
                   RAISE NOTICE 'Invalid input syntax for bytea for query: %', queriesArr[queryIndex];
          END;--exception
     rowsCnt := 0; -- reset count of number of rows returned per query
    END LOOP;
RETURN cntReplaced || ' octet character replacements made in '|| cntRecords ||' records.';
END;
$$ LANGUAGE plpgsql;

select overlay(
(EXCECUTE "SELECT mailing_phone::bytea FROM onlineapp_applications WHERE mailing_phone SIMILAR TO '%[^\x20-\x7e]+%';" placing 'hom' from 2 for 4
);

CREATE OR REPLACE FUNCTION getValidChar(indexNum int)
returns text
AS $$
declare
    finalResult integer[];    
begin
-- The following values reprecent a value that can be passed to the chr(##) internal function which will return a character in the encoding that matches
-- current database encoding.
    finalResult[11] := 9;    finalResult[020] := 32;     finalResult[026] := 32;     finalResult[027] := 32;     finalResult[030] := 32;
    finalResult[031] := 32;  finalResult[177] := 32;     finalResult[200] := 32;     finalResult[205] := 32;     finalResult[211] := 32;
    finalResult[212] := 32;  finalResult[215] := 45;     finalResult[216] := 32;     finalResult[222] := 39;     finalResult[223] := 32;
    finalResult[224] := 32;  finalResult[226] := 32;     finalResult[230] := 32;     finalResult[231] := 32;     finalResult[232] := 32;
    finalResult[235] := 32;  finalResult[240] := 32;     finalResult[242] := 32;     finalResult[251] := 101;    finalResult[253] := 32;
    finalResult[255] := 105; finalResult[277] := 101;    finalResult[326] := 32;     finalResult[342] := 39;     finalResult[351] := 101;
    finalResult[355] := 105;

  
-- The following values are all set to zero so that they can be specifically identified later. 
-- These values are not expected to be replaced by chr(##)
-- 012 015 201 202 204 241 244 260 261 265 270 271 301 302 314 321 324 341 343 347
    finalResult[12] := 0; finalResult[270] := 0;
    finalResult[15] := 0; finalResult[271] := 0;
    finalResult[201] := 0; finalResult[301] := 0;
    finalResult[202] := 0; finalResult[302] := 0;
    finalResult[204] := 0; finalResult[314] := 0;
    finalResult[241] := 0; finalResult[321] := 0;
    finalResult[244] := 0; finalResult[324] := 0;
    finalResult[260] := 0; finalResult[341] := 0;
    finalResult[261] := 0; finalResult[343] := 0;
    finalResult[265] := 0; finalResult[347] := 0;
    finalResult[274] := 0;

-- Handle special cases first
if indexNum = 274 then
    return '1/4';
end if;
if finalResult[indexNum] = 0 then
    return 'skip';
END if;
-- Handle all other cases
if finalResult[indexNum] is null then
    return '';
ELSE
    return chr(finalResult[indexNum]);
END if;
END;
$$ LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION updateCorruptedRecordsEncoding(queryIndx int, sanitizedDataSet text, uuidID varchar(36))
RETURNS BOOLEAN
AS $$
DECLARE
    updatesQryArr text[] := array(SELECT 'UPDATE '||table_name||' SET '||column_name||'=\''||sanitizedDataSet|| '\' where ID=' ||quote_literal(uuidID) ||';' from information_schema.columns where table_catalog = 'axia' and (data_type = 'character varying' or data_type = 'text') and table_schema = 'public' and column_name != 'id' and column_name not like '%\_id');        
BEGIN
    BEGIN
    EXECUTE updatesQryArr[queryIndx];
    exception when others then 
        RAISE NOTICE 'Update failed for table containing UUID: %. Related query: %', uuidID, updatesQryArr[queryIndx]; --if error ocurs
    END;--exception
RETURN true;
END;
$$ LANGUAGE plpgsql;