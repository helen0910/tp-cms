/*
Navicat MySQL Data Transfer

Source Server         : xiyou
Source Server Version : 50612
Source Host           : localhost:3306
Source Database       : xmkd

Target Server Type    : MYSQL
Target Server Version : 50612
File Encoding         : 65001

Date: 2016-07-06 10:19:38
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `xmkd_admin`
-- ----------------------------
DROP TABLE IF EXISTS `xmkd_admin`;
CREATE TABLE `xmkd_admin` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `username` char(12) NOT NULL,
  `password` char(40) NOT NULL,
  `login_time` int(10) unsigned NOT NULL DEFAULT '0',
  `login_num` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `reg_time` int(10) unsigned NOT NULL,
  `admin_status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `email` varchar(60) DEFAULT NULL COMMENT '邮箱',
  `phone` varchar(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='管理员表';

-- ----------------------------
-- Records of xmkd_admin
-- ----------------------------
INSERT INTO `xmkd_admin` VALUES ('1', 'admin', '10470c3b4b1fed12c3baac014be15fac67c6e815', '1467770889', '92', '1464932949', '1', '123@qq.com', '13245678909');
-- ----------------------------
-- Table structure for `xmkd_admin_group`
-- ----------------------------
DROP TABLE IF EXISTS `xmkd_admin_group`;
CREATE TABLE `xmkd_admin_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(30) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `rules` text NOT NULL,
  `navi_rules` text NOT NULL,
  `is_super` tinyint(1) unsigned NOT NULL DEFAULT '2' COMMENT '超级管理员标识',
  `remark` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xmkd_admin_group
-- ----------------------------
INSERT INTO `xmkd_admin_group` VALUES ('1', '超级管理员', '1', '', '', '1', '');

-- ----------------------------
-- Table structure for `xmkd_admin_group_access`
-- ----------------------------
DROP TABLE IF EXISTS `xmkd_admin_group_access`;
CREATE TABLE `xmkd_admin_group_access` (
  `uid` mediumint(8) unsigned NOT NULL,
  `group_id` mediumint(8) unsigned NOT NULL,
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xmkd_admin_group_access
-- ----------------------------
INSERT INTO `xmkd_admin_group_access` VALUES ('1', '1');

-- ----------------------------
-- Table structure for `xmkd_admin_rule`
-- ----------------------------
DROP TABLE IF EXISTS `xmkd_admin_rule`;
CREATE TABLE `xmkd_admin_rule` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(125) NOT NULL,
  `title` char(20) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `show_status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '显示状态',
  `node_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `condition` char(100) NOT NULL DEFAULT '',
  `pid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `sort` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `remark` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=283 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xmkd_admin_rule
-- ----------------------------
INSERT INTO `xmkd_admin_rule` VALUES ('1', 'App/Back/Index/index', '后台框架', '1', '2', '0', '', '0', '1', '');
INSERT INTO `xmkd_admin_rule` VALUES ('2', 'App/Back/Index/show', '后台界面', '1', '2', '0', '', '1', '2', '');
INSERT INTO `xmkd_admin_rule` VALUES ('3', 'App/Back/_public_test_public_module/_public_test_public_action', '系统', '1', '1', '0', '', '0', '180', '');
INSERT INTO `xmkd_admin_rule` VALUES ('4', 'App/Back/_public_test_public_module/_public_test_public_action', '权限设置', '1', '1', '0', '', '3', '4', '');
INSERT INTO `xmkd_admin_rule` VALUES ('5', 'App/Back/Rule/index', '权限规则', '2', '2', '1', '', '4', '5', '');
INSERT INTO `xmkd_admin_rule` VALUES ('6', 'App/Back/Rule/index', '规则列表', '2', '2', '2', '', '5', '8', '');
INSERT INTO `xmkd_admin_rule` VALUES ('7', 'App/Back/Rule/add', '添加规则', '2', '2', '2', '', '5', '7', '');
INSERT INTO `xmkd_admin_rule` VALUES ('8', 'App/Back/Rule/edit', '编辑规则', '2', '2', '3', '', '5', '8', '');
INSERT INTO `xmkd_admin_rule` VALUES ('9', 'App/Back/Rule/delete', '删除规则', '2', '2', '3', '', '5', '9', '');
INSERT INTO `xmkd_admin_rule` VALUES ('10', 'App/Back/Rule/sort', '规则排序', '2', '2', '4', '', '5', '10', '');
INSERT INTO `xmkd_admin_rule` VALUES ('11', 'App/Back/Admingroup/index', '用户类别管理', '1', '1', '1', '', '4', '11', '');
INSERT INTO `xmkd_admin_rule` VALUES ('12', 'App/Back/Admingroup/index', '用户类别列表', '1', '1', '2', '', '11', '15', '');
INSERT INTO `xmkd_admin_rule` VALUES ('13', 'App/Back/Admingroup/add', '添加用户类别', '1', '1', '2', '', '11', '13', '');
INSERT INTO `xmkd_admin_rule` VALUES ('14', 'App/Back/Admingroup/edit', '编辑用户类别', '1', '1', '3', '', '11', '14', '');
INSERT INTO `xmkd_admin_rule` VALUES ('15', 'App/Back/Admingroup/delete', '删除组', '2', '2', '3', '', '11', '15', '');
INSERT INTO `xmkd_admin_rule` VALUES ('16', 'App/Back/Admingroup/set_navigate', '导航权限', '2', '2', '3', '', '11', '16', '');
INSERT INTO `xmkd_admin_rule` VALUES ('17', 'App/Back/Admin/index', '用户管理', '1', '1', '1', '', '4', '17', '');
INSERT INTO `xmkd_admin_rule` VALUES ('18', 'App/Back/Admin/index', '用户列表', '1', '1', '2', '', '17', '20', '');
INSERT INTO `xmkd_admin_rule` VALUES ('19', 'App/Back/Admin/add', '添加用户', '1', '1', '2', '', '17', '19', '');
INSERT INTO `xmkd_admin_rule` VALUES ('20', 'App/Back/Admin/edit', '编辑用户', '1', '1', '3', '', '17', '20', '');
INSERT INTO `xmkd_admin_rule` VALUES ('21', 'App/Back/Admin/delete', '删除管理员', '2', '2', '3', '', '17', '21', '');
INSERT INTO `xmkd_admin_rule` VALUES ('22', 'App/Back/Admingroup/set_rule', '系统权限', '1', '1', '3', '', '11', '22', '');
INSERT INTO `xmkd_admin_rule` VALUES ('23', 'App/Back/Rule/sort', '规则排序', '2', '2', '4', '', '5', '23', '');
INSERT INTO `xmkd_admin_rule` VALUES ('24', 'App/Back/_public_test_public_module/_public_test_public_action', '系统日志', '1', '1', '0', '', '3', '2', '');
INSERT INTO `xmkd_admin_rule` VALUES ('25', 'App/Back/Log/index', '运行日志', '1', '1', '1', '', '24', '25', '');
INSERT INTO `xmkd_admin_rule` VALUES ('26', 'App/Back/Log/index', '日志列表', '1', '1', '2', '', '25', '26', '');
INSERT INTO `xmkd_admin_rule` VALUES ('30', 'App/Back/Log/login_success_log', '登录成功日志', '1', '1', '2', '', '25', '24', '');
INSERT INTO `xmkd_admin_rule` VALUES ('31', 'App/Back/Log/login_error_log', '登录失败日志', '1', '1', '2', '', '25', '23', '');
INSERT INTO `xmkd_admin_rule` VALUES ('27', 'App/Back/Log/delete', '删除', '1', '1', '3', '', '25', '0', '');
INSERT INTO `xmkd_admin_rule` VALUES ('272', 'App/Back/Proof/shop_info', '免税店信息', '1', '1', '3', '', '271', '1', '');
INSERT INTO `xmkd_admin_rule` VALUES ('273', 'App/Back/Mession/view_mession', '已完成任务 查看', '1', '1', '3', '', '265', '1', '');
INSERT INTO `xmkd_admin_rule` VALUES ('274', 'App/Back/Mession/pay', '付款', '1', '1', '3', '', '265', '1', '');
INSERT INTO `xmkd_admin_rule` VALUES ('275', 'App/Back/Mession/view_back_mession', '已打回任务 查看', '1', '1', '3', '', '265', '1', '');
INSERT INTO `xmkd_admin_rule` VALUES ('276', 'App/Back/Mession/back', '任务打回', '1', '1', '3', '', '265', '1', '');
INSERT INTO `xmkd_admin_rule` VALUES ('277', 'App/Back/Mession/save', '任务保存', '1', '1', '3', '', '265', '1', '');
INSERT INTO `xmkd_admin_rule` VALUES ('278', 'App/Back/Mession/update_mession', '任务确认确定', '1', '1', '3', '', '265', '1', '');
INSERT INTO `xmkd_admin_rule` VALUES ('279', 'App/Back/Mession/update_sp_status', '已完成任务 免税店状态', '1', '1', '3', '', '265', '1', '');
INSERT INTO `xmkd_admin_rule` VALUES ('280', 'App/Back/Mession/back_finish', '已完成任务 打回', '1', '1', '3', '', '265', '1', '');
INSERT INTO `xmkd_admin_rule` VALUES ('281', 'App/Back/Mession/dispatch', '分派任务人员', '1', '1', '3', '', '265', '1', '');
INSERT INTO `xmkd_admin_rule` VALUES ('282', 'App/Back/Mession/dispatch_save', '分派人员确定', '1', '1', '3', '', '265', '1', '');
INSERT INTO `xmkd_admin_rule` VALUES ('264', 'App/Back/_public_test_public_module/_public_test_public_action', '数据校对管理', '1', '1', '0', '', '3', '1', '');
INSERT INTO `xmkd_admin_rule` VALUES ('265', 'App/Back/Mession/index', '任务管理', '1', '1', '1', '', '264', '2', '');
INSERT INTO `xmkd_admin_rule` VALUES ('266', 'App/Back/Proof/index', '校对管理', '1', '1', '1', '', '264', '1', '');
INSERT INTO `xmkd_admin_rule` VALUES ('267', 'App/Back/Mession/add', '新增任务', '1', '1', '2', '', '265', '1', '');
INSERT INTO `xmkd_admin_rule` VALUES ('268', 'App/Back/Mession/confirm', '任务确认', '1', '1', '3', '', '265', '1', '');
INSERT INTO `xmkd_admin_rule` VALUES ('269', 'App/Back/Mession/cancel_mession', '任务取消', '1', '1', '3', '', '265', '1', '');
INSERT INTO `xmkd_admin_rule` VALUES ('270', 'App/Back/Mession/index', '任务列表', '1', '1', '2', '', '265', '1', '');
INSERT INTO `xmkd_admin_rule` VALUES ('271', 'App/Back/Proof/index', '任务列表', '1', '1', '2', '', '266', '1', '');

-- ----------------------------
-- Table structure for `xmkd_brand`
-- ----------------------------
DROP TABLE IF EXISTS `xmkd_brand`;
CREATE TABLE `xmkd_brand` (
  `bid` mediumint(8) NOT NULL AUTO_INCREMENT COMMENT '品牌id',
  `cname` varchar(255) NOT NULL COMMENT '品牌中文名称',
  `bchar` varchar(255) NOT NULL COMMENT '品牌首字母',
  `ename` varchar(255) NOT NULL COMMENT '品牌英文名称',
  `pyname` varchar(255) NOT NULL COMMENT '品牌拼音名称',
  `searchename` varchar(255) NOT NULL COMMENT '品牌搜索名称',
  `area` smallint(6) NOT NULL COMMENT '品牌所在地区',
  `priceratio` smallint(6) NOT NULL COMMENT '品牌折扣',
  `goodsnum` mediumint(8) NOT NULL COMMENT '品牌商品数',
  `brandtype` smallint(6) NOT NULL COMMENT '品牌分类',
  `created_in` int(10) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`bid`),
  KEY `cname` (`cname`),
  KEY `ename` (`ename`),
  KEY `num` (`goodsnum`),
  KEY `searchename` (`searchename`)
) ENGINE=InnoDB AUTO_INCREMENT=523 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xmkd_brand
-- ----------------------------
INSERT INTO `xmkd_brand` VALUES ('1', '安娜苏', 'A', 'ANNA SUI', '', 'ANNASUI', '0', '65', '508', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('2', '雅男士', '', 'ARAMIS', '', 'ARAMIS', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('3', '博柏利', '', 'BURBERRY', '', 'BURBERRY', '0', '0', '50', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('4', '宝格丽', '', 'BVLGARI', '', 'BVLGARI', '0', '0', '106', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('5', '博朗', '', 'BRAUN', '', 'BRAUN', '0', '0', '53', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('6', '迪奥', 'D', 'DIOR', '', 'DIOR', '0', '70', '443', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('7', '倩碧', 'C', 'CLINIQUE', '', 'CLINIQUE', '0', '70', '419', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('8', '卡尔文·克莱恩', '', 'CALVIN KLEIN', '', 'CALVINKLEIN', '0', '0', '52', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('9', '大卫杜夫', '', 'DAVIDOFF', '', 'DAVIDOFF', '0', '0', '29', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('10', '碧欧泉', 'B', 'BIOTHERM', '', 'BIOTHERM', '0', '55', '130', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('11', '海蓝之谜', 'L', 'LA MER', '', 'LAMER', '0', '65', '48', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('12', '雅诗兰黛', 'E', 'ESTEE LAUDER', '', 'ESTEELAUDER', '0', '60', '530', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('13', '伊丽莎白雅顿', '', 'ELIZABETH ARDEN', '', 'ELIZABETHARDEN', '0', '0', '144', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('14', '纪梵希', '', 'GIVENCHY', '', 'GIVENCHY', '0', '0', '376', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('15', '娇兰', 'G', 'GUERLAIN', '', 'GUERLAIN', '0', '0', '291', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('16', '爱马仕', '', 'HERMES', '', 'HERMES', '0', '0', '66', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('17', '赫莲娜', 'H', 'HELENA RUBINSTEIN', '', 'HELENARUBINSTEIN', '0', '60', '92', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('18', '三宅一生', 'I', 'ISSEY MIYAKE', '', 'ISSEYMIYAKE', '0', '52', '37', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('19', '高缇耶', '', 'JEAN PAUL GAULTIER', '', 'JEANPAULGAULTIER', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('20', '娇韵诗', 'C', 'CLARINS', '', 'CLARINS', '0', '60', '120', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('21', '凯卓', '', 'KENZO', '', 'KENZO', '0', '0', '41', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('22', '嘉娜宝', '', 'KANEBO', '', 'KANEBO', '0', '0', '84', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('23', '巴黎欧莱雅', 'L', 'LOREAL PARIS', '', 'LOREALPARIS', '0', '55', '217', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('24', '浪凡', '', 'LANVIN', '', 'LANVIN', '0', '0', '19', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('25', '兰蔻', 'L', 'LANCOME', '', 'LANCOME', '0', '65', '375', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('26', '洛丽塔', '', 'LOLITA LEMPICKA', '', 'LOLITALEMPICKA', '0', '0', '23', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('27', '莱珀妮', 'L', 'LA PRAIRIE', '', 'LAPRAIRIE', '0', '78', '116', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('28', '蔓秀莱施', '', 'MOTHERNEST', '', 'MOTHERNEST', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('29', '资生堂', 'S', 'SHISEIDO', '', 'SHISEIDO', '0', '70', '168', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('30', '悦木之源', '', 'ORIGINS', '', 'ORIGINS', '0', '0', '79', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('31', '拉夫·劳伦', '', 'POLO RALPH LAUREN', '', 'POLORALPHLAUREN', '0', '0', '34', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('32', '飞利浦', '', 'PHILIPS', '', 'PHILIPS', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('33', 'SK-II', 'S', 'SK-II', '', 'SK-II', '0', '70', '117', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('34', '希思黎', 'S', 'SISLEY COSMETIC', '', 'SISLEYCOSMETIC', '0', '65', '291', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('35', '伊夫圣罗兰', 'Y', 'YVES SAINT LAURENT', '', 'YVESSAINTLAURENT', '0', '60', '246', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('36', '馥蕾诗', '', 'FRESH', '', 'FRESH', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('37', '欧舒丹', 'L', 'LOCCITANE', '', 'LOCCITANE', '0', '70', '184', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('38', '芭比波朗', '', 'BOBBI BROWN', '', 'BOBBIBROWN', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('39', '植村秀', 'S', 'SHU UEMURA', '', 'SHUUEMURA', '0', '65', '304', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('40', '伊索', '', 'AESOP', '', 'AESOP', '0', '0', '96', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('41', '黛珂', '', 'COSME DECORTE', '', 'COSMEDECORTE', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('42', '仙丽施', '', 'CELLEX-C', '', 'CELLEX-C', '0', '0', '26', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('43', '馥绿德雅', '', 'RENE FURTERER', '', 'RENEFURTERER', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('44', '玫珂菲', '', 'MAKE UP FOR EVER', '', 'MAKEUPFOREVER', '0', '0', '319', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('45', '欧蕙', '', 'OHUI', '', 'OHUI', '0', '0', '174', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('46', '蒂佳婷', '', 'DR.JART', '', 'DR.JART', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('47', '克雷德', '', 'CREED', '', 'CREED', '0', '0', '25', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('48', '乔治·阿玛尼', '', 'GIORGIO ARMANI(PFM)', '', 'GIORGIOARMANI(PFM)', '0', '0', '50', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('49', 'NATURE S FAMILY', '', 'NATURE S FAMILY', '', 'NATURESFAMILY', '0', '0', '79', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('50', '姬斯汀', '', 'SCHRAMMEK', '', 'SCHRAMMEK', '0', '0', '12', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('51', '欧派', '', 'OPI', '', 'OPI', '0', '0', '137', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('52', '茱莉蔻', 'J', 'JURLIQUE', '', 'JURLIQUE', '0', '60', '165', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('53', '贝玲妃', 'B', 'BENEFIT', '', 'BENEFIT', '0', '65', '160', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('54', 'NOODLE & BOO', '', 'NOODLE & BOO', '', 'NOODLE&BOO', '0', '0', '32', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('55', '韩斯清', '', 'HANSKIN', '', 'HANSKIN', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('56', '科颜氏', 'K', 'KIEHL S', '', 'KIEHLS', '0', '70', '124', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('57', '蔻依', '', 'CHLOE', '', 'CHLOE', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('58', 'SKIN79', '', 'SKIN79', '', 'SKIN79', '0', '0', '137', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('59', '后', 'W', 'WHOO', '', 'WHOO', '0', '50', '165', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('60', '朗仕化妆品', 'L', 'LAB SERIES', '', 'LABSERIES', '0', '60', '25', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('61', '赫拉', 'H', 'HERA', '', 'HERA', '0', '65', '108', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('62', '谜尚', '', 'MISSHA', '', 'MISSHA', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('63', '古驰', '', 'GUCCI', '', 'GUCCI', '0', '0', '74', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('64', '托尼魅力', '', 'TONYMOLY', '', 'TONYMOLY', '0', '0', '273', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('65', '菲拉格慕', '', 'SALVATORE FERRAGAMO', '', 'SALVATOREFERRAGAMO', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('66', '菲诗小铺', '', 'THE FACE SHOP', '', 'THEFACESHOP', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('67', '疯狂旋转猴', '', 'CRAZY MONKEY', '', 'CRAZYMONKEY', '0', '0', '35', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('68', '雪花秀', 'S', 'SULWHASOO', '', 'SULWHASOO', '0', '70', '126', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('69', '杜嘉班纳', '', 'DOLCE&amp;GABBANA', '', 'DOLCE&amp;GABBANA', '0', '0', '25', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('70', '蒂普提克', '', 'DIPTYQUE', '', 'DIPTYQUE', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('71', '雨果波士', '', 'HUGO BOSS', '', 'HUGOBOSS', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('72', '达特法姆', '', 'DR.PHAMOR', '', 'DR.PHAMOR', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('73', '伊蒂之屋', '', 'ETUDE HOUSE', '', 'ETUDEHOUSE', '0', '0', '167', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('74', 'SUM37?', '', '苏秘37?', '', '苏秘37?', '0', '0', '114', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('75', '日月晶采', '', 'LUNASOL', '', 'LUNASOL', '0', '0', '241', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('76', '罗拉玛斯亚', '', 'LAURA MERCIER', '', 'LAURAMERCIER', '0', '0', '341', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('77', '玫瑰花蕾膏', '', 'SMITH\'S ROSEBUD SALVE', '', 'SMITH\'SROSEBUDSALVE', '0', '0', '9', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('78', '高丝', '', 'KOSE', '', 'KOSE', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('79', '美体小铺', '', 'THE BODY SHOP', '', 'THEBODYSHOP', '0', '0', '279', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('80', 'STYLERUSH', '', 'STYLERUSH', '', 'STYLERUSH', '0', '0', '4', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('81', '范思哲', '', 'VERSACE', '', 'VERSACE', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('82', '莫斯奇诺', '', 'MOSCHINO', '', 'MOSCHINO', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('83', '莲娜丽姿', '', 'NINA RICCI', '', 'NINARICCI', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('84', '法拉利', '', 'FERRARI', '', 'FERRARI', '0', '0', '32', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('85', '爱茉莉', 'A', 'AMORE PACIFIC', '', 'AMOREPACIFIC', '0', '70', '89', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('86', '婵真', '', 'CHARMZONE', '', 'CHARMZONE', '0', '0', '100', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('87', '雅漾', '', 'AVENE', '', 'AVENE', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('88', '趣乐活', '', 'TRILOGY', '', 'TRILOGY', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('89', '乔治阿玛尼', '', 'GIORGIO ARMANI (COS)', '', 'GIORGIOARMANI(COS)', '0', '0', '294', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('90', '兰芝', 'L', 'LANEIGE', '', 'LANEIGE', '0', '60', '130', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('91', 'EMBRYOLISSE', '', 'EMBRYOLISSE', '', 'EMBRYOLISSE', '0', '0', '9', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('92', '薇欧薇', '', 'VOV', '', 'VOV', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('93', '丽得姿', '', 'LEADERS', '', 'LEADERS', '0', '0', '141', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('94', 'ROYAL NATURE', '', 'ROYAL NATURE', '', 'ROYALNATURE', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('95', 'A.H.C', '', 'A.H.C', '', 'A.H.C', '0', '0', '38', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('96', '纳西索·罗德里格斯', '', 'NARCISO RODRIGUEZ', '', 'NARCISORODRIGUEZ', '0', '0', '23', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('97', '希恩派', '', 'CNP', '', 'CNP', '0', '0', '40', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('98', '芭妮兰', '', 'Banila co.', '', 'Banilaco.', '0', '0', '337', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('99', '约翰·瓦维托斯', '', 'JOHN VARVATOS', '', 'JOHNVARVATOS', '0', '0', '22', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('100', '多娜娴', '', 'DANAHAN', '', 'DANAHAN', '0', '0', '89', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('101', 'RMK', '', 'RMK', '', 'RMK', '0', '0', '333', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('102', '必丽肤', '', 'BELIF', '', 'BELIF', '0', '0', '96', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('103', '自然博士', '', 'DR.NATURAL', '', 'DR.NATURAL', '0', '0', '38', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('104', '江原道', '', 'KOH GEN DO', '', 'KOHGENDO', '0', '0', '55', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('105', '奇士美', '', 'KISS ME', '', 'KISSME', '0', '0', '37', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('106', '爱蕾雅', '', 'LOLLIA', '', 'LOLLIA', '0', '0', '81', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('107', '城野医生', '', 'DR.CILABO', '', 'DR.CILABO', '0', '0', '60', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('108', '贺本清', '', 'HERBACIN', '', 'HERBACIN', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('109', '约翰·里奇蒙德', '', 'JOHN RICHMOND', '', 'JOHNRICHMOND', '0', '0', '10', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('110', '素姬', '', 'SUISKIN', '', 'SUISKIN', '0', '0', '24', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('111', '普拉达', '', 'PRADA', '', 'PRADA', '0', '0', '28', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('112', 'JOHN MASTERS ORGANICS', '', 'JOHN MASTERS ORGANICS', '', 'JOHNMASTERSORGANICS', '0', '0', '60', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('113', '凯文?奥库安', '', 'KEVYN AUCOIN', '', 'KEVYNAUCOIN', '0', '0', '142', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('114', '吉尔斯图尔特', '', 'JILL STUART', '', 'JILLSTUART', '0', '0', '182', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('115', '芙莉美娜', '', 'PRIMERA', '', 'PRIMERA', '0', '0', '102', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('116', '塔莉卡', '', 'TALIKA', '', 'TALIKA', '0', '0', '17', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('117', '妙思乐', '', 'MUSTELA', '', 'MUSTELA', '0', '0', '33', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('118', '必列斯 ', '', 'BLISS', '', 'BLISS', '0', '0', '59', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('119', '欧缇丽', '', 'CAUDALIE', '', 'CAUDALIE', '0', '0', '51', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('120', '珂丽柏蒂', '', 'CLE DE PEAU', '', 'CLEDEPEAU', '0', '0', '136', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('121', '悦诗风吟', '', 'INNISFREE', '', 'INNISFREE', '0', '0', '152', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('122', '艾凡达', '', 'AVEDA', '', 'AVEDA', '0', '0', '136', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('123', 'KATE', '', 'KATE', '', 'KATE', '0', '0', '42', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('124', '忆可恩', '', 'IPKN', '', 'IPKN', '0', '0', '104', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('125', '自然乐园', '', 'NATURE REPUBLIC', '', 'NATUREREPUBLIC', '0', '0', '125', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('126', '周仰杰', '', 'JIMMY CHOO', '', 'JIMMYCHOO', '0', '0', '9', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('127', '万宝龙', '', 'MONTBLANC (PFM)', '', 'MONTBLANC(PFM)', '0', '0', '18', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('128', '秀肤生', '', 'Cell Fusion C', '', 'CellFusionC', '0', '0', '57', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('129', '得鲜', '', 'THE SAEM', '', 'THESAEM', '0', '0', '200', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('130', '伊思', '', 'IT\'S SKIN', '', 'IT\'SSKIN', '0', '0', '198', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('131', '美迪惠尔', '', 'MEDIHEAL', '', 'MEDIHEAL', '0', '0', '107', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('132', '金缕梅', '', 'THAYERS', '', 'THAYERS', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('133', 'MAMBINO ORGANICS', '', 'MAMBINO ORGANICS', '', 'MAMBINOORGANICS', '0', '0', '20', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('134', '贝德玛', '', 'BIODERMA', '', 'BIODERMA', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('135', '迷斯緹安', '', 'MISTIAN', '', 'MISTIAN', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('136', '昂贵公主', '', 'LOTREE', '', 'LOTREE', '0', '0', '45', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('137', '彤人秘', '', 'DONGINBI', '', 'DONGINBI', '0', '0', '104', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('138', '艾丝珀', '', 'ESPOIR', '', 'ESPOIR', '0', '0', '196', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('139', '卡蜜儿', '', 'KAMILL', '', 'KAMILL', '0', '0', '10', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('140', 'OGX', '', 'OGX', '', 'OGX', '0', '0', '57', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('141', '伯特小蜜蜂', '', 'BURT\'S BEES', '', 'BURT\'SBEES', '0', '0', '72', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('142', '丝洛比', '', 'SILK THERAPY', '', 'SILKTHERAPY', '0', '0', '70', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('143', '思亲肤', '', 'SKINFOOD', '', 'SKINFOOD', '0', '0', '254', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('144', '华伦天奴', '', 'VALENTINO', '', 'VALENTINO', '0', '0', '16', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('145', '伊所爱', '', 'ISOI', '', 'ISOI', '0', '0', '80', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('146', '珍碧嘉', '', 'JEANNE PIAUBERT', '', 'JEANNEPIAUBERT', '0', '0', '76', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('147', '自然哲理', '', 'PHILOSOPHY', '', 'PHILOSOPHY', '0', '0', '70', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('148', '百洛护肤油', '', 'BIO OIL', '', 'BIOOIL', '0', '0', '4', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('149', '高丽安', '', 'CAOLION', '', 'CAOLION', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('150', '御木本', '', 'MIKIMOTO COSMETIC', '', 'MIKIMOTOCOSMETIC', '0', '0', '19', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('151', '庭润', '', 'THANN', '', 'THANN', '0', '0', '117', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('152', 'Dr.Jucre', '', 'Dr.Jucre', '', 'Dr.Jucre', '0', '0', '11', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('153', '维多利亚', '', 'VICTORIA', '', 'VICTORIA', '0', '0', '7', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('154', '摩顿布朗', '', 'MOLTON BROWN', '', 'MOLTONBROWN', '0', '0', '146', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('155', '丝荻拉', '', 'STILA', '', 'STILA', '0', '0', '157', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('156', '博尼', '', 'VONIN', '', 'VONIN', '0', '0', '12', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('157', 'BEYOND', '', 'BEYOND', '', 'BEYOND', '0', '0', '73', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('158', '秀雅韩', '', 'SOORYEHAN', '', 'SOORYEHAN', '0', '0', '87', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('159', '伊诺姿', '', 'ISAKNOX', '', 'ISAKNOX', '0', '0', '33', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('160', 'CANMAKE', '', 'CANMAKE', '', 'CANMAKE', '0', '0', '40', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('161', '爱茉诗', '', 'AMOS PROFESSIONAL', '', 'AMOSPROFESSIONAL', '0', '0', '35', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('162', 'CREMORLAB', '', 'CREMORLAB', '', 'CREMORLAB', '0', '0', '36', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('163', 'PENHALIGON\'S', '', 'PENHALIGON\'S', '', 'PENHALIGON\'S', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('164', 'VIDIVICI', '', 'VIDIVICI', '', 'VIDIVICI', '0', '0', '105', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('165', '艾芭薇', '', 'ERBAVIVA', '', 'ERBAVIVA', '0', '0', '54', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('166', '美宝莲纽约', '', 'MAYBELLINE NEWYORK', '', 'MAYBELLINENEWYORK', '0', '0', '26', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('167', 'NARS', '', 'NARS', '', 'NARS', '0', '0', '420', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('168', '神秘卡米拉 ', '', 'GAMILA SECRET', '', 'GAMILASECRET', '0', '0', '11', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('169', '德美乐嘉 ', '', 'DERMALOGICA', '', 'DERMALOGICA', '0', '0', '28', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('170', '爱多康', '', 'ATOPALM', '', 'ATOPALM', '0', '0', '49', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('171', '艾诺碧', 'I', 'IOPE', '', 'IOPE', '0', '65', '142', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('172', 'CHOSUNGAH 22', '', 'CHOSUNGAH 22', '', 'CHOSUNGAH22', '0', '0', '149', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('173', '帕科', '', 'paco rabanne', '', 'pacorabanne', '0', '0', '19', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('174', '凯茜·琦丝敦', '', 'CATH KIDSTON', '', 'CATHKIDSTON', '0', '0', '306', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('175', 'By Pharmicell Lab', '', 'By Pharmicell Lab', '', 'ByPharmicellLab', '0', '0', '15', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('176', '康绮墨丽', '', 'DAENG GI MEO RI', '', 'DAENGGIMEORI', '0', '0', '29', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('177', 'Doctorcos', '', 'Doctorcos', '', 'Doctorcos', '0', '0', '14', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('178', '璞帝妃', '', 'PETITFEE', '', 'PETITFEE', '0', '0', '14', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('179', '荷拉', '', 'HEELAA', '', 'HEELAA', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('180', 'YONKA', '', 'YONKA', '', 'YONKA', '0', '0', '47', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('181', '帕尔玛之水', '', 'ACQUA DI PARMA', '', 'ACQUADIPARMA', '0', '0', '65', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('182', '安霓可·古特尔', '', 'ANNICK GOUTAL', '', 'ANNICKGOUTAL', '0', '0', '38', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('183', '凯诗薇', '', 'KATE SOMERVILLE', '', 'KATESOMERVILLE', '0', '0', '51', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('184', 'LUSH', '', 'LUSH', '', 'LUSH', '0', '0', '187', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('185', 'Maison Francis Kurkdjian Paris', '', 'Maison Francis Kurkdjian Paris', '', 'MaisonFrancisKurkdjianParis', '0', '0', '33', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('186', '伊诗贝格', '', 'EISENBERG', '', 'EISENBERG', '0', '0', '118', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('187', '科莱丽', '', 'clarisonic', '', 'clarisonic', '0', '0', '29', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('188', '法贝儿', '', 'BIOLANE', '', 'BIOLANE', '0', '0', '21', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('189', 'PAUL&JOE', '', 'PAUL&JOE', '', 'PAUL&JOE', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('190', 'TOKYO MILK', '', 'TOKYO MILK', '', 'TOKYOMILK', '0', '0', '89', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('191', '夏依', '', 'SUMMER\'S EVE', '', 'SUMMER\'SEVE', '0', '0', '12', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('192', 'Mymi', '', 'Mymi', '', 'Mymi', '0', '0', '8', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('193', '吕', '', 'RYO', '', 'RYO', '0', '0', '37', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('194', 'NOTS', '', 'NOTS', '', 'NOTS', '0', '0', '56', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('195', 'HI WELL', '', 'HI WELL', '', 'HIWELL', '0', '0', '25', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('196', 'OBSIDIAN', '', 'OBSIDIAN', '', 'OBSIDIAN', '0', '0', '52', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('197', '欧珑', '', 'Atelier Cologne', '', 'AtelierCologne', '0', '0', '61', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('198', 'NEOGEN', '', 'NEOGEN', '', 'NEOGEN', '0', '0', '33', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('199', '九朵云', '', 'Cloud 9', '', 'Cloud9', '0', '0', '17', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('200', 'GUERISSON', '', 'GUERISSON', '', 'GUERISSON', '0', '0', '14', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('201', 'EYECANDY BRUSH', '', 'EYECANDY BRUSH', '', 'EYECANDYBRUSH', '0', '0', '15', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('202', '高丽雅娜', '', 'Coreana', '', 'Coreana', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('203', 'MEDI-PEEL', '', 'MEDI-PEEL', '', 'MEDI-PEEL', '0', '0', '44', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('204', 'BEURER', '', 'BEURER', '', 'BEURER', '0', '0', '59', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('205', '自然晨露', '', 'DEWYTREE', '', 'DEWYTREE', '0', '0', '58', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('206', 'UNIX', '', 'UNIX', '', 'UNIX', '0', '0', '16', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('207', '依泉', '', 'URIAGE', '', 'URIAGE', '0', '0', '36', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('208', 'BANDI', '', 'BANDI', '', 'BANDI', '0', '0', '71', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('209', 'EASYDEW', '', 'EASYDEW', '', 'EASYDEW', '0', '0', '67', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('210', 'SKIN1004', '', 'SKIN1004', '', 'SKIN1004', '0', '0', '2', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('211', '发朵', '', 'PHYTO', '', 'PHYTO', '0', '0', '16', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('212', '斐珞尔', '', 'FOREO', '', 'FOREO', '0', '0', '28', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('213', 'too cool for school', '', 'too cool for school', '', 'toocoolforschool', '0', '0', '113', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('214', 'MAEUX', '', 'MAEUX', '', 'MAEUX', '0', '0', '3', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('215', 'Mei-Klout', '', 'Mei-Klout', '', 'Mei-Klout', '0', '0', '4', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('216', '3 CONCEPT EYES', '', '3 CONCEPT EYES', '', '3CONCEPTEYES', '0', '0', '419', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('217', 'dr.tree', '', 'dr.tree', '', 'dr.tree', '0', '0', '12', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('218', 'THERRSEVEN', '', 'THERRSEVEN', '', 'THERRSEVEN', '0', '0', '10', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('219', 'MUCOTA', '', 'MUCOTA', '', 'MUCOTA', '0', '0', '61', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('220', '天空湖水', '', 'skylake', '', 'skylake', '0', '0', '45', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('221', '菲洛嘉', '', 'FILORGA', '', 'FILORGA', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('222', 'ZONSKIN COSMETIC', '', 'ZONSKIN COSMETIC', '', 'ZONSKINCOSMETIC', '0', '0', '30', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('223', '喜德恩特丽', '', 'SEED&amp;TREE', '', 'SEED&amp;TREE', '0', '0', '22', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('224', 'FASCY', '', 'FASCY', '', 'FASCY', '0', '0', '8', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('225', 'ELIZAVECCA', '', 'ELIZAVECCA', '', 'ELIZAVECCA', '0', '0', '4', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('226', 'TANGLE TEEZER', '', 'TANGLE TEEZER', '', 'TANGLETEEZER', '0', '0', '17', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('227', 'HINIJINI', '', 'HINIJINI', '', 'HINIJINI', '0', '0', '11', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('228', 'SKIN&LAB', '', 'SKIN&LAB', '', 'SKIN&LAB', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('229', '茵葩兰', '', 'ENPRANI', '', 'ENPRANI', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('230', 'Holika Holika', '', 'Holika Holika', '', 'HolikaHolika', '0', '0', '68', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('231', 'CELDERMA', '', 'CELDERMA', '', 'CELDERMA', '0', '0', '38', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('232', 'SNP', '', 'SNP', '', 'SNP', '0', '0', '26', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('233', 'AMINI', '', 'AMINI', '', 'AMINI', '0', '0', '22', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('234', 'Panpuri', '', 'Panpuri', '', 'Panpuri', '0', '0', '19', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('235', 'NEAL\'S YARD REMEDIES', '', 'NEAL\'S YARD REMEDIES', '', 'NEAL\'SYARDREMEDIES', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('236', 'RAINBOW', '', 'RAINBOW', '', 'RAINBOW', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('237', '欧树', '', 'NUXE', '', 'NUXE', '0', '0', '41', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('238', 'BEAUTEECOLLAGEN', '', 'BEAUTEECOLLAGEN', '', 'BEAUTEECOLLAGEN', '0', '0', '19', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('239', '瑞碧儿', '', 'REPERE', '', 'REPERE', '0', '0', '5', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('240', '清淡化妆品', '', 'CHUNGDAM', '', 'CHUNGDAM', '0', '0', '56', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('241', 'LOHASYS', '', 'LOHASYS', '', 'LOHASYS', '0', '0', '10', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('242', 'GELLYFIT', '', 'GELLYFIT', '', 'GELLYFIT', '0', '0', '21', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('243', 'N.BLOSSOM', '', 'N.BLOSSOM', '', 'N.BLOSSOM', '0', '0', '13', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('244', 'TS SHAMPOO', '', 'TS SHAMPOO', '', 'TSSHAMPOO', '0', '0', '5', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('245', 'ROYAL SKIN', '', 'ROYAL SKIN', '', 'ROYALSKIN', '0', '0', '45', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('246', '睿姿丽', '', 'CreBeau', '', 'CreBeau', '0', '0', '30', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('247', '蒂尔芭丽', '', 'DEARBERRY', '', 'DEARBERRY', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('248', 'NAOBAY', '', 'NAOBAY', '', 'NAOBAY', '0', '0', '6', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('249', 'BADAMISO', '', 'BADAMISO', '', 'BADAMISO', '0', '0', '11', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('250', '瑰珀翠', '', 'CRABTREE &amp; EVELYN', '', 'CRABTREE&amp;EVELYN', '0', '0', '29', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('251', 'EOSIKA', '', 'EOSIKA', '', 'EOSIKA', '0', '0', '8', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('252', '奇缔', '', 'Gdew', '', 'Gdew', '0', '0', '21', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('253', 'ILLI', '', 'ILLI', '', 'ILLI', '0', '0', '10', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('254', 'Makeon', '', 'Makeon', '', 'Makeon', '0', '0', '1', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('255', 'ALLVIT', '', 'ALLVIT', '', 'ALLVIT', '0', '0', '32', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('256', 'SERAZENA', '', 'SERAZENA', '', 'SERAZENA', '0', '0', '56', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('257', '施巴', '', 'SEBAMED', '', 'SEBAMED', '0', '0', '29', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('258', '倍轻松', '', 'BREO', '', 'BREO', '0', '0', '14', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('259', 'Secret Age', '', 'Secret Age', '', 'SecretAge', '0', '0', '37', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('260', 'DARPHIN', '', 'DARPHIN', '', 'DARPHIN', '0', '0', '35', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('261', 'MOONSHOT', '', 'MOONSHOT', '', 'MOONSHOT', '0', '0', '209', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('262', '薇姿', '', 'VICHY', '', 'VICHY', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('263', '理肤泉', '', 'LA ROCHE-POSAY', '', 'LAROCHE-POSAY', '0', '0', '56', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('264', '美妆仙', '', 'MISEENSCENE', '', 'MISEENSCENE', '0', '0', '4', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('265', 'PrimaryRaw', '', 'PrimaryRaw', '', 'PrimaryRaw', '0', '0', '16', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('266', 'Seatree', '', 'Seatree', '', 'Seatree', '0', '0', '71', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('267', 'SALSOMAGGIORE', '', 'SALSOMAGGIORE', '', 'SALSOMAGGIORE', '0', '0', '15', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('268', '凡士林', '', 'VASELINE', '', 'VASELINE', '0', '0', '9', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('269', 'NATURE`S TOP', '', 'NATURE`S TOP', '', 'NATURE`STOP', '0', '0', '14', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('270', 'ULOS', '', 'ULOS', '', 'ULOS', '0', '0', '13', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('271', 'CASMARA', '', 'CASMARA', '', 'CASMARA', '0', '0', '19', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('272', 'ZAMIONE', '', 'ZAMIONE', '', 'ZAMIONE', '0', '0', '19', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('273', 'REFA', '', 'REFA', '', 'REFA', '0', '0', '15', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('274', 'A24', '', 'A24', '', 'A24', '0', '0', '19', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('275', 'LUMACA', '', 'LUMACA', '', 'LUMACA', '0', '0', '11', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('276', 'JOAS', '', 'JOAS', '', 'JOAS', '0', '0', '8', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('277', 'PAPA RECIPE', '', 'PAPA RECIPE', '', 'PAPARECIPE', '0', '0', '35', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('278', 'EUBOS', '', 'EUBOS', '', 'EUBOS', '0', '0', '12', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('279', 'PLU', '', 'PLU', '', 'PLU', '0', '0', '47', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('280', 'PhytoTree', '', 'PhytoTree', '', 'PhytoTree', '0', '0', '10', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('281', 'PONY EFFECT', '', 'PONY EFFECT', '', 'PONYEFFECT', '0', '0', '50', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('282', 'I\'M MEME', '', 'I\'M MEME', '', 'I\'MMEME', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('283', 'HOPE GIRL', '', 'HOPE GIRL', '', 'HOPEGIRL', '0', '0', '96', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('284', 'DEESSE', '', 'DEESSE', '', 'DEESSE', '0', '0', '1', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('285', '瑷嘉莎', '', 'AGATHA COSMETIC', '', 'AGATHACOSMETIC', '0', '0', '68', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('286', '莱俪', '', 'LALIQUE', '', 'LALIQUE', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('287', '巴黎卡诗', '', 'KERASTASE', '', 'KERASTASE', '0', '0', '67', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('288', 'VANT36.5', '', 'VANT36.5', '', 'VANT36.5', '0', '0', '75', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('289', 'URBAN DOLLKISS', '', 'URBAN DOLLKISS', '', 'URBANDOLLKISS', '0', '0', '50', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('290', 'SON&amp;PARK', '', 'SON&amp;PARK', '', 'SON&amp;PARK', '0', '0', '0', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('291', 'STEBLANC', '', 'STEBLANC', '', 'STEBLANC', '0', '0', '25', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('292', 'MIZON', '', 'MIZON', '', 'MIZON', '0', '0', '3', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('293', 'CHAKAN FACTORY', '', 'CHAKAN FACTORY', '', 'CHAKANFACTORY', '0', '0', '9', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('294', 'DENTISTE', '', 'DENTISTE', '', 'DENTISTE', '0', '0', '16', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('295', 'SIXPAD', '', 'SIXPAD', '', 'SIXPAD', '0', '0', '2', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('296', 'LIZ K', '', 'LIZ K', '', 'LIZK', '0', '0', '2', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('297', 'TSOB', '', 'TSOB', '', 'TSOB', '0', '0', '1', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('298', 'JAYJUN', '', 'JAYJUN', '', 'JAYJUN', '0', '0', '5', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('299', 'Dr.Althea', '', 'Dr.Althea', '', 'Dr.Althea', '0', '0', '12', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('300', 'ABOUT ME', '', 'ABOUT ME', '', 'ABOUTME', '0', '0', '19', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('301', 'CP-1', '', 'CP-1', '', 'CP-1', '0', '0', '5', '1', '1459993886');
INSERT INTO `xmkd_brand` VALUES ('302', '安普里奥.阿玛尼', '', 'EMPORIO ARMANI', '', 'EMPORIOARMANI', '0', '0', '0', '0', '1462428995');
INSERT INTO `xmkd_brand` VALUES ('303', '迪赛', '', 'DIESEL', '', 'DIESEL', '0', '0', '0', '0', '1462428995');
INSERT INTO `xmkd_brand` VALUES ('304', '唐可娜儿', '', 'DKNY', '', 'DKNY', '0', '0', '0', '0', '1462428995');
INSERT INTO `xmkd_brand` VALUES ('305', '杰尼亚', '', 'Ermenegildo Zegna', '', 'ErmenegildoZegna', '0', '0', '0', '0', '1462428995');
INSERT INTO `xmkd_brand` VALUES ('306', '宾利', '', 'BENTLEY', '', 'BENTLEY', '0', '0', '0', '0', '1462429196');
INSERT INTO `xmkd_brand` VALUES ('307', '葆蝶家', '', 'BOTTEGA VENETA', '', 'BOTTEGAVENETA', '0', '0', '0', '0', '1462429958');
INSERT INTO `xmkd_brand` VALUES ('308', '乔治?阿玛尼', '', 'GIORGIO ARMANI', '', 'GIORGIOARMANI', '0', '0', '0', '0', '1462429958');
INSERT INTO `xmkd_brand` VALUES ('309', '安普里奥?阿玛尼', '', 'EMPORIO ARMANI', '', 'EMPORIOARMANI', '0', '0', '0', '0', '1462429958');
INSERT INTO `xmkd_brand` VALUES ('310', '马克·雅可布', '', 'MARC JACOBS(PFM)', '', 'MARCJACOBS(PFM)', '0', '0', '0', '0', '1462429958');
INSERT INTO `xmkd_brand` VALUES ('311', '洁净', '', 'CLEAN', '', 'CLEAN', '0', '0', '0', '0', '1462429958');
INSERT INTO `xmkd_brand` VALUES ('312', '橘滋', '', 'JUICY COUTURE', '', 'JUICYCOUTURE', '0', '0', '0', '0', '1462429958');
INSERT INTO `xmkd_brand` VALUES ('313', '扬基蜡烛', '', 'YANKEE CANDLE', '', 'YANKEECANDLE', '0', '0', '0', '0', '1462429958');
INSERT INTO `xmkd_brand` VALUES ('314', 'WOOD WICK', '', 'WOOD WICK', '', 'WOODWICK', '0', '0', '0', '0', '1462429958');
INSERT INTO `xmkd_brand` VALUES ('315', '朵昂思', '', 'DURANCE', '', 'DURANCE', '0', '0', '0', '0', '1462429958');
INSERT INTO `xmkd_brand` VALUES ('316', '奔驰', '', 'BENZ', '', 'BENZ', '0', '0', '0', '0', '1462429958');
INSERT INTO `xmkd_brand` VALUES ('317', 'ESTEBAN', '', 'ESTEBAN', '', 'ESTEBAN', '0', '0', '0', '0', '1462429958');
INSERT INTO `xmkd_brand` VALUES ('318', '迈克·科尔斯', '', 'MICHAEL KORS', '', 'MICHAELKORS', '0', '0', '0', '0', '1462429958');
INSERT INTO `xmkd_brand` VALUES ('319', '艾特罗', '', 'ETRO', '', 'ETRO', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('320', '阿玛尼·卡尔兹', '', '阿玛尼·卡尔兹', '', '阿玛尼·卡尔兹', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('321', '奥伦彼安克', '', 'OROBIANCO', '', 'OROBIANCO', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('322', '巴利', '', 'BALLY', '', 'BALLY', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('323', '百年灵', '', '百年灵', '', '百年灵', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('324', '保罗·史密斯', '', 'PAUL SMITH', '', 'PAULSMITH', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('325', '宝格丽 BTQ', '', '宝格丽 BTQ', '', '宝格丽BTQ', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('326', '宝缇嘉', '', '宝缇嘉', '', '宝缇嘉', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('327', '巴宝莉（BTQ)', '', '巴宝莉（BTQ)', '', '巴宝莉（BTQ)', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('328', '布里克斯', '', 'BRIC\'S', '', 'BRIC\'S', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('329', '巴黎世家（BTQ)', '', '巴黎世家（BTQ)', '', '巴黎世家（BTQ)', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('330', '滨波', '', 'BEAN POLE', '', 'BEANPOLE', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('331', 'BLACK MARTINE SITBON', '', 'BLACK MARTINE SITBON', '', 'BLACKMARTINESITBON', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('332', '贝纳通', '', 'BENETTON', '', 'BENETTON', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('333', 'COURONNE', '', 'COURONNE', '', 'COURONNE', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('334', 'CUTIES AND PALS', '', 'CUTIES AND PALS', '', 'CUTIESANDPALS', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('335', 'CRASH BAGGAGE', '', 'CRASH BAGGAGE', '', 'CRASHBAGGAGE', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('336', '都彭', '', 'S.T. DUPONT', '', 'S.T.DUPONT', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('337', '蒂芬尼', '', '蒂芬尼', '', '蒂芬尼', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('338', '达克斯', '', 'DAKS', '', 'DAKS', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('339', 'DANK', '', 'DANK', '', 'DANK', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('340', 'DECKE', '', 'DECKE', '', 'DECKE', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('341', 'DISNEY &amp; MARVEL', '', 'DISNEY &amp; MARVEL', '', 'DISNEY&amp;MARVEL', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('342', 'DOT-DROPS', '', 'DOT-DROPS', '', 'DOT-DROPS', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('343', 'EASTPAK', '', 'EASTPAK', '', 'EASTPAK', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('344', '芙丽芙丽', '', 'FOLLI FOLLIE', '', 'FOLLIFOLLIE', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('345', '芬迪（BTQ）', '', '芬迪（BTQ）', '', '芬迪（BTQ）', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('346', '芙拉', '', 'FURLA', '', 'FURLA', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('347', '菲拉格慕（BTQ）', '', '菲拉格慕（BTQ）', '', '菲拉格慕（BTQ）', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('348', '古驰 BTQ', '', '古驰 BTQ', '', '古驰BTQ', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('349', '古奇（WH）', '', '古奇（WH）', '', '古奇（WH）', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('350', '哈吉斯', '', '哈吉斯', '', '哈吉斯', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('351', 'HELIANTHUS', '', 'HELIANTHUS', '', 'HELIANTHUS', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('352', 'HAVIANOO', '', 'HAVIANOO', '', 'HAVIANOO', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('353', 'HATSON', '', 'HATSON', '', 'HATSON', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('354', 'HORMIGA', '', 'HORMIGA', '', 'HORMIGA', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('355', '吉普林', '', 'KIPLING', '', 'KIPLING', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('356', 'J.ESTINA', '', 'J.ESTINA', '', 'J.ESTINA', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('357', 'JIMMY CHOO 周仰杰', '', 'JIMMY CHOO 周仰杰', '', 'JIMMYCHOO周仰杰', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('358', 'JILL STUART', '', 'JILL STUART', '', 'JILLSTUART', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('359', '卡地亚', '', '卡地亚', '', '卡地亚', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('360', '蔻驰', '', 'COACH', '', 'COACH', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('361', '看步', '', '看步', '', '看步', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('362', '克洛伊（BTQ)', '', '克洛伊（BTQ)', '', '克洛伊（BTQ)', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('363', 'KANGOL', '', 'KANGOL', '', 'KANGOL', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('364', '珑骧', '', 'LONGCHAMP', '', 'LONGCHAMP', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('365', '罗意威(LOEWE)', '', '罗意威(LOEWE)', '', '罗意威(LOEWE)', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('366', '力士保', '', 'LESPORTSAC', '', 'LESPORTSAC', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('367', '劳力士', '', '劳力士', '', '劳力士', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('368', '拉蝶翔', '', 'LAVISANT', '', 'LAVISANT', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('369', '瑞克朵丝', '', 'LOUIS QUATORZE', '', 'LOUISQUATORZE', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('370', '路易威登', '', '路易威登', '', '路易威登', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('371', '乐斯菲斯', '', 'THE NORTH FACE', '', 'THENORTHFACE', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('372', 'LEE GEON MAAN', '', 'LEE GEON MAAN', '', 'LEEGEONMAAN', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('373', '拉夫·劳伦马球（BTO）', '', '拉夫·劳伦马球（BTO）', '', '拉夫·劳伦马球（BTO）', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('374', 'LOVCAT', '', 'LOVCAT', '', 'LOVCAT', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('375', '陆心媛', '', 'YOUK SHIM WON', '', 'YOUKSHIMWON', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('376', '丽派朵', '', 'REPETTO', '', 'REPETTO', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('377', '龙卡多', '', 'RONCATO', '', 'RONCATO', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('378', 'LIPAULT', '', 'LIPAULT', '', 'LIPAULT', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('379', '意大利鸳鸯', '', 'MANDARINA DUCK', '', 'MANDARINADUCK', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('380', '马克·雅可布(BTQ)', '', 'MARC JACOBS(BTQ)', '', 'MARCJACOBS(BTQ)', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('381', '麦丝玛拉', '', 'MaxMara BTQ', '', 'MaxMaraBTQ', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('382', 'METROCITY', '', 'METROCITY', '', 'METROCITY', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('383', '美旅', '', 'AMERICAN TOURISTER', '', 'AMERICANTOURISTER', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('384', 'MULCO', '', 'MULCO', '', 'MULCO', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('385', 'MOLDIR', '', 'MOLDIR', '', 'MOLDIR', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('386', 'MOLESKINE', '', 'MOLESKINE', '', 'MOLESKINE', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('387', 'MARHEN.J', '', 'MARHEN.J', '', 'MARHEN.J', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('388', 'NBA', '', 'NBA', '', 'NBA', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('389', 'ORYANY', '', 'ORYANY', '', 'ORYANY', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('390', '埃尔维罗·马汀尼', '', 'PRIMA CLASSE', '', 'PRIMACLASSE', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('391', '普拉达(BTQ)', '', 'PRADA(BTQ)', '', 'PRADA(BTQ)', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('392', '苹果', '', '苹果', '', '苹果', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('393', 'Pomme d\'Ellie', '', 'Pomme d\'Ellie', '', 'Pommed\'Ellie', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('394', 'PETIT ELIN', '', 'PETIT ELIN', '', 'PETITELIN', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('395', 'Pink Lining', '', 'Pink Lining', '', 'PinkLining', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('396', 'Playnomore', '', 'Playnomore', '', 'Playnomore', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('397', '瑞贝卡·明可弗', '', 'REBECCA MINKOFF', '', 'REBECCAMINKOFF', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('398', 'rouge&lounge', '', 'rouge&lounge', '', 'rouge&lounge', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('399', 'ROBERTA PIERI', '', 'ROBERTA PIERI', '', 'ROBERTAPIERI', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('400', '赛琳（BTQ）', '', '赛琳（BTQ）', '', '赛琳（BTQ）', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('401', 'ST.JOHN', '', 'ST.JOHN', '', 'ST.JOHN', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('402', '笑脸', '', 'SONOVI', '', 'SONOVI', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('403', '尚美珠宝', '', '尚美珠宝', '', '尚美珠宝', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('404', 'SORBEBE', '', 'SORBEBE', '', 'SORBEBE', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('405', '途明', '', 'TUMI', '', 'TUMI', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('406', '托德斯', '', '托德斯', '', '托德斯', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('407', '拓意卡', '', 'TROIKA', '', 'TROIKA', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('408', '托里·伯奇', '', '托里·伯奇', '', '托里·伯奇', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('409', 'THAVMA', '', 'THAVMA', '', 'THAVMA', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('410', 'TASAKI', '', 'TASAKI', '', 'TASAKI', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('411', '维维安·韦斯特伍德', '', 'VIVIENNE WESTWOOD(BTQ)', '', 'VIVIENNEWESTWOOD(BTQ)', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('412', '维氏', '', 'VICTORINOX', '', 'VICTORINOX', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('413', '新秀丽', '', 'SAMSONITE', '', 'SAMSONITE', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('414', '小马包', '', 'LAPALETTE', '', 'LAPALETTE', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('415', 'YEUNWOO', '', 'YEUNWOO', '', 'YEUNWOO', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('416', '欧米茄手表', '', '欧米茄手表', '', '欧米茄手表', '0', '0', '0', '0', '1462884151');
INSERT INTO `xmkd_brand` VALUES ('417', 'MEN\'S COLLECTION', '', 'MEN\'S COLLECTION', '', 'MEN\'SCOLLECTION', '0', '0', '0', '0', '1462884262');
INSERT INTO `xmkd_brand` VALUES ('521', '施华洛世奇', '', 'SWAROVSKI', '', 'SWAROVSKI', '0', '0', '0', '0', '0');
INSERT INTO `xmkd_brand` VALUES ('419', 'PHILIPP PLEIN', '', 'PHILIPP PLEIN', '', 'PHILIPPPLEIN', '0', '0', '0', '0', '1462884278');
INSERT INTO `xmkd_brand` VALUES ('420', 'RECIFE', '', 'RECIFE', '', 'RECIFE', '0', '0', '0', '0', '1462884278');
INSERT INTO `xmkd_brand` VALUES ('421', 'VICTRIX (la Vetrina)', '', 'VICTRIX (la Vetrina)', '', 'VICTRIX(laVetrina)', '0', '0', '0', '0', '1462884278');
INSERT INTO `xmkd_brand` VALUES ('422', '雅柏', '', 'ALBA', '', 'ALBA', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('423', 'ARMANI EXCHANGE', '', 'ARMANI EXCHANGE', '', 'ARMANIEXCHANGE', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('424', '爱格纳', '', 'AIGNER (WH)', '', 'AIGNER(WH)', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('425', 'ARMANI SWISS', '', 'ARMANI SWISS', '', 'ARMANISWISS', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('426', 'Betsey Johnson', '', 'Betsey Johnson', '', 'BetseyJohnson', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('427', '名士', '', 'BAUME&MERCIER', '', 'BAUME&MERCIER', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('428', '丹尼尔·惠灵顿', '', 'Daniel Wellington', '', 'DanielWellington', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('429', 'ELLE', '', 'ELLE', '', 'ELLE', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('430', '康斯登', '', 'FREDERIQUE CONSTANT', '', 'FREDERIQUECONSTANT', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('431', '芬迪', '', 'FENDI(WH)', '', 'FENDI(WH)', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('432', '飞亚达', '', 'FIYTA', '', 'FIYTA', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('433', '盖尔斯手表', '', 'GUESS(WH)', '', 'GUESS(WH)', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('434', 'Gaga Milano', '', 'Gaga Milano', '', 'GagaMilano', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('435', '泰格豪雅', '', 'TAG HEUER', '', 'TAGHEUER', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('436', '豪利时', '', 'ORIS', '', 'ORIS', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('437', '汉米尔顿', '', 'HAMILTON', '', 'HAMILTON', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('438', '赫柏林', '', 'HERBELIN', '', 'HERBELIN', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('439', '晶振', '', 'SEIKO', '', 'SEIKO', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('440', '卡西欧', '', 'CASIO', '', 'CASIO', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('441', '寇驰', '', 'COACH(WH)', '', 'COACH(WH)', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('442', '卡尔·拉格菲尔德', '', 'KARL LAGERFELD(WH)', '', 'KARLLAGERFELD(WH)', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('443', 'KENZO (WH)', '', 'KENZO (WH)', '', 'KENZO(WH)', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('444', '浪琴', '', 'LONGINES', '', 'LONGINES', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('445', '雷达', '', 'RADO', '', 'RADO', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('446', '罗斯蒙特', '', 'ROSEMONT', '', 'ROSEMONT', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('447', '鲁美诺斯', '', 'LUMINOX', '', 'LUMINOX', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('448', '拉尔森', '', 'LARS LARSEN', '', 'LARSLARSEN', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('449', '梅花表', '', 'TITONI', '', 'TITONI', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('450', 'MISAKI', '', 'MISAKI', '', 'MISAKI', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('451', '美度', '', 'MIDO', '', 'MIDO', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('452', 'MILTON STELLE', '', 'MILTON STELLE', '', 'MILTONSTELLE', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('453', 'MARBEN', '', 'MARBEN', '', 'MARBEN', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('454', '尼克松', '', 'NIXON', '', 'NIXON', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('455', 'NOMINATION', '', 'NOMINATION', '', 'NOMINATION', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('456', '百丽格', '', 'PILGRIM', '', 'PILGRIM', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('457', '瑞士军表', '', 'SWISS MILITARY', '', 'SWISSMILITARY', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('458', '瑞士维氏军刀', '', 'VICTORINOX SWISS ARMY', '', 'VICTORINOXSWISSARMY', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('459', '瑞士罗马表 ', '', 'ROAMER', '', 'ROAMER', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('460', '浪漫神', '', 'ROMANSON', '', 'ROMANSON', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('461', '??施华洛世奇', '', 'SWAROVSKI', '', 'SWAROVSKI', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('462', '斯沃琪', '', 'SWATCH', '', 'SWATCH', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('463', '巨石阵', '', 'STONE HENGE', '', 'STONEHENGE', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('464', '斯卡恩', '', 'SKAGEN', '', 'SKAGEN', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('465', '颂拓', '', 'SUUNTO', '', 'SUUNTO', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('466', '天梭', '', 'TISSOT', '', 'TISSOT', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('467', '添柏岚', '', 'TIMBERLAND', '', 'TIMBERLAND', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('468', '汤丽柏琦', '', 'TORY BURCH', '', 'TORYBURCH', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('469', 'Vivienne Westwood(wh)', '', 'Vivienne Westwood(wh)', '', 'VivienneWestwood(wh)', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('470', '西铁城', '', 'CITIZEN', '', 'CITIZEN', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('471', '英纳格', '', 'ENICAR', '', 'ENICAR', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('472', '英格索尔', '', 'INGERSOLL', '', 'INGERSOLL', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('473', 'ZASPERO', '', 'ZASPERO', '', 'ZASPERO', '0', '0', '0', '0', '1462884296');
INSERT INTO `xmkd_brand` VALUES ('474', '欧克利', '', 'OAKLEY', '', 'OAKLEY', '0', '0', '0', '0', '1462884307');
INSERT INTO `xmkd_brand` VALUES ('475', '艾斯卡达', '', 'ESCADA(EYE)', '', 'ESCADA(EYE)', '0', '0', '0', '0', '1462884307');
INSERT INTO `xmkd_brand` VALUES ('476', '宝格丽', '', 'BVLGARI', '', 'BVLGARI', '0', '0', '0', '0', '1462884307');
INSERT INTO `xmkd_brand` VALUES ('477', '保时捷', '', 'PORSCHE DESIGN', '', 'PORSCHEDESIGN', '0', '0', '0', '0', '1462884307');
INSERT INTO `xmkd_brand` VALUES ('478', '巴尔曼', '', 'BALMAIN', '', 'BALMAIN', '0', '0', '0', '0', '1462884307');
INSERT INTO `xmkd_brand` VALUES ('479', '巴黎世家', '', 'BALENCIAGA', '', 'BALENCIAGA', '0', '0', '0', '0', '1462884307');
INSERT INTO `xmkd_brand` VALUES ('480', '暴龙', '', 'BOLON', '', 'BOLON', '0', '0', '0', '0', '1462884307');
INSERT INTO `xmkd_brand` VALUES ('481', 'BABY BANZ', '', 'BABY BANZ', '', 'BABYBANZ', '0', '0', '0', '0', '1462884307');
INSERT INTO `xmkd_brand` VALUES ('482', 'DSQUARED2', '', 'DSQUARED2', '', 'DSQUARED2', '0', '0', '0', '0', '1462884307');
INSERT INTO `xmkd_brand` VALUES ('483', 'ED Hardy (EYE)', '', 'ED Hardy (EYE)', '', 'EDHardy(EYE)', '0', '0', '0', '0', '1462884307');
INSERT INTO `xmkd_brand` VALUES ('484', '法国鳄鱼', '', 'LACOSTE(EYE)', '', 'LACOSTE(EYE)', '0', '0', '0', '0', '1462884307');
INSERT INTO `xmkd_brand` VALUES ('485', '斐乐', '', 'FILA', '', 'FILA', '0', '0', '0', '0', '1462884307');
INSERT INTO `xmkd_brand` VALUES ('486', '盖尔斯', '', 'GUESS(EYE)', '', 'GUESS(EYE)', '0', '0', '0', '0', '1462884307');
INSERT INTO `xmkd_brand` VALUES ('487', '卡尔·拉格斐尔德', '', 'KARL LAGERFELD', '', 'KARLLAGERFELD', '0', '0', '0', '0', '1462884307');
INSERT INTO `xmkd_brand` VALUES ('488', 'KENZO (EYE)', '', 'KENZO (EYE)', '', 'KENZO(EYE)', '0', '0', '0', '0', '1462884307');
INSERT INTO `xmkd_brand` VALUES ('489', 'KSUBI', '', 'KSUBI', '', 'KSUBI', '0', '0', '0', '0', '1462884307');
INSERT INTO `xmkd_brand` VALUES ('490', '凯伦沃克', '', 'KAREN WALKER', '', 'KARENWALKER', '0', '0', '0', '0', '1462884307');
INSERT INTO `xmkd_brand` VALUES ('491', '雷朋', '', 'RAY-BAN', '', 'RAY-BAN', '0', '0', '0', '0', '1462884307');
INSERT INTO `xmkd_brand` VALUES ('492', '罗意威', '', 'LOEWE', '', 'LOEWE', '0', '0', '0', '0', '1462884307');
INSERT INTO `xmkd_brand` VALUES ('493', '麦丝玛拉 (眼镜)', '', 'MAXMARA (EYE)', '', 'MAXMARA(EYE)', '0', '0', '0', '0', '1462884307');
INSERT INTO `xmkd_brand` VALUES ('494', 'MAUI JIM', '', 'MAUI JIM', '', 'MAUIJIM', '0', '0', '0', '0', '1462884307');
INSERT INTO `xmkd_brand` VALUES ('495', 'Marie Claire (EYE)', '', 'Marie Claire (EYE)', '', 'MarieClaire(EYE)', '0', '0', '0', '0', '1462884307');
INSERT INTO `xmkd_brand` VALUES ('496', 'MARTINE SITBON', '', 'MARTINE SITBON', '', 'MARTINESITBON', '0', '0', '0', '0', '1462884307');
INSERT INTO `xmkd_brand` VALUES ('497', '普拉达(EYE)', '', 'PRADA(EYE)', '', 'PRADA(EYE)', '0', '0', '0', '0', '1462884307');
INSERT INTO `xmkd_brand` VALUES ('498', '普里斯', '', 'POLICE', '', 'POLICE', '0', '0', '0', '0', '1462884307');
INSERT INTO `xmkd_brand` VALUES ('499', '璐迪', '', 'RUDY PROJECT', '', 'RUDYPROJECT', '0', '0', '0', '0', '1462884307');
INSERT INTO `xmkd_brand` VALUES ('500', '赛琳', '', 'CELINE', '', 'CELINE', '0', '0', '0', '0', '1462884307');
INSERT INTO `xmkd_brand` VALUES ('501', '汤姆·福特', '', 'TOMFORD', '', 'TOMFORD', '0', '0', '0', '0', '1462884307');
INSERT INTO `xmkd_brand` VALUES ('502', 'TORY BURCH', '', 'TORY BURCH', '', 'TORYBURCH', '0', '0', '0', '0', '1462884307');
INSERT INTO `xmkd_brand` VALUES ('503', 'THE NUMERO', '', 'THE NUMERO', '', 'THENUMERO', '0', '0', '0', '0', '1462884307');
INSERT INTO `xmkd_brand` VALUES ('504', 'VEDI VERO', '', 'VEDI VERO', '', 'VEDIVERO', '0', '0', '0', '0', '1462884307');
INSERT INTO `xmkd_brand` VALUES ('505', '维果罗夫', '', 'VIKTOR &amp; ROLF', '', 'VIKTOR&amp;ROLF', '0', '0', '0', '0', '1462884307');
INSERT INTO `xmkd_brand` VALUES ('506', '沃尔夫冈', '', 'BYWP (EYE)', '', 'BYWP(EYE)', '0', '0', '0', '0', '1462884307');
INSERT INTO `xmkd_brand` VALUES ('507', '肖邦', '', 'CHOPARD(EYE)', '', 'CHOPARD(EYE)', '0', '0', '0', '0', '1462884307');
INSERT INTO `xmkd_brand` VALUES ('508', 'ZADIG&amp;VOTAIRE (EYE)', '', 'ZADIG&amp;VOTAIRE (EYE)', '', 'ZADIG&amp;VOTAIRE(EYE)', '0', '0', '0', '0', '1462884307');
INSERT INTO `xmkd_brand` VALUES ('509', 'BACIO BACI', '', 'BACIO BACI', '', 'BACIOBACI', '0', '0', '0', '0', '1462884326');
INSERT INTO `xmkd_brand` VALUES ('510', 'Cruciani', '', 'Cruciani', '', 'Cruciani', '0', '0', '0', '0', '1462884326');
INSERT INTO `xmkd_brand` VALUES ('511', 'DIDIER DUBOT', '', 'DIDIER DUBOT', '', 'DIDIERDUBOT', '0', '0', '0', '0', '1462884326');
INSERT INTO `xmkd_brand` VALUES ('512', 'JUVIA JEWELLERY', '', 'JUVIA JEWELLERY', '', 'JUVIAJEWELLERY', '0', '0', '0', '0', '1462884326');
INSERT INTO `xmkd_brand` VALUES ('513', '明洞紫水晶', '', 'M.D JEWELRY', '', 'M.DJEWELRY', '0', '0', '0', '0', '1462884326');
INSERT INTO `xmkd_brand` VALUES ('514', 'MOLLIS', '', 'MOLLIS', '', 'MOLLIS', '0', '0', '0', '0', '1462884326');
INSERT INTO `xmkd_brand` VALUES ('515', '潘多拉', '', 'PANDORA', '', 'PANDORA', '0', '0', '0', '0', '1462884326');
INSERT INTO `xmkd_brand` VALUES ('516', 'ROPEKA', '', 'ROPEKA', '', 'ROPEKA', '0', '0', '0', '0', '1462884326');
INSERT INTO `xmkd_brand` VALUES ('517', 'TIRR LIRR', '', 'TIRR LIRR', '', 'TIRRLIRR', '0', '0', '0', '0', '1462884326');
INSERT INTO `xmkd_brand` VALUES ('518', 'TOSCOW', '', 'TOSCOW', '', 'TOSCOW', '0', '0', '0', '0', '1462884326');
INSERT INTO `xmkd_brand` VALUES ('519', 'VallenTInoRudy', '', 'VallenTInoRudy', '', 'VallenTInoRudy', '0', '0', '0', '0', '1462884326');
INSERT INTO `xmkd_brand` VALUES ('522', 'KENZO', '', 'KENZO', '', 'KENZO', '0', '0', '0', '0', '0');

-- ----------------------------
-- Table structure for `xmkd_check`
-- ----------------------------
DROP TABLE IF EXISTS `xmkd_check`;
CREATE TABLE `xmkd_check` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ProductId` int(11) NOT NULL COMMENT '''商品id''',
  `CategoryId` varchar(255) NOT NULL COMMENT '商品一级分类',
  `ScategoryId` varchar(255) DEFAULT NULL COMMENT '商品二级分类',
  `TcategoryId` varchar(255) DEFAULT NULL COMMENT '商品三级分类',
  `PicUrl` varchar(255) DEFAULT NULL,
  `Sign` tinyint(3) NOT NULL COMMENT '图片标识',
  `Brand` smallint(6) NOT NULL COMMENT '商品品牌id',
  `Name` varchar(255) NOT NULL COMMENT '商品名称',
  `Oprice` varchar(255) NOT NULL DEFAULT '0.00' COMMENT '商品原价',
  `Price` varchar(255) NOT NULL DEFAULT '0.00' COMMENT '商品乐天折扣价',
  `Volume` varchar(255) DEFAULT NULL COMMENT '扩展字段1',
  `Color` text COMMENT '扩展字段2',
  `Material` text COMMENT '扩展字段3',
  `Size` text COMMENT '扩展字段4',
  `Notice` varchar(255) DEFAULT NULL COMMENT '扩展字段5',
  `Country` smallint(6) DEFAULT NULL COMMENT '扩展字段6',
  `Specifications` text COMMENT '扩展字段7',
  `Introduce` text COMMENT '扩展字段8',
  `Code` text COMMENT '商品编码',
  `updated_in` int(11) unsigned zerofill DEFAULT NULL COMMENT '更新时间',
  `mession_id` varchar(20) DEFAULT NULL COMMENT '任务Id',
  `data_user_id` int(11) DEFAULT NULL COMMENT '数据人员',
  `operator_id` int(11) DEFAULT NULL COMMENT '运营人员',
  `check_time` int(11) DEFAULT NULL COMMENT '检查时间',
  `confirm_time` int(11) DEFAULT NULL COMMENT '确认时间',
  `check_status` tinyint(1) DEFAULT '1' COMMENT '1:待校验，2:已打回，3:打回待校验，4:待入库',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ProductId` (`ProductId`),
  KEY `Brand` (`Brand`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `xmkd_log`
-- ----------------------------
DROP TABLE IF EXISTS `xmkd_log`;
CREATE TABLE `xmkd_log` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1管理员2用户',
  `ip` bigint(20) unsigned NOT NULL DEFAULT '0',
  `save_time` int(10) unsigned NOT NULL DEFAULT '0',
  `log_type` enum('USER_ERROR','INFO','SYSTEM_ERROR') NOT NULL,
  `userid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '当是用户的时候可能为0，',
  `username` varchar(40) NOT NULL,
  `operate_model` varchar(30) NOT NULL COMMENT '操作模型',
  `source_url` varchar(300) NOT NULL COMMENT '来源url',
  `possible_sql` varchar(1000) NOT NULL COMMENT '可能的SQL语句',
  `content` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='日志表';

-- ----------------------------
-- Table structure for `xmkd_login_error`
-- ----------------------------
DROP TABLE IF EXISTS `xmkd_login_error`;
CREATE TABLE `xmkd_login_error` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `login_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1管理员2会员',
  `test_username` varchar(40) NOT NULL COMMENT '尝试用户名',
  `test_password` varchar(40) NOT NULL COMMENT '尝试密码',
  `error_ip` bigint(20) unsigned NOT NULL,
  `error_num` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `error_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录出错的时间',
  PRIMARY KEY (`id`),
  KEY `error_ip` (`error_ip`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='会员登录出错表';

-- ----------------------------
-- Table structure for `xmkd_login_success`
-- ----------------------------
DROP TABLE IF EXISTS `xmkd_login_success`;
CREATE TABLE `xmkd_login_success` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `userid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `login_ip` bigint(20) unsigned NOT NULL DEFAULT '0',
  `login_time` int(10) unsigned NOT NULL DEFAULT '0',
  `username` varchar(40) NOT NULL,
  `login_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1管理员2会员 ',
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='登录日志表';

-- ----------------------------
-- Table structure for `xmkd_mession`
-- ----------------------------
DROP TABLE IF EXISTS `xmkd_mession`;
CREATE TABLE `xmkd_mession` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mession_id` varchar(20) NOT NULL COMMENT '任务id',
  `data_user_id` int(11) DEFAULT NULL COMMENT '数据人员',
  `operator_id` int(11) DEFAULT NULL COMMENT '运营人员id',
  `dispatch_time` int(11) DEFAULT NULL COMMENT '分派任务时间',
  `finished_time` int(11) DEFAULT NULL COMMENT '完成任务时间',
  `mession_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:待分派，2:已分派，3:待确认，4:已打回，5:已完成，6:已关闭，7:已付款',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
-- ----------------------------
-- Table structure for `xmkd_shop`
-- ----------------------------
DROP TABLE IF EXISTS `xmkd_shop`;
CREATE TABLE `xmkd_shop` (
  `sid` smallint(6) NOT NULL AUTO_INCREMENT COMMENT '商铺id',
  `name` varchar(255) NOT NULL COMMENT '商铺中文名称',
  `ename` varchar(255) NOT NULL COMMENT '商铺英文名称',
  `areaid` smallint(6) NOT NULL COMMENT '商铺地区id',
  `uplevel` smallint(6) NOT NULL COMMENT '商铺上级分类',
  `sbrand` tinyint(3) NOT NULL COMMENT '商铺品牌',
  `displayorder` tinyint(3) NOT NULL COMMENT '商铺排序',
  `currency` varchar(255) NOT NULL COMMENT '商铺使用货币',
  PRIMARY KEY (`sid`),
  KEY `displayorder` (`displayorder`),
  KEY `sbrand` (`sbrand`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `xmkd_shop_product`
-- ----------------------------
DROP TABLE IF EXISTS `xmkd_shop_product`;
CREATE TABLE `xmkd_shop_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '商店Id',
  `shop_id` int(11) DEFAULT NULL COMMENT '店铺id',
  `shop_name` varchar(20) DEFAULT NULL COMMENT '免税店名称',
  `shop_url` varchar(255) DEFAULT NULL COMMENT '店铺URL',
  `product_id` int(11) DEFAULT NULL COMMENT '产品Id',
  `product_name` varchar(255) DEFAULT NULL COMMENT '产品名称',
  `brand_id` int(11) DEFAULT NULL COMMENT '品牌Id',
  `brand_cname` varchar(255) DEFAULT NULL COMMENT '品牌名称',
  `product_url` varchar(255) DEFAULT NULL COMMENT '产品url',
  `product_price` varchar(255) DEFAULT NULL COMMENT '产品价格',
  `status` tinyint(1) DEFAULT '0' COMMENT '0-正确；1-错误',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;