FROM ubuntu

WORKDIR /opt/weatherali
COPY . .

RUN apt update 
RUN apt install -y "make" "python3" "python3-pip" "php" "wget" "sqlite3"
RUN make all

CMD [ "make run" ]