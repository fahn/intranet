buildEnv:
	make down
	echo "build"
	docker build --tag intranet_dev .

runEnv:
	make down
	docker-compose -f docker-compose.yml -f docker-compose.env.yml up -d


down:
	docker-compose down --remove-orphans

