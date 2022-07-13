phpstan:
	docker-compose exec app vendor/bin/phpstan
cscheck:
	docker-compose exec app composer cs-check
csfix:
	docker-compose exec app composer cs-fix