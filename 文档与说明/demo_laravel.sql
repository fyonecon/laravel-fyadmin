/*
Navicat MySQL Data Transfer

Source Server         : vm-128
Source Server Version : 50726
Source Host           : 192.168.186.129:3306
Source Database       : demo_laravel

Target Server Type    : MYSQL
Target Server Version : 50726
File Encoding         : 65001

Date: 2019-09-26 10:45:31
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for fy_admin_user
-- ----------------------------
DROP TABLE IF EXISTS `fy_admin_user`;
CREATE TABLE `fy_admin_user` (
  `user_id` int(20) NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `user_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户名',
  `user_level` int(5) NOT NULL DEFAULT '2' COMMENT '用户管理员等级，1超级，2普通，3仅查看，0冻结或不可用',
  `create_time` char(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `update_time` char(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_state` int(5) NOT NULL DEFAULT '1' COMMENT '用户状态，1正常，0冻结，2删除',
  `user_remark` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT '无' COMMENT '备注',
  `user_login_pwd` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户登录密码',
  `user_login_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户登录名，英文和字母',
  `user_token1` varchar(2000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_token2` varchar(2000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_token3` varchar(2000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_user_id` int(20) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `user_id` (`user_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of fy_admin_user
-- ----------------------------
INSERT INTO `fy_admin_user` VALUES ('6', '超级账号-测试1', '1', '20190803143203', null, '1', '这是超级管理员', '5290710376e12f6f94a4c36c761ad282', 'superuser', 'CDqMmJkBuarbnij6jatCiDqMmJmBjOs6iUnVhimCqDrMuJoBq0mCsDtMoJlBkDm', 'CDqMmJkBuarbnij6jat8iiq5mUm8jksCiDnMhJmBqrrkusotqJmCsDtMoJlBkDm', 'CDqMmJkBuarbnij6jatCiDqMmJmBjOs6iUnZhNmCqDrMuJoBqrmkssttoJlCkDmMlJwBhDq', '1');
INSERT INTO `fy_admin_user` VALUES ('7', '测试普通管理员', '2', '20190824135853', null, '1', '测试普通管理员', 'a57d7cd25d9590b60bb7d69efa0beb38', 'test2222', 'EFyOsLjDvcsdskx7mCmhstlmwumvlLm7styEnkxyvCx2ytpEtkmylCo8objEpFsOvLrDtFyttCrxujyBwyszqMmxkjpBvytzvkyGjOxxszjauCxiwFynoMpxvjvByyyzmZn0mtvEykqynCnijZyvssxulEwstBwxrzrErFsOqLlDmtnHyBpjmvl', null, null, '6');
