#
# Table structure for table `xnponlinesimulation_item_detail`
#

CREATE TABLE `xnponlinesimulation_item_detail` (
  `onlinesimulation_id` int(10) unsigned NOT NULL,
  `simulator_name` varchar(255) NOT NULL,
  `simulator_version` varchar(255) NOT NULL,
  `model_contents_url` varchar(255) NOT NULL,
  `model_site_name` varchar(255) NOT NULL,
  `download_url` varchar(255) NOT NULL,
  `vm_type` varchar(32) NOT NULL,
  `contents_count` int(10) unsigned NOT NULL default '0',
  `model_contents_count` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY (`onlinesimulation_id`)
) ENGINE = InnoDB;
