
TRUNCATE #PREFIX#herisson_types;

INSERT INTO #PREFIX#herisson_types (id,name) VALUES (1,'page');
INSERT INTO #PREFIX#herisson_types (id,name) VALUES (2,'image');
INSERT INTO #PREFIX#herisson_types (id,name) VALUES (3,'video');



TRUNCATE #PREFIX#herisson_screenshots;

INSERT INTO #PREFIX#herisson_screenshots (id,name,fonction) VALUES (1,'wkhtmltoimage-amd64','herisson_screenshots_wkhtmltoimage_amd64');
INSERT INTO #PREFIX#herisson_screenshots (id,name,fonction) VALUES (2,'wkhtmltoimage-i386','herisson_screenshots_wkhtmltoimage_i386');



