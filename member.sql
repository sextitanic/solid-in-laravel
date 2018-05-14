CREATE TABLE `member_activator` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL COMMENT '會員 ID',
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '啟用的代碼',
  `type` tinyint(1) NOT NULL COMMENT '註冊類型，1: email, 2: 手機號碼',
  `created_at` datetime DEFAULT NULL COMMENT '建立日期',
  `updated_at` datetime DEFAULT NULL COMMENT '更新日期',
  PRIMARY KEY (`id`),
  KEY `idx_member_id` (`member_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `member` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '流水號 ID',
  `reg_email` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '會員註冊 email',
  `reg_phone` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '會員註冊手機號碼',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sex` tinyint(1) DEFAULT 0 COMMENT '性別，0:未填寫, 1:男性, 2:女性, 3:跨性別',
  `type` tinyint(1) NOT NULL COMMENT '註冊類型，1: email, 2: 手機號碼',
  `reg_date` datetime DEFAULT NULL COMMENT '註冊日期時間',
  `updated_at` datetime DEFAULT NULL COMMENT '更新日期時間',
  `status` tinyint(1) DEFAULT 0 COMMENT '會員狀態，0: 未啟用, 1:啟用, 2:黑名單',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_UNIQUE` (`reg_email`),
  UNIQUE KEY `reg_phone_UNIQUE` (`reg_phone`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='會員資料表';
