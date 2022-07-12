.PHONY:  docker-compose
phpstan:
	docker-compose exec app vendor/bin/phpstan
