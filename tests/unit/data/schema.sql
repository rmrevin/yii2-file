DROP TABLE IF EXISTS "file";

CREATE TABLE "file" (
  id INTEGER NOT NULL,
  mime varchar(255) NOT NULL,
  size bigint(11) NOT NULL DEFAULT 0,
  name varchar(255) NOT NULL,
  origin_name varchar(255) NOT NULL,
  sha1 varchar(40) NOT NULL,
  image_bad tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
);