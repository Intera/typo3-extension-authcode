#
# Table structure for table 'tx_authcode_domain_model_authcode'
#
CREATE TABLE tx_authcode_domain_model_authcode (
	uid int(11) unsigned NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,

	action varchar(255) DEFAULT '' NOT NULL,
	auth_code varchar(255) DEFAULT '' NOT NULL,
	identifier varchar(255) DEFAULT '' NOT NULL,
	identifier_context varchar(255) DEFAULT '' NOT NULL,
	reference_table varchar(255) DEFAULT '' NOT NULL,
	reference_table_hidden_field varchar(255) DEFAULT '' NOT NULL,
	reference_table_hidden_field_must_be_true tinyint(4) DEFAULT '0' NOT NULL,
	reference_table_uid int(11) unsigned DEFAULT '0' NOT NULL,
	reference_table_uid_field varchar(255) DEFAULT '' NOT NULL,
	serialized_auth_data text,
	type varchar(255) DEFAULT '' NOT NULL,
	valid_until int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid),
	UNIQUE KEY auth_code (auth_code)
);
