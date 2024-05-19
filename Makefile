DATABASE := "weatherali.db"
DATA := "data.xlsx"
IFACE := 0.0.0.0
PORT := 8080

all: getBootstrap createDatabase

getBootstrap:
	bash bootstrap.sh

createDatabase:
	python3 weatherali.py --dosya $(DATA) --yaz $(DATABASE)

run:
	php -S $(IFACE):$(PORT)

clean:
	rm -vrf $(DATABASE) yardimcilar/* $(DATABASE)

.PHONY: all getBootstrap createDatabase run