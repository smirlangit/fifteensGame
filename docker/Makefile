shell:
	docker exec -it fifteen_php sh
	
start:
	docker-compose up -d
	
stop:
	docker-compose down
	
perm:
	sudo chown -R www-data:www-data ../app/storage/
	sudo chmod -R 777 ../app/storage/