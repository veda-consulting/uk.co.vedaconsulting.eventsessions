CREATE TABLE `civicrm_price_field_dependency` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `price_set_id` int(11) unsigned NOT NULL,
  `required_pfid` int(11) unsigned NOT NULL,
  `depends_on_pfid` int(11) unsigned NOT NULL,
  `depends_on_fid` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY UI_price_field_dependency (price_set_id, required_pfid, depends_on_pfid, depends_on_fid),
  CONSTRAINT FK_civicrm_price_field_dependency_price_set_id FOREIGN KEY (`price_set_id`) REFERENCES `civicrm_price_set`(`id`) ON DELETE CASCADE,
  CONSTRAINT FK_civicrm_price_field_dependency_required_pfid FOREIGN KEY (`required_pfid`) REFERENCES `civicrm_price_field`(`id`) ON DELETE CASCADE,
  CONSTRAINT FK_civicrm_price_field_dependency_depends_on_pfid FOREIGN KEY (`depends_on_pfid`) REFERENCES `civicrm_price_field`(`id`) ON DELETE CASCADE,
  CONSTRAINT FK_civicrm_price_field_dependency_depends_on_fid FOREIGN KEY (`depends_on_fid`) REFERENCES `civicrm_price_field_value`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
