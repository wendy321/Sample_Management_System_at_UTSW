#!/usr/bin/python

from mysql_operation import read_one_data_from_db
from mysql_dbconfig import read_db_config

""" Generate the patient id 
:param is_fake_patient_id: whether the generated patient_id will be true or fake/unlinked patient_id
:return the generated patient_id
(1) The true patient_id is the id that collaborator will link their sample to.
True Patient ID Format: (1 character + 6-digit integers), e.g. A000000-Z999999
(2) The fake/unlinked patient_id is the id that collaborator has not linked their sample to.
Fake/unlinked Patient ID Format: (7 characters), e.g. AAAAAAA-ZZZZZZZ
"""
def generatePatientID(is_unlinked_patient_id):
	pat_id_len = 7
	max_pat_id = 999999
	query = ('SELECT MAX(Patient_ID) FROM Patient WHERE Patient_ID REGEXP "^[A-Z]{%s}$"' %(pat_id_len)) \
			if (is_unlinked_patient_id) \
			else ('SELECT MAX(Patient_ID) FROM Patient WHERE Patient_ID REGEXP "^[A-Z]{1}[0-9]{%s}$"' %(pat_id_len-1))
	db_config = read_db_config(section = 'mysqlSMS')
	last_patient_id = read_one_data_from_db(db_config, query, None)
	
	new_patient_id = None
	if(last_patient_id == None):
		new_patient_id = ('A'*pat_id_len) if (is_unlinked_patient_id) else ("A"+"0"*(pat_id_len-1))
	else:
		postfix_pat_id = ""
		carry = True
		if(is_unlinked_patient_id):
			for i in reversed(range(pat_id_len)):
				if(carry == False):
					break
				char = last_patient_id[i]
				if(char == "Z"):
					char = "A"
				else:
					carry = False
					char = chr((ord(char)+1))
				postfix_pat_id = char + postfix_pat_id
			new_patient_id= last_patient_id[0:pat_id_len - len(postfix_pat_id)] + postfix_pat_id
		else: 
			res_chars = last_patient_id[1:pat_id_len]
			res_chars_len = len(res_chars)
			for i in reversed(range(res_chars_len)):
				if(carry == False):
					break
				char = res_chars[i]
				if(char == "9"):
					char = "0"
				else:
					carry = False
					char = str(int(char) + 1)
				postfix_pat_id = char + postfix_pat_id
			new_patient_id= res_chars[0:res_chars_len - len(postfix_pat_id)] + postfix_pat_id

			first_char = last_patient_id[0]
			if(carry == True):			
				if(first_char == "Z"):
					first_char = "A"
				else:
					first_char = chr(ord(first_char) + 1)
			new_patient_id = first_char + new_patient_id		
	return new_patient_id

""" Generate the sample id 
Sample ID Format: 
	7-characters patient_id + 2-digit pathological_status code + 2-digit sample_class code + 2-digit auto-increment
"""
def generateSampleID(patient_id, pathological_status, sample_class):
	args = (patient_id, pathological_status, sample_class)
	query = 'SELECT MAX(Sample_ID) FROM Sample WHERE Patient_ID = %i AND Pathological_Status = %i AND Sample_Class = %i'
	db_config = read_db_config(section = 'mysqlSMS')
	last_sample_id = read_one_data_from_db(db_config, query, args)

	new_sample_id = None
	pathological_status_str = str(pathological_status)
	sample_class_str = str(sample_class)
	if(len(pathological_status_str)==1):
		pathological_status_str = "0" + pathological_status_str
	if(len(sample_class_str)==1):
		sample_class_str = "0" + sample_class_str
	if(last_sample_id == None):
		new_sample_id = patient_id + pathological_status_str + sample_class_str + "00"
	else:
		last_2_digit_int = int(last_sample_id[11:2])
		if(last_2_digit_int == 99):
			last_2_digit_int = 0
		else:
			last_2_digit_int += 1
		last_2_digit_str = str(last_2_digit_int)
		if(len(last_2_digit_str) == 1):
			last_2_digit_str = "0" + last_2_digit_str
		new_sample_id = patient_id + pathological_status_str + sample_class_str + last_2_digit_str

	return new_sample_id