# build a docker image from dockerfile
sudo docker build --network="host" --build-arg http_proxy=http://proxy.swmed.edu:3128 --build-arg https_proxy=http://proxy.swmed.edu:3128 --build-arg proxy_user=xxx --build-arg proxy_pass=xxx --build-arg remoter_path=/home/program/ --build-arg remoter_host=${remoter_host} --build-arg remoter_usr=${remoter_usr} --build-arg remoter_passwd=${remoter_passwd} --build-arg remoter_db=${remoter_db} -t ubuntu_python_perl . 

# Inspect build history
sudo docker history ubuntu_python_perl

# list docker images
sudo docker images

# run a script directly in docker container
sudo docker run -p 5000:5000 ubuntu_python_perl:latest python ./program/sms/runsamplebatchupload.py


# run an interactive bash in a docker container (w/o volume mount)
# , if you copied program folder into the container (not save space in container)
sudo docker run -p 5000:5000 -it ubuntu_python_perl:latest /bin/bash 

# run an interactive bash in a docker container (w/i volume mount) 
# , if you didn't copy program folder into the container (save space in container)
sudo docker run -p 5000:5000 -it ubuntu_python_perl:latest /bin/bash -v /home/wendy/public_html/sample_management_system/program_docker:/mnt

# access a running container again from outside of the container
sudo docker exec -it <container names> /bin/bash

# execute command from outside of the container
sudo docker exec -it <container names> ls /home/lib_unzip/perl-5.16.0/

# list all docker containers including stopped containers, ...
sudo docker container ls -a

# list running docker containers
sudo docker ps

# stop a container
sudo docker [containter-id] stop

# remove a container
sudo docker rm [containter-id]

# remove a image
sudo docker image rm [image-id]


