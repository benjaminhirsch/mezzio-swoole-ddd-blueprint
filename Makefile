.PHONY: phpstan
phpstan:
	docker-compose exec app vendor/bin/phpstan
