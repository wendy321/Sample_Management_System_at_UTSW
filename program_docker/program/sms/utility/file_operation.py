#!/usr/bin/python

""" Using the with statement
	1) provides much cleaner syntax and exceptions handling &
	2) any files opened will be closed automatically after you are done
"""
def write_file(data, filename):
	with open(filename, 'wb') as f:
		f.write(data)

def read_file(filename):
    with open(filename, 'rb') as f:
        fileobj = f.read()
    return fileobj