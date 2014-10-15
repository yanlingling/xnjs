delimiter $$
drop procedure if exists findErrorLifeRoute $$
CREATE PROCEDURE findErrorLifeRoute()   
BEGIN
declare routeId,routeStopId BIGINT   ; 
declare str,tmp_street VARCHAR(255)  ; 
DECLARE l_done,num,life,localId,tmp_localId INT DEFAULT  0;
-- 声明两个游标，第二个游标使用到第一个的查询结果
DECLARE grade_csr cursor  FOR SELECT route_id FROM routes where   LIFE_CYCLE_STATUS<39998 and LIFE_CYCLE_STATUS>35001;
DECLARE class_csr cursor  FOR SELECT LOCATION_INNER_ID,STREET FROM route_stops  WHERE ROUTE_ID=routeId order by STOP_NUMBER asc;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET l_done=1;
  OPEN grade_csr;
-- 第一个循环
  grade_loop: LOOP   -- Loop through org_grade
    FETCH grade_csr into routeId;
    IF l_done=1 THEN
       LEAVE grade_loop;
    END IF;
        SET num=0;
        set tmp_street=null;
        set tmp_localId=0;
    OPEN class_csr;
-- 第二个循环
    class_loop: LOOP      -- Loop through class in grade.
      FETCH class_csr INTO localId,str;
      IF l_done=1 THEN
         LEAVE class_loop;
      END IF; 
     if localId =tmp_localId or (str is not null and str=tmp_street) THEN
                set tmp_street=str;
                set tmp_localId=localId;
            else
                set num=num+1;
                set tmp_street=str;
                set tmp_localId=localId;
        end if;      
-- 结束第一个循环
    END LOOP class_loop;
    CLOSE class_csr;
    SET l_done=0;
        set life=36000+(num-1)*100+6; 
        INSERT into route_tmp (select ROUTE_ID,LIFE_CYCLE_STATUS,num,life,ACCOUNT_ID,str from routes r where r.ROUTE_ID=routeId and LIFE_CYCLE_STATUS=life  );
-- 结束第二个循环
  END LOOP grade_loop;
  CLOSE grade_csr;
END $$
delimiter ;
