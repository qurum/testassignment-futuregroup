docker-compose -f docker-compose.test.yml up -d --remove-orphans
docker exec testassignment_php_fpm_test chown -R www-data:www-data /database
docker exec testassignment_php_fpm_test php /app/vendor/bin/phinx migrate -e development
docker exec testassignment_php_fpm_test php /app/vendor/bin/codecept run --steps Unit
docker exec testassignment_php_fpm_test php /app/vendor/bin/codecept run --steps Api
docker-compose down
echo "---------"
echo "All done."
