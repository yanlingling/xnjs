delimiter //
CREATE PROCEDURE change_light_status9()
BEGIN
 DECLARE Done  INT DEFAULT  0;

 DECLARE due_date1    datetime;
 DECLARE light_status1 INT;
 DECLARE id1 INT;

 DECLARE assigned_to_departid1 INT;
 /* 声明游标  */
 DECLARE rs CURSOR  FOR  SELECT id, due_date,light_status, assigned_to_departid  FROM  og_project_tasks;
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
             /*超过7天的时候，变黄灯,扣2分*/
     IF   DATEDIFF (CURRENT_DATE ( ) ,DATE(due_date1) ) = 8 THEN
 
        UPDATE og_project_tasks SET light_status =3  WHERE  id= id1;
       UPDATE og_department SET score =score-2  WHERE  depart_id = assigned_to_departid1;
        INSERT INTO og_score_detail  ( `depart_id`, `minus`, `task_id`, `minus_time`) VALUES (assigned_to_departid1 , 2,id1, CURRENT_DATE ( ));
     ELSE 
  
       /*超过14天的时候，变红灯*/
   
      IF DATEDIFF (CURRENT_DATE ( ) ,DATE(due_date1) ) =15   THEN

 
            UPDATE og_project_tasks SET light_status =4  WHERE  id= id1;

          UPDATE og_department SET score =score-5  WHERE  depart_id = assigned_to_departid1;
         INSERT INTO og_score_detail  (`depart_id`, `minus`, `task_id`, `minus_time`) VALUES (assigned_to_departid1 ,  5,id1, CURRENT_DATE ( ));
      END IF;

     END IF;
    end if;
   
END IF;

FETCH  NEXT  FROM rs INTO id1, due_date1,light_status1,assigned_to_departid1;

 UNTIL Done END REPEAT;

/* 关闭游标 */
CLOSE rs;
END;//
delimiter ;
call change_light_status9()

