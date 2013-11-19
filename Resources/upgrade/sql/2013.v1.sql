ALTER TABLE  `cms_template_block` ADD  `is_repeatable` TINYINT NOT NULL DEFAULT  '0' AFTER  `is_desktop`;

ALTER TABLE  `cms_page` CHANGE  `target_url`  `target_url` VARCHAR( 6 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT  '_self';

ALTER TABLE  `cms_page` CHANGE  `status`  `status` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';

ALTER TABLE  `cms_page_template_block_version` CHANGE  `status`  `status` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'draft';

ALTER TABLE  `user` ADD  `wysiwyg` TINYINT NOT NULL DEFAULT  '1' AFTER  `locale`;

ALTER TABLE  `cms_route` ADD  `title` VARCHAR( 250 ) NOT NULL DEFAULT  '' AFTER  `locale`;