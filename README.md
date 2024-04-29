# BlogWebsite

SQL Layout:

my_database
  |
  |
users --> id[pk] username email password
posts --> id[pk] user_id[fk] content created_at(DATETIME) image_path
