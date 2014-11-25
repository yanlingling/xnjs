DROP PROCEDURE IF EXISTS `computeTaskScore`;
delimiter //
CREATE PROCEDURE computeTaskScore(IN taskid INT)
/**
*进行岗位职责得分相关的计算
1 对于30天效能办和副局长未评价的任务，自动中评
2 计算每个任务的基本评价分
3 计算科室的最终效能考核分
**/

BEGIN
  DECLARE Done INT DEFAULT 0;

  DECLARE id1 INT;
  DECLARE due_date1 datetime;
  DECLARE completed_on1 datetime;
  DECLARE comment_status_xiaoneng1 INT;
  DECLARE comment_status_fujuzhang1 INT;
  DECLARE comment_status_juzhang1 INT;
  DECLARE tuijian1 INT;
  DECLARE pishi1 INT;
  DECLARE comment_xiaoneng_deadline1 datetime;
  DECLARE comment_fujuzhang_deadline1 datetime;
  DECLARE assigned_to_departid1 INT;
  DECLARE xiaoneng_score1 INT;
  DECLARE tuijian_num INT;
  DECLARE pishi_num INT;
  DECLARE task_num INT;
  DECLARE base_score INT;
  DECLARE comment_num INT;
  DECLARE comment_score INT;
  DECLARE score INT;

/* 声明游标
 已经删除
 未完成的任务
 不计算
 */
  DECLARE rs CURSOR FOR SELECT
                          id,
                          due_date,
                          completed_on,
                          comment_status_xiaoneng,
                          comment_status_fujuzhang,
                          comment_status_juzhang,

                          tuijian,
                          pishi,
                          comment_xiaoneng_deadline,
                          comment_fujuzhang_deadline,
                            assigned_to_departid,xiaoneng_score
                        FROM og_project_tasks
                        WHERE id= taskid;

/* 异常处理  */
  DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET Done = 1;


/* 打开游标 */
  OPEN rs;
 set comment_score =0;
 set task_num =0;
/* 逐个取出当前记录 LingQi字段的值，需要进行最大值的判断  */
  FETCH NEXT FROM RS INTO id1, due_date1, completed_on1,
  comment_status_xiaoneng1,comment_status_fujuzhang1, comment_status_juzhang1,
  tuijian1, pishi1, comment_xiaoneng_deadline1,comment_fujuzhang_deadline1, assigned_to_departid1,xiaoneng_score1;
/* 遍历数据表  */
  REPEAT
    IF NOT Done
    THEN
            SET comment_num = 0, comment_score = 0, base_score = 0;
            IF DATE(completed_on1) > DATE(due_date1)
            THEN
              SET base_score = 30;
            ELSE
              SET base_score = 50;
            END IF;

            IF comment_status_xiaoneng1 != 0
            THEN
              SET comment_num = comment_num + 1,
              comment_score = comment_score + (50 - (comment_status_xiaoneng1 - 1) * 10);
            END IF;
            IF comment_status_fujuzhang1 != 0
            THEN
              SET comment_num = comment_num + 1,
              comment_score = comment_score + (50 - (comment_status_fujuzhang1 - 1) * 10);
            END IF;
            IF comment_status_juzhang1 != 0
            THEN
              SET comment_num = comment_num + 1,
              comment_score = comment_score + (50 - (comment_status_juzhang1 - 1) * 10);
            END IF;
            set score=0;
            if comment_num =0 THEN
            set score = base_score;
            else
            set score = base_score + (comment_score / comment_num);
            end if;
            UPDATE og_project_tasks SET xiaoneng_score = score  WHERE id = id1;
            select base_score;
            select comment_score;
            select comment_num;
            select 'base_score + (comment_score / comment_num)';
            select score;
    END IF;
    FETCH NEXT FROM RS INTO id1, due_date1, completed_on1,
    comment_status_xiaoneng1,comment_status_fujuzhang1, comment_status_juzhang1,
    tuijian1, pishi1, comment_xiaoneng_deadline1,comment_fujuzhang_deadline1, assigned_to_departid1,xiaoneng_score1;

  UNTIL Done END REPEAT;
/* 关闭游标 */
  CLOSE rs;
END //
delimiter ;
call computeTaskScore(131);