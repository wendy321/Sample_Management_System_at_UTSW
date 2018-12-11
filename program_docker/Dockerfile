# Download base image ubuntu latest version
FROM ubuntu:latest

# Record dockerfile maintainer
MAINTAINER Shin-Yi Lin "shinyi.lin@utsouthwestern.edu"

# Get proxy and remoter environment parameters at build time
ARG http_proxy
ARG https_proxy
ARG proxy_user
ARG proxy_pass
ARG remoter_path
ARG remoter_host
ARG remoter_usr
ARG remoter_passwd
ARG remoter_db

# Define the ENV variable
ENV http_proxy $http_proxy
ENV https_proxy $https_proxy
ENV proxy_user $proxy_user
ENV proxy_pass $proxy_pass
ENV remoter_path $remoter_path
ENV remoter_host $remoter_host
ENV remoter_usr $remoter_usr
ENV remoter_passwd $remoter_passwd
ENV remoter_db $remoter_db

# Update ubuntu software repository
RUN apt-get update

# Install Python2.7, pip, perl, etc. from ubuntu repository
RUN apt-get -y install python2.7 python-pip perlbrew libdbd-mysql-perl libdbi-perl libproc-processtable-perl wget vim gzip
RUN pip install --upgrade pip

# Install python package, openpyxl
RUN pip install openpyxl mysql-connector-python

# Set default container work directory 
WORKDIR /home

# Copy a file from local to container
RUN mkdir /home/program
ADD program /home/program
VOLUME ["/home/program"]

# Set the shared files or directories with local
RUN mkdir /home/shared
VOLUME ["/home/shared"]

# Expose port of the container
EXPOSE 5000

CMD ["echo","Image created"]
