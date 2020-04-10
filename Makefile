.PHONY: build
build:
	make down
	make clean
	echo "build"
	docker build --tag intranet_dev --compress .

.PHONY: run
run:
	make down
	CURRENT_UID=$(id -u):$(id -g) docker-compose -f docker-compose.yml -f docker-compose.env.yml up -d


down:
	docker-compose down --remove-orphans

clean:
	sudo rm -rf mysql/_data


sniff:
	sudo phpcs --color .