CREATE TABLE IF NOT EXISTS `og_holiday_apply` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `reason` varchar(2) CHARACTER SET utf8 NOT NULL,
  `detail` varchar(600) CHARACTER SET utf8 NOT NULL,
  `begin_date` date NOT NULL,
  `end_date` date NOT NULL,
  `apply_status` int(2) NOT NULL,
  `user_id` int(10) NOT NULL,
  `create_time` datetime NOT NULL,
  `apply_time` datetime NOT NULL,
  `isHandled` tinyint(1) NOT NULL DEFAULT '0',
  `current_handler` int(10) NOT NULL,
  `reject_userid` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1391828601 ;