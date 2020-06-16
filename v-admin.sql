/*
SQLyog Ultimate v12.08 (64 bit)
MySQL - 5.7.26-log : Database - vadmin
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`vadmin` /*!40100 DEFAULT CHARACTER SET utf8 */;

/*Table structure for table `s_group` */

CREATE TABLE `s_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(30) DEFAULT NULL COMMENT '角色标题',
  `rights` text COMMENT '权限',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

/*Data for the table `s_group` */

insert  into `s_group`(`id`,`title`,`rights`) values (22,'超级管理员','[325,326,332,333,336,350,363,327,351,352,357,359,360,328,361,362,364,365,366,367,368,369,370,371,372]');

/*Table structure for table `s_menu` */

CREATE TABLE `s_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) DEFAULT '0' COMMENT '父级id',
  `crd` int(11) DEFAULT '0' COMMENT '排序',
  `title` varchar(20) DEFAULT NULL COMMENT '名称',
  `contro` varchar(30) DEFAULT NULL COMMENT '控制器',
  `method` varchar(30) DEFAULT NULL COMMENT '方法',
  `path` varchar(15) DEFAULT NULL COMMENT '前端路由',
  `ishidden` tinyint(1) DEFAULT '0' COMMENT '0：显示 1：隐藏',
  `status` tinyint(1) DEFAULT '0' COMMENT '0：正常 1：禁用',
  `icon` varchar(50) DEFAULT NULL COMMENT 'icon-element 类名',
  `pidpath` varchar(50) DEFAULT NULL COMMENT 'pid路径',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=373 DEFAULT CHARSET=utf8;

/*Data for the table `s_menu` */

insert  into `s_menu`(`id`,`pid`,`crd`,`title`,`contro`,`method`,`path`,`ishidden`,`status`,`icon`,`pidpath`) values (325,0,0,'权限管理',NULL,NULL,NULL,0,0,'el-icon-cpu',''),(326,325,0,'菜单管理','','','menus',0,0,NULL,'325'),(327,325,0,'角色管理','','','roles',0,0,NULL,'325'),(328,325,0,'用户管理','','','users',0,0,NULL,'325'),(332,326,0,'菜单添加','Menu','add',NULL,0,0,NULL,'325,326'),(333,326,0,'菜单修改','Menu','edit','',0,0,'','325,326'),(336,326,0,'菜单删除','Menu','del','',0,0,'','325,326'),(350,326,0,'菜单详情','Menu','menuDetai','',0,0,'','325,326'),(351,327,0,'角色添加','Role','add',NULL,0,0,NULL,'325,327'),(352,327,0,'角色详情','Role','detail','',0,0,'','325,327'),(357,327,0,'角色删除','Role','del','',0,0,'','325,327'),(359,327,0,'角色列表','Role','list','',0,0,'','325,327'),(360,327,0,'角色修改','Role','edit',NULL,0,0,NULL,'325,327'),(361,328,0,'用户列表','User','list',NULL,0,0,NULL,'325,328'),(362,328,0,'角色列表','User','roleList',NULL,0,0,NULL,'325,328'),(363,326,0,'菜单列表','Menu','list',NULL,0,0,NULL,'325,326'),(364,328,0,'用户添加','User','add','',0,0,'','325,328'),(365,328,0,'用户修改','User','edit','',0,0,'','325,328'),(366,328,0,'用户删除','User','del',NULL,0,0,NULL,'325,328'),(367,328,0,'用户详情','User','detail','',0,0,'','325,328'),(368,328,0,'重置密码','User','resetPass','',0,0,'','325,328'),(369,328,0,'用户信息','User','userInfo','',0,0,'','325,328'),(370,0,0,'数据管理','','','',0,0,'el-icon-data-analysis',''),(371,370,0,'销售数据',NULL,NULL,'sales',0,0,NULL,'370'),(372,371,0,'销售日报','Saledaily','list',NULL,0,0,NULL,'370,371');

/*Table structure for table `s_user` */

CREATE TABLE `s_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `key` varchar(20) DEFAULT NULL,
  `phone` bigint(20) unsigned DEFAULT NULL COMMENT '手机号',
  `gid` int(11) unsigned DEFAULT NULL COMMENT '角色组id',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '用户状态',
  `add_time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=206 DEFAULT CHARSET=utf8;

/*Data for the table `s_user` */

insert  into `s_user`(`id`,`username`,`password`,`key`,`phone`,`gid`,`status`,`add_time`) values (204,'root','ad9ecf5b47b42d1c1f7f600daff5dc2032448174','ts_anbdshdsdf',15658833581,22,0,156465677);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
