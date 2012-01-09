cat drop_db.sql | sed 's/#PREFIX#/wp_/g' | mysql -uroot -ptoto wordpress
cat init_db.sql | sed 's/#PREFIX#/wp_/g' | mysql -uroot -ptoto wordpress
cat init_data.sql | sed 's/#PREFIX#/wp_/g' | mysql -uroot -ptoto wordpress
