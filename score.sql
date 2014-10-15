delimiter //
CREATE PROCEDURE p2()
BEGIN
 DECLARE Done  INT DEFAULT  0;

 DECLARE due_date1 datetime;
 DECLARE light_status1 INT;
 DECLARE id1 INT;

 DECLARE assigned_to_departid1 INT;
 /* 声明游标  */
DECLARE rs CURSOR  FOR  SELECT departid  FROM  og_project_tasks;
/* 异常处理  */
DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET Done = 1;


/* 打开游标 */
OPEN rs;  


FETCH  NEXT  FROM  rs  INTO id1, due_date1,light_status1,assigned_to_departid1;     
/* 遍历数据表  */
 REPEAT
IF  NOT Done THEN
    /*1的时候任务已经完成，不用管*/
    if light_status1 != 1 then
             /*超过7天的时候，变红灯*/
     IF   DATEDIFF (CURRENT_DATE ( ) ,DATE(due_date1) ) >= 8 THEN
        
       if  light_status1 !=4 then
 UPDATE og_project_tasks SET light_status =4  WHERE  id= id1;

         end if;
     

     ELSE  
  
       /*超过规定时间，变黄色*/
   
      IF DATEDIFF (CURRENT_DATE ( ) ,DATE(due_date1) ) >=1   THEN
             if  light_status1 !=3 then
   UPDATE og_project_tasks SET light_status =3  WHERE  id= id1;

             end if;
 
         
      END IF;


     
     END IF;
    end if;
   
END IF;

FETCH  NEXT  FROM rs INTO id1, due_date1,light_status1,assigned_to_departid1;

 UNTIL Done END REPEAT;

/* 关闭游标 */
CLOSE rs;
 INSERT INTO og_procedure_detail  ( `add_time`) VALUES (NOW());
END;//
delimiter ;
