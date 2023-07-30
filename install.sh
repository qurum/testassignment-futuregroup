cp ./back/.env.example ./back/.env
cp ./back/.test.env.example ./back/tests/.env
docker-compose up -d
docker exec testassignment_php_fpm chown -R www-data:www-data /database
docker exec testassignment_php_fpm composer install
docker exec testassignment_php_fpm php /app/vendor/bin/phinx migrate -e development
docker-compose down
echo "---------"
echo "All done."
