DROP PROCEDURE IF EXISTS `dianziScore`;
delimiter //
CREATE PROCEDURE dianziScore(IN departid INT)

BEGIN
DECLARE Done  INT DEFAULT  0;

DECLARE dead_time1    datetime;
DECLARE light_status1 INT;
DECLARE id1 INT;

/* 声明游标  */
DECLARE rs CURSOR  FOR  SELECT id, dead_time,light_status FROM  og_dianzixiaoneng_task where light_status!=0 and assign_to_departid=departid and sub_proccess!=4;

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
             /*超过7天的时候，变红灯,扣3分*/
     IF   DATEDIFF (CURRENT_DATE ( ) ,DATE(dead_time1) ) = 8 THEN

       if  light_status1 !=4 then
               UPDATE og_dianzixiaoneng_tasks SET light_status =4  WHERE  id= id1;
               UPDATE  og_department
             SET xiaoneng_score =xiaoneng_score-3  WHERE  depart_id = departid;

               INSERT INTO og_score_detail  ( `depart_id`, `minus`, `task_id`, `minus_time`) VALUES (departid , 3,id1, NOW());
        end if;


     ELSE

       /*超过1天的时候，变黄灯灯,三个扣2分*/

        IF DATEDIFF (CURRENT_DATE ( ) ,DATE(dead_time1) ) =1   THEN
             if  light_status1 !=3 then
               UPDATE og_dianzixiaoneng_tasks SET light_status =3  WHERE  id= id1;
               UPDATE  og_department
                SET xiaoneng_score =xiaoneng_score-1  WHERE  depart_id = departid;

               INSERT INTO og_score_detail  ( `depart_id`, `minus`, `task_id`, `minus_time`) VALUES (departid , 1,id, NOW());

             end if;


        END IF;

     END IF;/*end of 8*/
    end if;/* end of lightstatus !=1*/

END IF;/* end of not done*/

FETCH  NEXT  FROM rs INTO id1, dead_time1,light_status1;

UNTIL Done END REPEAT;

/* 关闭游标 */
CLOSE rs;
END //
delimiter ;
// yaoxie2科
call dianziScore(5);
