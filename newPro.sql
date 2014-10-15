delimiter //
drop procedure if exists p1 //
CREATE PROCEDURE p1()
BEGIN
 DECLARE Done  INT DEFAULT  0;

 DECLARE due_date1    datetime;
 DECLARE light_status1 INT;
 DECLARE id1 INT;
 DECLARE yellow_task_num INT; 
 DECLARE yellow_task VARCHAR(1000) ;
 
 DECLARE assigned_to_departid1 INT;
 /* 声明游标  */
 DECLARE rs CURSOR  FOR  SELECT id, due_date,light_status, assigned_to_departid  FROM  og_project_tasks;
 
 /* 声明游标  */
 DECLARE rs1 CURSOR  FOR  SELECT yellow_to_minus, task_to_minus  FROM  og_department where depart_id=assigned_to_departid1;
 
/* 异常处理  */
DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET Done = 1;


/* 打开游标 */
OPEN rs;  

/* 逐个取出当前记录 LingQi字段的值，需要进行最大值的判断  */
FETCH  NEXT  FROM  rs  INTO id1, due_date1,light_status1,assigned_to_departid1;     
/* 遍历数据表  */
 REPEAT
IF  NOT Done THEN
    /*1的时候任务已经完成，不用管*/
    if light_status1 != 1 then
             /*超过7天的时候，变红灯,扣3分*/
     IF   DATEDIFF (CURRENT_DATE ( ) ,DATE(due_date1) ) = 7 THEN
        
       if  light_status1 !=4 then
			 UPDATE og_project_tasks SET light_status =4  WHERE  id= id1;
			 UPDATE  og_department
             SET score =score-3  WHERE  depart_id = assigned_to_departid1;
        
			INSERT INTO og_score_detail  ( `depart_id`, `minus`, `task_id`, `minus_time`) VALUES (assigned_to_departid1 , 3,id1, NOW());
        end if;
     

     ELSE 
  
       /*超过1天的时候，变黄灯灯,三个扣2分*/
   
        IF DATEDIFF (CURRENT_DATE ( ) ,DATE(due_date1) ) =1   THEN
             if  light_status1 !=3 then
				UPDATE og_project_tasks SET light_status =3  WHERE  id= id1;
				/*task_to_minus用来记录还没扣分的任务id*/
                UPDATE  og_department SET yellow_to_minus =yellow_to_minus+1,task_to_minus =CONCAT(task_to_minus,',',id1)  WHERE  depart_id = assigned_to_departid1;
				/* 打开游标 */
                OPEN rs1;  
                FETCH  NEXT  FROM  rs1  INTO yellow_task_num, yellow_task;   
				REPEAT
                IF  NOT Done THEN  
				    /*已经有三个个黄灯任务了，可以2扣分了*/
				    if yellow_task_num =3 then
						
						INSERT INTO og_score_detail  ( `depart_id`, `minus`, `task_id`, `minus_time`) VALUES (assigned_to_departid1 , 2,yellow_task, NOW());
						UPDATE  og_department SET score =score-2 ,yellow_to_minus=0,task_to_minus='' WHERE  depart_id = assigned_to_departid1;
					end if;
			    end if;
				FETCH  NEXT  FROM  rs1  INTO yellow_task_num, yellow_task;

                UNTIL Done END REPEAT;
				CLOSE rs1;
	            SET Done=0;
				
             end if;
 
         
        END IF;
     
     END IF;/*end of 8*/
    end if;/* end of lightstatus !=1*/
   
END IF;/* end of not done*/

FETCH  NEXT  FROM rs INTO id1, due_date1,light_status1,assigned_to_departid1;

 UNTIL Done END REPEAT;

/* 关闭游标 */
CLOSE rs;
 INSERT INTO og_procedure_detail  ( `add_time`) VALUES (NOW());
END;//
delimiter ;
call p1()

