DROP PROCEDURE IF EXISTS `dianziScore`;
delimiter //
CREATE PROCEDURE dianziScore(IN departid INT,IN sub_processes varchar(50))

BEGIN
DECLARE Done  INT DEFAULT  0;

DECLARE dead_time1    datetime;
DECLARE light_status1 INT;
DECLARE id1 INT;

/* 声明游标  */
DECLARE rs CURSOR  FOR  SELECT id, dead_time,light_status FROM  og_dianzixiaoneng_task where light_status!=1 and task_process=4;

/* 异常处理  1,4,21,23,5,6,9,8,27*/
DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET Done = 1;


/* 打开游标 */
OPEN rs;

/* 逐个取出当前记录 LingQi字段的值，需要进行最大值的判断  */
FETCH  NEXT  FROM  rs  INTO id1, dead_time1,light_status1;
/* 遍历数据表  */
REPEAT
IF  NOT Done THEN
    /*1的时候任务已经完成，不用管*/
    if light_status1 != 1 then
             /*筹备期期限到了，自动变成完成*/
     IF   DATEDIFF (CURRENT_DATE ( ) ,DATE(dead_time1) ) = 0 THEN
               UPDATE og_dianzixiaoneng_tasks SET light_status =1,result=1,complete_time=now()  WHERE  id= id1;
     ELSE

     END IF;
    end if;

END IF;/* end of not done*/

FETCH  NEXT  FROM rs INTO id1, dead_time1,light_status1;

UNTIL Done END REPEAT;

/* 关闭游标 */
CLOSE rs;
END //
delimiter ;
