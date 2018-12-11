#!/usr/bin/python
import os
from ConfigParser import ConfigParser

""" Read database configuration file and return a dictionary object
:param filename: name of the configuration file
:param section: section of database configuration
:return: a dictionary of database parameters
"""
def read_db_config(section, filename='config.ini'):

	parser = ConfigParser()
	parser.read(os.path.join(os.path.abspath(os.path.dirname(__file__)), 'config', filename))
	db = {}
	if parser.has_section(section):
		items = parser.items(section)
		for item in items:
			db[item[0]] = item[1]
	else:
		raise Exception('{0} not found in the {1} file'.format(section, filename))
	return db
