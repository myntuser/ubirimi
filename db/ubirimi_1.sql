-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2+deb7u1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 03, 2015 at 07:36 AM
-- Server version: 5.5.41
-- PHP Version: 5.5.21-1~dotdeb.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `ubirimi`
--

-- --------------------------------------------------------

--
-- Table structure for table `agile_board`
--

CREATE TABLE IF NOT EXISTS `agile_board` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `filter_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `swimlane_strategy` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `user_created_id` bigint(20) unsigned NOT NULL,
  `date_created` date NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_created_id` (`user_created_id`),
  KEY `client_id` (`client_id`),
  KEY `filter_id` (`filter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `agile_board_column`
--

CREATE TABLE IF NOT EXISTS `agile_board_column` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `agile_board_id` bigint(20) unsigned NOT NULL,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `position` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `agile_board_id` (`agile_board_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `agile_board_column_status`
--

CREATE TABLE IF NOT EXISTS `agile_board_column_status` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `agile_board_column_id` bigint(20) unsigned NOT NULL,
  `issue_status_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `agile_board_column_id` (`agile_board_column_id`),
  KEY `issue_status_id` (`issue_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `agile_board_project`
--

CREATE TABLE IF NOT EXISTS `agile_board_project` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `agile_board_id` bigint(20) unsigned NOT NULL,
  `project_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `agile_board_id` (`agile_board_id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `agile_board_sprint`
--

CREATE TABLE IF NOT EXISTS `agile_board_sprint` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `agile_board_id` bigint(20) unsigned NOT NULL,
  `user_created_id` bigint(20) unsigned NOT NULL,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date_start` date DEFAULT NULL,
  `date_end` date DEFAULT NULL,
  `started_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `finished_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `date_created` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `agile_board_id` (`agile_board_id`),
  KEY `user_created_id` (`user_created_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `agile_board_sprint_issue`
--

CREATE TABLE IF NOT EXISTS `agile_board_sprint_issue` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `agile_board_sprint_id` bigint(20) unsigned NOT NULL,
  `issue_id` bigint(20) unsigned NOT NULL,
  `done_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `agile_board_sprint_id` (`agile_board_sprint_id`),
  KEY `issue_id` (`issue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cal_calendar`
--

CREATE TABLE IF NOT EXISTS `cal_calendar` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `name` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `color` varchar(7) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `default_flag` tinyint(3) unsigned DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1652 ;

-- --------------------------------------------------------

--
-- Table structure for table `cal_calendar_default_reminder`
--

CREATE TABLE IF NOT EXISTS `cal_calendar_default_reminder` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cal_calendar_id` bigint(20) unsigned NOT NULL,
  `cal_event_reminder_type_id` bigint(20) unsigned NOT NULL,
  `cal_event_reminder_period_id` bigint(20) unsigned NOT NULL,
  `value` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1249 ;

-- --------------------------------------------------------

--
-- Table structure for table `cal_calendar_share`
--

CREATE TABLE IF NOT EXISTS `cal_calendar_share` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cal_calendar_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

-- --------------------------------------------------------

--
-- Table structure for table `cal_event`
--

CREATE TABLE IF NOT EXISTS `cal_event` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cal_calendar_id` bigint(20) unsigned NOT NULL,
  `user_created_id` bigint(20) NOT NULL,
  `cal_event_repeat_id` bigint(20) unsigned DEFAULT NULL,
  `cal_event_link_id` bigint(20) unsigned DEFAULT NULL,
  `date_from` datetime NOT NULL,
  `name` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `location` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_to` datetime NOT NULL,
  `color` varchar(7) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cal_calendar_id` (`cal_calendar_id`),
  KEY `user_created_id` (`user_created_id`),
  KEY `cal_event_repeat_id` (`cal_event_repeat_id`),
  KEY `cal_event_link_id` (`cal_event_link_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1389 ;

-- --------------------------------------------------------

--
-- Table structure for table `cal_event_reminder`
--

CREATE TABLE IF NOT EXISTS `cal_event_reminder` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cal_event_id` bigint(20) unsigned NOT NULL,
  `cal_event_reminder_type_id` bigint(20) unsigned NOT NULL,
  `cal_event_reminder_period_id` bigint(20) unsigned NOT NULL,
  `value` int(10) unsigned NOT NULL,
  `fired_flag` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=222 ;

-- --------------------------------------------------------

--
-- Table structure for table `cal_event_reminder_period`
--

CREATE TABLE IF NOT EXISTS `cal_event_reminder_period` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cal_event_reminder_type`
--

CREATE TABLE IF NOT EXISTS `cal_event_reminder_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cal_event_repeat`
--

CREATE TABLE IF NOT EXISTS `cal_event_repeat` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cal_event_repeat_cycle_id` bigint(20) unsigned NOT NULL,
  `repeat_every` int(10) unsigned NOT NULL,
  `end_after_occurrences` int(10) unsigned DEFAULT NULL,
  `on_day_0` tinyint(3) unsigned DEFAULT '0',
  `on_day_1` tinyint(3) unsigned DEFAULT '0',
  `on_day_2` tinyint(3) unsigned DEFAULT '0',
  `on_day_3` tinyint(3) unsigned DEFAULT '0',
  `on_day_4` tinyint(3) unsigned DEFAULT '0',
  `on_day_5` tinyint(3) unsigned DEFAULT '0',
  `on_day_6` tinyint(3) unsigned DEFAULT '0',
  `start_date` datetime NOT NULL,
  `end_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Table structure for table `cal_event_repeat_cycle`
--

CREATE TABLE IF NOT EXISTS `cal_event_repeat_cycle` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `cal_event_share`
--

CREATE TABLE IF NOT EXISTS `cal_event_share` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cal_event_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

CREATE TABLE IF NOT EXISTS `client` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sys_country_id` bigint(20) unsigned DEFAULT NULL,
  `company_name` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `company_domain` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `base_url` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `address_1` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_2` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `district` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `contact_email` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `installed_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `instance_type` tinyint(3) unsigned NOT NULL COMMENT '1 - on demand; 2 - download',
  `timezone` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `language` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date_created` datetime NOT NULL,
  `date_last_login` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `company_domain` (`company_domain`),
  KEY `country_id` (`sys_country_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1937 ;

-- --------------------------------------------------------

--
-- Table structure for table `client_documentator_settings`
--

CREATE TABLE IF NOT EXISTS `client_documentator_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `anonymous_use_flag` tinyint(4) unsigned NOT NULL,
  `anonymous_view_user_profile_flag` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1922 ;

-- --------------------------------------------------------

--
-- Table structure for table `client_product`
--

CREATE TABLE IF NOT EXISTS `client_product` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `sys_product_id` bigint(20) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `sys_product_id` (`sys_product_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2270 ;

-- --------------------------------------------------------

--
-- Table structure for table `client_settings`
--

CREATE TABLE IF NOT EXISTS `client_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `title_name` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `operating_mode` varchar(7) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `timezone` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `language` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1921 ;

-- --------------------------------------------------------

--
-- Table structure for table `client_smtp_settings`
--

CREATE TABLE IF NOT EXISTS `client_smtp_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `default_ubirimi_server_flag` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `from_address` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email_prefix` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `smtp_protocol` tinyint(3) unsigned NOT NULL,
  `hostname` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `port` int(11) NOT NULL,
  `timeout` int(10) unsigned NOT NULL,
  `tls_flag` tinyint(4) NOT NULL DEFAULT '0',
  `username` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=457 ;

-- --------------------------------------------------------

--
-- Table structure for table `client_yongo_settings`
--

CREATE TABLE IF NOT EXISTS `client_yongo_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `allow_unassigned_issues_flag` tinyint(3) unsigned DEFAULT NULL,
  `issues_per_page` int(10) unsigned NOT NULL,
  `allow_attachments_flag` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `notify_own_changes_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `issue_linking_flag` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `time_tracking_flag` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `time_tracking_hours_per_day` float unsigned NOT NULL,
  `time_tracking_days_per_week` int(10) unsigned NOT NULL,
  `time_tracking_default_unit` varchar(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1937 ;

-- --------------------------------------------------------

--
-- Table structure for table `documentator_entity`
--

CREATE TABLE IF NOT EXISTS `documentator_entity` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `documentator_entity_type_id` bigint(20) unsigned NOT NULL,
  `documentator_space_id` bigint(20) unsigned NOT NULL,
  `parent_entity_id` bigint(20) unsigned DEFAULT NULL,
  `user_created_id` bigint(20) unsigned NOT NULL,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `in_trash_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documentator_space_id` (`documentator_space_id`),
  KEY `user_created_id` (`user_created_id`),
  KEY `page_parent_id` (`parent_entity_id`),
  KEY `documentator_entity_id` (`documentator_entity_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- Table structure for table `documentator_entity_attachment`
--

CREATE TABLE IF NOT EXISTS `documentator_entity_attachment` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `documentator_entity_id` bigint(20) unsigned NOT NULL,
  `name` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `documentator_entity_id` (`documentator_entity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `documentator_entity_attachment_revision`
--

CREATE TABLE IF NOT EXISTS `documentator_entity_attachment_revision` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `documentator_entity_attachment_id` bigint(20) unsigned NOT NULL,
  `user_created_id` bigint(20) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `documentator_entity_attachment_id` (`documentator_entity_attachment_id`),
  KEY `user_created_id` (`user_created_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `documentator_entity_comment`
--

CREATE TABLE IF NOT EXISTS `documentator_entity_comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `documentator_entity_id` bigint(10) unsigned NOT NULL,
  `parent_comment_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `content` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `documentator_entity_id` (`documentator_entity_id`),
  KEY `user_id` (`user_id`),
  KEY `parent_comment_id` (`parent_comment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `documentator_entity_file`
--

CREATE TABLE IF NOT EXISTS `documentator_entity_file` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `documentator_entity_id` bigint(20) unsigned NOT NULL,
  `name` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `documentator_entity_id` (`documentator_entity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `documentator_entity_file_revision`
--

CREATE TABLE IF NOT EXISTS `documentator_entity_file_revision` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `documentator_entity_file_id` bigint(20) unsigned NOT NULL,
  `user_created_id` bigint(20) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `documentator_entity_file_id` (`documentator_entity_file_id`),
  KEY `user_created_id` (`user_created_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `documentator_entity_revision`
--

CREATE TABLE IF NOT EXISTS `documentator_entity_revision` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `entity_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `content` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `page_id` (`entity_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=248 ;

-- --------------------------------------------------------

--
-- Table structure for table `documentator_entity_snapshot`
--

CREATE TABLE IF NOT EXISTS `documentator_entity_snapshot` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `documentator_entity_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `content` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `documentator_entity_id` (`documentator_entity_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=138 ;

-- --------------------------------------------------------

--
-- Table structure for table `documentator_entity_type`
--

CREATE TABLE IF NOT EXISTS `documentator_entity_type` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `documentator_space`
--

CREATE TABLE IF NOT EXISTS `documentator_space` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `user_created_id` bigint(20) unsigned NOT NULL,
  `home_entity_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(6) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `home_page_id` (`home_entity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `documentator_space_permission`
--

CREATE TABLE IF NOT EXISTS `documentator_space_permission` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `space_id` bigint(20) unsigned NOT NULL,
  `group_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `all_view_flag` tinyint(3) unsigned NOT NULL,
  `space_admin_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `space_id` (`space_id`),
  KEY `group_id` (`group_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

-- --------------------------------------------------------

--
-- Table structure for table `documentator_space_permission_anonymous`
--

CREATE TABLE IF NOT EXISTS `documentator_space_permission_anonymous` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `documentator_space_id` bigint(20) unsigned NOT NULL,
  `all_view_flag` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `documentator_space_id` (`documentator_space_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `documentator_user_entity_favourite`
--

CREATE TABLE IF NOT EXISTS `documentator_user_entity_favourite` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `entity_id` bigint(20) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `page_id` (`entity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `documentator_user_space_favourite`
--

CREATE TABLE IF NOT EXISTS `documentator_user_space_favourite` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `space_id` bigint(20) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `space_id` (`space_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `general_group`
--

CREATE TABLE IF NOT EXISTS `general_group` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `sys_product_id` bigint(20) unsigned NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `sys_product_id` (`sys_product_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9515 ;

-- --------------------------------------------------------

--
-- Table structure for table `general_group_data`
--

CREATE TABLE IF NOT EXISTS `general_group_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=35518 ;

-- --------------------------------------------------------

--
-- Table structure for table `general_log`
--

CREATE TABLE IF NOT EXISTS `general_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `message` text NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=62924 ;

-- --------------------------------------------------------

--
-- Table structure for table `general_mail_queue`
--

CREATE TABLE IF NOT EXISTS `general_mail_queue` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `from_address` varchar(50) NOT NULL,
  `to_address` varchar(50) NOT NULL,
  `reply_to_address` varchar(50) DEFAULT NULL,
  `subject` varchar(250) NOT NULL,
  `content` mediumtext NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `general_task_queue`
--

CREATE TABLE IF NOT EXISTS `general_task_queue` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL,
  `data` mediumtext CHARACTER SET utf8 NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `general_user`
--

CREATE TABLE IF NOT EXISTS `general_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned DEFAULT NULL,
  `country_id` bigint(20) unsigned DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `first_name` varchar(200) DEFAULT NULL,
  `last_name` varchar(200) DEFAULT NULL,
  `avatar_picture` varchar(250) DEFAULT NULL,
  `issues_per_page` int(10) unsigned DEFAULT NULL,
  `issues_display_columns` text,
  `client_administrator_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `svn_administrator_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `super_user_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `notify_own_changes_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `customer_service_desk_flag` tinyint(4) NOT NULL DEFAULT '0',
  `date_created` datetime DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL,
  `date_last_login` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3149 ;

-- --------------------------------------------------------

--
-- Table structure for table `help_customer`
--

CREATE TABLE IF NOT EXISTS `help_customer` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `help_organization_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `help_filter`
--

CREATE TABLE IF NOT EXISTS `help_filter` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) NOT NULL,
  `created_user_id` bigint(20) unsigned NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `definition` text NOT NULL,
  `columns` text NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `help_organization`
--

CREATE TABLE IF NOT EXISTS `help_organization` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `help_organization_user`
--

CREATE TABLE IF NOT EXISTS `help_organization_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `help_organization_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `help_reset_password`
--

CREATE TABLE IF NOT EXISTS `help_reset_password` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `token` varchar(50) NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `help_sla`
--

CREATE TABLE IF NOT EXISTS `help_sla` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `start_condition` text NOT NULL,
  `stop_condition` text NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31 ;

-- --------------------------------------------------------

--
-- Table structure for table `help_sla_calendar`
--

CREATE TABLE IF NOT EXISTS `help_sla_calendar` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned NOT NULL,
  `sys_timezone_id` bigint(20) unsigned DEFAULT NULL,
  `default_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `name` varchar(150) NOT NULL,
  `description` varchar(250) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sys_timezone_id` (`sys_timezone_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Table structure for table `help_sla_calendar_data`
--

CREATE TABLE IF NOT EXISTS `help_sla_calendar_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `help_sla_calendar_id` bigint(20) unsigned NOT NULL,
  `day_number` int(10) unsigned NOT NULL,
  `time_from` varchar(8) DEFAULT NULL,
  `time_to` varchar(8) DEFAULT NULL,
  `not_working_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `help_calendar_id` (`help_sla_calendar_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=64 ;

-- --------------------------------------------------------

--
-- Table structure for table `help_sla_goal`
--

CREATE TABLE IF NOT EXISTS `help_sla_goal` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `help_sla_id` bigint(20) unsigned NOT NULL,
  `help_sla_calendar_id` bigint(20) unsigned NOT NULL,
  `definition` text NOT NULL,
  `definition_sql` text NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `help_sla_calendar_id` (`help_sla_calendar_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=41 ;

-- --------------------------------------------------------

--
-- Table structure for table `newsletter`
--

CREATE TABLE IF NOT EXISTS `newsletter` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email_address` varchar(200) NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `qn_notebook`
--

CREATE TABLE IF NOT EXISTS `qn_notebook` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `name` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `default_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=64 ;

-- --------------------------------------------------------

--
-- Table structure for table `qn_notebook_note`
--

CREATE TABLE IF NOT EXISTS `qn_notebook_note` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `qn_notebook_id` bigint(20) unsigned NOT NULL,
  `summary` varchar(250) NOT NULL,
  `content` text,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `qn_notebook_id` (`qn_notebook_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=36 ;

-- --------------------------------------------------------

--
-- Table structure for table `qn_notebook_note_tag`
--

CREATE TABLE IF NOT EXISTS `qn_notebook_note_tag` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `qn_notebook_note_id` bigint(20) unsigned NOT NULL,
  `qn_tag_id` bigint(20) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `qn_notebook_note_id` (`qn_notebook_note_id`,`qn_tag_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `qn_tag`
--

CREATE TABLE IF NOT EXISTS `qn_tag` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `name` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `server_settings`
--

CREATE TABLE IF NOT EXISTS `server_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `maintenance_server_message` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `svn_repository`
--

CREATE TABLE IF NOT EXISTS `svn_repository` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `user_created_id` bigint(20) unsigned NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `code` varchar(50) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `user_created_id` (`user_created_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `svn_repository_user`
--

CREATE TABLE IF NOT EXISTS `svn_repository_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `svn_repository_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `date_created` bigint(20) unsigned NOT NULL,
  `password` varchar(250) DEFAULT NULL,
  `has_read` tinyint(1) DEFAULT NULL,
  `has_write` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `svn_repository_id` (`svn_repository_id`),
  KEY `user_id` (`user_id`),
  KEY `date_created` (`date_created`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=270 ;

-- --------------------------------------------------------

--
-- Table structure for table `sys_country`
--

CREATE TABLE IF NOT EXISTS `sys_country` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=197 ;

-- --------------------------------------------------------

--
-- Table structure for table `sys_product`
--

CREATE TABLE IF NOT EXISTS `sys_product` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_condition`
--

CREATE TABLE IF NOT EXISTS `yongo_condition` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_event`
--

CREATE TABLE IF NOT EXISTS `yongo_event` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `code` tinyint(3) unsigned NOT NULL,
  `description` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `system_flag` tinyint(3) unsigned DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23239 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_field`
--

CREATE TABLE IF NOT EXISTS `yongo_field` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `sys_field_type_id` bigint(20) unsigned DEFAULT NULL,
  `code` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `system_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `all_issue_type_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `all_project_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `sys_field_type_id` (`sys_field_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=29058 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_field_configuration`
--

CREATE TABLE IF NOT EXISTS `yongo_field_configuration` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1938 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_field_configuration_data`
--

CREATE TABLE IF NOT EXISTS `yongo_field_configuration_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `field_configuration_id` bigint(20) unsigned NOT NULL,
  `field_id` bigint(20) unsigned NOT NULL,
  `visible_flag` tinyint(3) unsigned DEFAULT NULL,
  `required_flag` tinyint(3) unsigned NOT NULL,
  `field_description` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `field_configuration_id` (`field_configuration_id`),
  KEY `field_id` (`field_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=29000 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_field_data`
--

CREATE TABLE IF NOT EXISTS `yongo_field_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `field_id` bigint(20) unsigned NOT NULL,
  `value` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `field_id` (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_field_issue_type_data`
--

CREATE TABLE IF NOT EXISTS `yongo_field_issue_type_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `field_id` bigint(20) unsigned NOT NULL,
  `issue_type_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `field_id` (`field_id`),
  KEY `issue_type_id` (`issue_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=100 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_field_project_data`
--

CREATE TABLE IF NOT EXISTS `yongo_field_project_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `field_id` bigint(20) unsigned NOT NULL,
  `project_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `field_id` (`field_id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=141 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_field_type`
--

CREATE TABLE IF NOT EXISTS `yongo_field_type` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(250) NOT NULL,
  `code` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_filter`
--

CREATE TABLE IF NOT EXISTS `yongo_filter` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `name` varchar(250) NOT NULL,
  `definition` mediumtext NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=159 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_filter_favourite`
--

CREATE TABLE IF NOT EXISTS `yongo_filter_favourite` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `filter_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_filter_subscription`
--

CREATE TABLE IF NOT EXISTS `yongo_filter_subscription` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `filter_id` bigint(20) unsigned NOT NULL,
  `user_created_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `group_id` bigint(20) unsigned DEFAULT NULL,
  `period` varchar(200) NOT NULL,
  `email_when_empty_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue`
--

CREATE TABLE IF NOT EXISTS `yongo_issue` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `priority_id` bigint(20) unsigned NOT NULL,
  `status_id` bigint(20) unsigned NOT NULL,
  `type_id` bigint(20) unsigned NOT NULL,
  `resolution_id` bigint(20) unsigned DEFAULT NULL,
  `user_reported_id` bigint(20) unsigned NOT NULL,
  `user_assigned_id` bigint(20) unsigned DEFAULT NULL,
  `project_id` bigint(20) unsigned NOT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `security_scheme_level_id` bigint(20) unsigned DEFAULT NULL,
  `nr` bigint(20) unsigned NOT NULL,
  `summary` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `environment` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `user_reported_ip` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `original_estimate` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `remaining_estimate` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `helpdesk_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  `date_resolved` datetime DEFAULT NULL,
  `date_due` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_assigned_id` (`user_assigned_id`),
  KEY `project_id` (`project_id`),
  KEY `status_id` (`status_id`),
  KEY `user_reported_id` (`user_reported_id`),
  KEY `type_id` (`type_id`),
  KEY `resolution_id` (`resolution_id`),
  KEY `priority_id` (`priority_id`),
  KEY `parent_id` (`parent_id`),
  KEY `issue_security_scheme_level_id` (`security_scheme_level_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4512 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_attachment`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_attachment` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `issue_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `name` varchar(200) NOT NULL,
  `size` bigint(20) unsigned DEFAULT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `issue_id` (`issue_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1308 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_comment`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_comment` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `issue_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `content` mediumtext NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `issue_id` (`issue_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12140 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_component`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_component` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `issue_id` bigint(20) unsigned NOT NULL,
  `project_component_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_component_id` (`project_component_id`),
  KEY `issue_id` (`issue_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1205 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_custom_field_data`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_custom_field_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `issue_id` bigint(20) unsigned NOT NULL,
  `field_id` bigint(20) unsigned NOT NULL,
  `value` mediumtext NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `issue_id` (`issue_id`),
  KEY `field_id` (`field_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4793 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_history`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `issue_id` bigint(20) unsigned NOT NULL,
  `by_user_id` bigint(20) unsigned NOT NULL,
  `field` varchar(20) NOT NULL,
  `old_value` mediumtext,
  `new_value` mediumtext,
  `old_value_id` varchar(250) DEFAULT NULL,
  `new_value_id` varchar(250) DEFAULT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `issue_id` (`issue_id`),
  KEY `by_user_id` (`by_user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19695 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_link`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_link` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_issue_id` bigint(20) unsigned NOT NULL,
  `issue_link_type_id` bigint(20) unsigned NOT NULL,
  `link_type` varchar(7) NOT NULL,
  `child_issue_id` bigint(20) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_issue_id` (`parent_issue_id`),
  KEY `sys_issue_link_type_id` (`issue_link_type_id`),
  KEY `child_issue_id` (`child_issue_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=179 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_link_type`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_link_type` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `outward_description` varchar(200) NOT NULL,
  `inward_description` varchar(200) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7677 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_priority`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_priority` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `icon_name` varchar(50) NOT NULL,
  `color` varchar(7) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9666 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_resolution`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_resolution` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9688 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_security_scheme`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_security_scheme` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_security_scheme_level`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_security_scheme_level` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `issue_security_scheme_id` bigint(20) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(250) NOT NULL,
  `default_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `issue_security_scheme_id` (`issue_security_scheme_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_security_scheme_level_data`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_security_scheme_level_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `issue_security_scheme_level_id` bigint(20) unsigned NOT NULL,
  `permission_role_id` bigint(20) unsigned DEFAULT NULL,
  `group_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `current_assignee` tinyint(3) unsigned DEFAULT NULL,
  `reporter` tinyint(3) unsigned DEFAULT NULL,
  `project_lead` tinyint(3) unsigned DEFAULT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `issue_security_scheme_level_id` (`issue_security_scheme_level_id`),
  KEY `permission_role_id` (`permission_role_id`),
  KEY `group_id` (`group_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_sla`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_sla` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `yongo_issue_id` bigint(20) unsigned NOT NULL,
  `help_sla_id` bigint(20) unsigned NOT NULL,
  `help_sla_goal_id` bigint(20) unsigned DEFAULT NULL,
  `started_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `stopped_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `started_date` datetime DEFAULT NULL,
  `stopped_date` datetime DEFAULT NULL,
  `value` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `yongo_issue_id` (`yongo_issue_id`),
  KEY `help_sla_id` (`help_sla_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10540 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_status`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_status` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9702 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_type`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_type` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `sub_task_flag` int(10) unsigned DEFAULT NULL,
  `icon_name` varchar(50) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15547 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_type_field_configuration`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_type_field_configuration` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(250) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1937 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_type_field_configuration_data`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_type_field_configuration_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `issue_type_field_configuration_id` bigint(20) unsigned NOT NULL,
  `issue_type_id` bigint(20) unsigned NOT NULL,
  `field_configuration_id` bigint(20) unsigned DEFAULT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `issue_type_id` (`issue_type_id`),
  KEY `field_configuration_id` (`field_configuration_id`),
  KEY `issue_type_field_configuration_id` (`issue_type_field_configuration_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15512 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_type_scheme`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_type_scheme` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(250) NOT NULL,
  `type` varchar(20) NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3906 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_type_scheme_data`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_type_scheme_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `issue_type_scheme_id` bigint(20) unsigned NOT NULL,
  `issue_type_id` bigint(20) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `issue_type_scheme_id` (`issue_type_scheme_id`),
  KEY `issue_type_id` (`issue_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31602 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_type_screen_scheme`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_type_screen_scheme` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(250) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1938 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_type_screen_scheme_data`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_type_screen_scheme_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `issue_type_screen_scheme_id` bigint(20) unsigned NOT NULL,
  `issue_type_id` bigint(20) unsigned NOT NULL,
  `screen_scheme_id` bigint(20) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`issue_type_screen_scheme_id`),
  KEY `issue_type_id` (`issue_type_id`),
  KEY `screen_scheme_id` (`screen_scheme_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15520 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_version`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_version` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `issue_id` bigint(20) unsigned NOT NULL,
  `project_version_id` bigint(20) unsigned NOT NULL,
  `affected_targeted_flag` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_version_id` (`project_version_id`),
  KEY `issue_id` (`issue_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3168 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_watch`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_watch` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `yongo_issue_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `yongo_issue_id` (`yongo_issue_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4566 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_work_log`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_work_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `issue_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `time_spent` varchar(20) NOT NULL,
  `comment` mediumtext NOT NULL,
  `edited_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `date_started` datetime NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `issue_id` (`issue_id`,`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3063 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_notification_scheme`
--

CREATE TABLE IF NOT EXISTS `yongo_notification_scheme` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(250) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1960 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_notification_scheme_data`
--

CREATE TABLE IF NOT EXISTS `yongo_notification_scheme_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `notification_scheme_id` bigint(20) unsigned NOT NULL,
  `event_id` bigint(20) unsigned NOT NULL,
  `permission_role_id` bigint(20) unsigned DEFAULT NULL,
  `group_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `current_assignee` tinyint(3) unsigned DEFAULT NULL,
  `reporter` tinyint(3) unsigned DEFAULT NULL,
  `current_user` tinyint(3) unsigned DEFAULT NULL,
  `project_lead` tinyint(3) unsigned DEFAULT NULL,
  `component_lead` tinyint(3) unsigned DEFAULT NULL,
  `all_watchers` tinyint(3) unsigned DEFAULT NULL,
  `user_picker_multiple_selection` bigint(20) unsigned DEFAULT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `notification_scheme_id` (`notification_scheme_id`),
  KEY `permission_role_id` (`permission_role_id`),
  KEY `group_id` (`group_id`),
  KEY `user_id` (`user_id`),
  KEY `current_assignee` (`current_assignee`),
  KEY `event_id` (`event_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=48016 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_operation`
--

CREATE TABLE IF NOT EXISTS `yongo_operation` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_permission`
--

CREATE TABLE IF NOT EXISTS `yongo_permission` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sys_permission_category_id` bigint(20) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sys_permission_category_id` (`sys_permission_category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=29 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_permission_category`
--

CREATE TABLE IF NOT EXISTS `yongo_permission_category` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_permission_global`
--

CREATE TABLE IF NOT EXISTS `yongo_permission_global` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sys_product_id` bigint(20) unsigned NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` mediumtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sys_product_id` (`sys_product_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_permission_global_data`
--

CREATE TABLE IF NOT EXISTS `yongo_permission_global_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `sys_permission_global_id` bigint(20) unsigned NOT NULL,
  `group_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `sys_permission_global_id` (`sys_permission_global_id`),
  KEY `group_id` (`group_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15252 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_permission_role`
--

CREATE TABLE IF NOT EXISTS `yongo_permission_role` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5820 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_permission_role_data`
--

CREATE TABLE IF NOT EXISTS `yongo_permission_role_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `permission_role_id` bigint(20) unsigned NOT NULL,
  `default_group_id` bigint(20) unsigned DEFAULT NULL,
  `default_user_id` bigint(20) unsigned DEFAULT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`default_user_id`),
  KEY `user_group_id` (`default_group_id`),
  KEY `permission_role_id` (`permission_role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5898 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_permission_scheme`
--

CREATE TABLE IF NOT EXISTS `yongo_permission_scheme` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` varchar(250) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2041 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_permission_scheme_data`
--

CREATE TABLE IF NOT EXISTS `yongo_permission_scheme_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `permission_scheme_id` bigint(20) unsigned NOT NULL,
  `sys_permission_id` bigint(20) unsigned NOT NULL,
  `permission_role_id` bigint(20) unsigned DEFAULT NULL,
  `group_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `current_assignee` tinyint(3) unsigned DEFAULT NULL,
  `reporter` tinyint(3) unsigned DEFAULT NULL,
  `project_lead` tinyint(3) unsigned DEFAULT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `permission_scheme_id` (`permission_scheme_id`),
  KEY `permission_id` (`sys_permission_id`),
  KEY `permission_role_id` (`permission_role_id`),
  KEY `group_id` (`group_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=88627 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_project`
--

CREATE TABLE IF NOT EXISTS `yongo_project` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lead_id` bigint(20) unsigned DEFAULT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `issue_type_scheme_id` bigint(20) unsigned NOT NULL,
  `issue_type_screen_scheme_id` bigint(20) unsigned NOT NULL,
  `issue_type_field_configuration_id` bigint(20) unsigned NOT NULL,
  `workflow_scheme_id` bigint(20) unsigned NOT NULL,
  `permission_scheme_id` bigint(20) unsigned NOT NULL,
  `notification_scheme_id` bigint(20) unsigned NOT NULL,
  `issue_security_scheme_id` bigint(20) unsigned DEFAULT NULL,
  `project_category_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `code` varchar(5) NOT NULL,
  `description` varchar(250) NOT NULL,
  `help_desk_enabled_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `issue_number` bigint(20) unsigned NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `owner_id` (`lead_id`),
  KEY `issue_type_scheme_id` (`issue_type_scheme_id`),
  KEY `workflow_scheme_id` (`workflow_scheme_id`),
  KEY `issue_type_screen_scheme_id` (`issue_type_screen_scheme_id`),
  KEY `issue_type_field_configuration_id` (`issue_type_field_configuration_id`),
  KEY `permission_scheme_id` (`permission_scheme_id`),
  KEY `notification_scheme_id` (`notification_scheme_id`),
  KEY `issue_security_scheme_id` (`issue_security_scheme_id`),
  KEY `project_category_id` (`project_category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=421 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_project_category`
--

CREATE TABLE IF NOT EXISTS `yongo_project_category` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(250) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=48 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_project_component`
--

CREATE TABLE IF NOT EXISTS `yongo_project_component` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `leader_id` bigint(20) unsigned DEFAULT NULL,
  `project_id` bigint(20) unsigned NOT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` mediumtext NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`project_id`),
  KEY `leader_id` (`leader_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=24 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_project_role_data`
--

CREATE TABLE IF NOT EXISTS `yongo_project_role_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned NOT NULL,
  `permission_role_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `group_id` bigint(20) unsigned DEFAULT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`permission_role_id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2866 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_project_version`
--

CREATE TABLE IF NOT EXISTS `yongo_project_version` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_screen`
--

CREATE TABLE IF NOT EXISTS `yongo_screen` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(200) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`client_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5819 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_screen_data`
--

CREATE TABLE IF NOT EXISTS `yongo_screen_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `screen_id` bigint(20) unsigned NOT NULL,
  `field_id` bigint(20) unsigned NOT NULL,
  `position` int(10) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_workflow_screen_id` (`screen_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=34900 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_screen_scheme`
--

CREATE TABLE IF NOT EXISTS `yongo_screen_scheme` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1939 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_screen_scheme_data`
--

CREATE TABLE IF NOT EXISTS `yongo_screen_scheme_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `screen_scheme_id` bigint(20) unsigned NOT NULL,
  `sys_operation_id` bigint(20) unsigned NOT NULL,
  `screen_id` bigint(20) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5818 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_workflow`
--

CREATE TABLE IF NOT EXISTS `yongo_workflow` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `issue_type_scheme_id` bigint(20) unsigned NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` mediumtext NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `issue_type_scheme_id` (`issue_type_scheme_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1972 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_workflow_condition_data`
--

CREATE TABLE IF NOT EXISTS `yongo_workflow_condition_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `workflow_data_id` bigint(20) unsigned NOT NULL,
  `definition_data` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `workflow_data_id` (`workflow_data_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23345 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_workflow_data`
--

CREATE TABLE IF NOT EXISTS `yongo_workflow_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `workflow_id` bigint(20) unsigned NOT NULL,
  `screen_id` bigint(20) unsigned DEFAULT NULL,
  `workflow_step_id_from` bigint(20) unsigned DEFAULT NULL,
  `workflow_step_id_to` bigint(20) unsigned DEFAULT NULL,
  `transition_name` varchar(50) DEFAULT NULL,
  `transition_description` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`workflow_id`),
  KEY `issue_status_from_id` (`workflow_step_id_from`,`workflow_step_id_to`),
  KEY `screen_id` (`screen_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25384 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_workflow_position`
--

CREATE TABLE IF NOT EXISTS `yongo_workflow_position` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `workflow_id` bigint(20) unsigned NOT NULL,
  `workflow_step_id` bigint(20) unsigned NOT NULL,
  `top_position` int(10) unsigned NOT NULL,
  `left_position` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_workflow_id` (`workflow_id`,`workflow_step_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11853 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_workflow_post_function`
--

CREATE TABLE IF NOT EXISTS `yongo_workflow_post_function` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  `description` varchar(250) NOT NULL,
  `user_addable_flag` tinyint(3) unsigned NOT NULL,
  `user_editable_flag` tinyint(3) unsigned NOT NULL,
  `user_deletable_flag` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_workflow_post_function_data`
--

CREATE TABLE IF NOT EXISTS `yongo_workflow_post_function_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `workflow_data_id` bigint(20) unsigned NOT NULL,
  `sys_workflow_post_function_id` bigint(20) unsigned NOT NULL,
  `definition_data` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_workflow_data_id` (`workflow_data_id`,`sys_workflow_post_function_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=83787 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_workflow_scheme`
--

CREATE TABLE IF NOT EXISTS `yongo_workflow_scheme` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(250) NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1949 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_workflow_scheme_data`
--

CREATE TABLE IF NOT EXISTS `yongo_workflow_scheme_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `workflow_scheme_id` bigint(20) unsigned NOT NULL,
  `workflow_id` bigint(20) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `workflow_scheme_id` (`workflow_scheme_id`),
  KEY `workflow_id` (`workflow_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1960 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_workflow_step`
--

CREATE TABLE IF NOT EXISTS `yongo_workflow_step` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `workflow_id` bigint(20) unsigned NOT NULL,
  `linked_issue_status_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `initial_step_flag` tinyint(3) unsigned DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `workflow_id` (`workflow_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11774 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_workflow_step_property`
--

CREATE TABLE IF NOT EXISTS `yongo_workflow_step_property` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_workflow_step_property_data`
--

CREATE TABLE IF NOT EXISTS `yongo_workflow_step_property_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `workflow_step_id` bigint(20) unsigned NOT NULL,
  `sys_workflow_step_property_id` bigint(20) unsigned NOT NULL,
  `value` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `workflow_step_id` (`workflow_step_id`),
  KEY `sys_workflow_step_property_id` (`sys_workflow_step_property_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=53 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
