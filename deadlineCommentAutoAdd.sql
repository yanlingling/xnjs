DROP PROCEDURE IF EXISTS `deadlineCommentAutoAdd`;
delimiter //
CREATE PROCEDURE deadlineCommentAutoAdd()
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
    DECLARE tuijian_num INT;
    DECLARE pishi_num INT;
    DECLARE comment_num INT;
    DECLARE comment_score INT;
    DECLARE base_score INT;

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
                            comment_fujuzhang_deadline
                              assigned_to_departid
                          FROM og_project_tasks
                          WHERE deleted != 1 AND light_status = 1 AND comment_status_xiaoneng != -1;

/* 异常处理  */
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET Done = 1;


/* 打开游标 */
    OPEN rs;

/* 逐个取出当前记录 LingQi字段的值，需要进行最大值的判断  */
    FETCH NEXT FROM RS INTO id1, due_date1, completed_on1,
    comment_status_xiaoneng1,comment_status_fujuzhang1, comment_status_juzhang1,
    tuijian1, pishi1, comment_xiaoneng_deadline1,comment_fujuzhang_deadline1, assigned_to_departid1;
/* 遍历数据表  */
    REPEAT
      IF NOT Done
      THEN
        IF CURRENT_DATE ( ) > DATE(comment_xiaoneng_deadline1)
        THEN
/*过了评价期，默认中评*/
          IF comment_status_xiaoneng1 = 0
          THEN
            UPDATE og_project_tasks
            SET comment_status_xiaoneng = 2
            WHERE id = id1;
            /** 重新计算分数 */
            CALL  computeTaskScore(id1);
            CALL  computeDepartScore(assigned_to_departid1);
          END IF;

        END IF;


        IF CURRENT_DATE ( ) > DATE(comment_fujuzhang_deadline1)
        THEN
/*过了评价期，默认中评*/
          IF comment_status_fujuzhang1 = 0
          THEN
            UPDATE og_project_tasks
            SET comment_status_fujuzhang = 2
            WHERE id = id1;
            /** 重新计算分数 */
            CALL  computeTaskScore(id1);
            CALL  computeDepartScore(assigned_to_departid1);
          END IF;
        END IF;
      END IF;
      FETCH NEXT FROM RS INTO id1, due_date1, completed_on1,
    comment_status_xiaoneng1,comment_status_fujuzhang1, comment_status_juzhang1,
    tuijian1, pishi1, comment_xiaoneng_deadline1,comment_fujuzhang_deadline1, assigned_to_departid1;

    UNTIL Done END REPEAT;

/* 关闭游标 */
    CLOSE rs;
  END //
    delimiter ;
call deadlineCommentAutoAdd();