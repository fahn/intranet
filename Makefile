$(warning A top-level warning)

down:
	docker-compose down --remove-orphans

clean:
	sudo rm -rf mysql/_data


# BUILD
.PHONY: build
build:
	make down
	make clean
	echo "build"
	docker build --tag intranet_dev --compress docker-build/.

# RUN
.PHONY: run
run:
	#make down
	docker-compose -f docker-compose.yml -f docker-compose.env.yml up -d
	# CURRENT_UID=$(id -u):$(id -g) 


sniff:
	-f report_phpcs && sudo rm report_phpcs
	sudo phpcs --config-set tab_width 4
	sudo phpcs --config-set show_progress 1
	sudo phpcs -vv \
	--report=full --report-file=report_phpcs -q --encoding=UTF-8 --error-severity=5 --warning-severity=5 src/.
	# --standard=/html/test/phpcs_ruleset.xml

testreport:
	./vendor/bin/phpunit --coverage-html reports/