CREATE TABLE mootwit (
	id		BIGINT UNSIGNED,
	text		VARCHAR(240),
	created_at	DATETIME,
	PRIMARY KEY (id)
);

CREATE TABLE moourls (
	id		BIGINT UNSIGNED NOT NULL auto_increment,
	tweetid		BIGINT UNSIGNED,
	url		VARCHAR(320),
	short		VARCHAR(140),
	PRIMARY KEY (id)
)
