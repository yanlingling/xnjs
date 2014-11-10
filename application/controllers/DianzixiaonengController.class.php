<?php

/**
 * Controller for handling task list and task related requests
 *
 * @version 1.0
 * @author Ilija Studen <ilija.studen@gmail.com>
 */
class DianzixiaonengController extends ApplicationController
{

    /**
     * Construct the MilestoneController
     *
     * @access public
     * @param void
     * @return MilestoneController
     */
    function __construct()
    {
        parent::__construct();
        prepare_company_website_controller($this, 'website');
    } // __construct

    /**
     * 获取任务列表
     */
    function index()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        DB::beginWork();
        $sql = "SELECT z.id,z.name,z.create_time FROM og_kaoqinducha_file AS z ";
        if (isset($_POST['condition']) && $_POST['condition'] != '') {
            $sql .= ' and (z.name like "%' . $_POST['condition'] . '%")';
        }
        $sql .= ' order by z.create_time desc';
        $rows = DB::executeAll($sql);
        tpl_assign('jufaFileInfo', $rows);

        $rows1 = DB::executeAll('select can_manage_file from og_users as x where x.id=' . logged_user()->getId());
        $canManageFile = $rows1[0]['can_manage_file'];
        tpl_assign('canManageFile', $canManageFile);
        if (isset($_POST['condition']) && $_POST['condition'] != '') {
            tpl_assign('condition', $_POST['condition']);
        }
        tpl_assign('currentTabId', isset($_POST['currentTabId']) ? $_POST['currentTabId'] : 'to-read-tab');
    }
}


?>