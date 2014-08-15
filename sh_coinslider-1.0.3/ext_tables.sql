#
# Table structure for table 'tx_shcoinslider_images'
#
CREATE TABLE tx_shcoinslider_images (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext,
	description text,
	image text,
	link tinytext,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);