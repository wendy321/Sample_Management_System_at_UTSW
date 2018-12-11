#!/usr/bin/python

from mysql.connector import MySQLConnection, Error
from mysql_dbconfig import read_db_config

""" Read one data from DB
:param db_config: db configuration dictionary
:param query: SQL query statement
:param args: input arguments in SQL query statement
:return: a data from single record
"""
def read_one_data_from_db(db_config, query, args):
	data = None
	try:
		conn = MySQLConnection(**db_config)
		cursor = conn.cursor()
		cursor.execute(query, args)
		data = cursor.fetchone()[0]
	except Error as error: 
		print(error)
	finally:
		cursor.close()
		conn.close()
		return data

""" Read one row from DB
:param db_config: db configuration dictionary
:param query: SQL query statement
:param args: input arguments in SQL query statement
:return: a data from single record
"""
def read_one_row_from_db(db_config, query, args):
	row = None
	try:
		conn = MySQLConnection(**db_config)
		cursor = conn.cursor()
		cursor.execute(query, args)
		row = cursor.fetchone()
	except Error as error: 
		print(error)
	finally:
		cursor.close()
		conn.close()
		return row

""" Read Blob from SMSParameters Table in RemoteR DB
:param job_id: job id in SMSParameters Table
:param account_id: user account id in SMS web application
:param data_type: batch upload data type, either sample or patient
:return: the read blob object
"""
def read_excel_blob_from_remoter_db(job_id, tag, data_type):
	args = (job_id, tag, data_type)
	query = 'SELECT XLSXFile FROM SMSParameters AS P INNER JOIN Jobs AS J ON P.JobID = J.JobID ' \
	'WHERE P.XLSXFile is NOT NULL AND P.JobID = %s AND P.Tag = %s AND P.DataType = %s AND J.Status = 2 ORDER BY J.CreateTime DESC LIMIT 1'
	db_config = read_db_config(section='mysqlRemoteR')
	print(db_config)
	return read_one_data_from_db(db_config, query, args)

""" Read the last sample id with specified pathological status and sample class from Sample Table in SMS DB
:param pathological_status: pathological status of the import sample
:param sample_class: sample class of the import sample
:return: the last sample id
"""
def read_last_sampleid_from_sms_db(pathological_status, sample_class):
	args = (pathological_status, sample_class)
	query = 'SELECT Sample_ID FROM Sample WHERE Sample_ID IS NOT NULL AND Pathological_Status = %s AND Sample_Class = %s'
	db_config = read_db_config(section='mysqlSMS')
	return read_one_data_from_db(db_config, query, args)

""" Read the Sample UUID from Sample Table in SMS DB
:param local_sample_id: sample id from local lab or institute
:return: the system generated sample uuid
"""
def read_uuid_from_sms_db_by_local_sample_id(local_sample_id):
	args = (local_sample_id,)
	query = 'SELECT UUID FROM Sample WHERE UUID IS NOT NULL AND Local_Sample_ID = %s AND isDelete = 0'
	db_config = read_db_config(section='mysqlSMS')
	return read_one_data_from_db(db_config, query, args)

""" Read the Sample UUID from EnrollStudy Table in SMS DB
:param study_id: study or source id 
:param study_sample_id: study or source sample id 
:return: the system generated sample uuid
"""
def read_uuid_from_sms_db_by_study_sample_id(study_id,study_sample_id):
	args = (study_id,study_sample_id)
	query = 'SELECT Sample_UUID FROM EnrollStudy WHERE Sample_UUID IS NOT NULL AND Study_ID = %s AND Within_Study_Sample_ID = %s AND isDelete = 0'
	db_config = read_db_config(section='mysqlSMS')
	return read_one_data_from_db(db_config, query, args)

""" Read the Sample record from Sample Table in SMS DB
:param local_sample_id: sample id from local lab or institute
:return: the sample record
"""
def read_sample_from_sms_db_by_local_sample_id(local_sample_id):
	args = (local_sample_id,)
	query = 'SELECT * FROM Sample WHERE Local_Sample_ID = %s And isDelete = 0'
	db_config = read_db_config(section='mysqlSMS')
	return read_one_row_from_db(db_config, query, args)

""" Read the Sample record from EnrollStudy Table in SMS DB
:param study_id: study or source id 
:param study_sample_id: study or source sample id 
:return: the enroll study record
"""
def read_sample_from_sms_db_by_study_sample_id(study_id,study_sample_id):
	args = (study_id,study_sample_id)
	query = 'SELECT * FROM EnrollStudy WHERE Study_ID = %s And Within_Study_Sample_ID = %s AND isDelete = 0'
	db_config = read_db_config(section='mysqlSMS')
	return read_one_row_from_db(db_config, query, args)

""" Read the Patient ID from Patient Table in SMS DB
:param local_patient_id: patient id from local lab or institute
:return: the system generated patient id
"""
def read_patient_id_from_sms_db_by_local_patient_id(local_patient_id):
	args = (local_patient_id,)
	query = 'SELECT Patient_ID FROM Patient WHERE Local_Patient_ID = %s'
	db_config = read_db_config(section='mysqlSMS')
	return read_one_data_from_db(db_config, query, args)

""" Read the Patient ID from EnrollStudy Table in SMS DB
:param study_id: study or source id 
:param study_patient_id: study or source patient id 
:return: the system generated patient id
"""
def read_patient_id_from_sms_db_by_study_patient_id(study_id,study_patient_id):
	args = (study_id,study_patient_id)
	query = 'SELECT Patient_ID FROM EnrollStudy WHERE Study_ID = %s And Within_Study_Patient_ID = %s AND isDelete = 0'
	db_config = read_db_config(section='mysqlSMS')
	return read_one_data_from_db(db_config, query, args)

""" Read the Patient record from Patient Table in SMS DB
:param local_patient_id: patient id from local lab or institute
:return: the system generated patient id
"""
def read_patient_from_sms_db_by_local_patient_id(local_patient_id):
	args = (local_patient_id,)
	query = 'SELECT * FROM Patient WHERE Local_Patient_ID = %s'
	db_config = read_db_config(section='mysqlSMS')
	return read_one_row_from_db(db_config, query, args)

""" Read the Patient record from EnrollStudy Table in SMS DB
:param study_id: study or source id 
:param study_patient_id: study or source patient id 
:return: the system generated patient id
"""
def read_patient_from_sms_db_by_study_patient_id(study_id,study_patient_id):
	args = (study_id,study_patient_id)
	query = 'SELECT * FROM EnrollStudy WHERE Study_ID = %s And Within_Study_Patient_ID = %s AND isDelete = 0'
	db_config = read_db_config(section='mysqlSMS')
	return read_one_row_from_db(db_config, query, args)

""" Insert one record into DB
:param db_config: db configuration dictionary
:param query: SQL query statement
:param args: input arguments in SQL query statement
:return: the last insert id or None if not found
"""
def insert_one_data_into_db(db_config, query, args):
	last_row_id = None
	try:
		conn = MySQLConnection(**db_config)
		cursor = conn.cursor()
		cursor.execute(query, args)
		# only AUTO_INCREMENT id can be retrieved by cursor.lastrowid, when UPDATE or INSERT
		# if lastrowid is not found, cursor.lastrowid = 0
		last_row_id = cursor.lastrowid	
		conn.commit()
	except Error as error:
		print(error)
	finally:
		cursor.close()
		conn.close()
		return last_row_id

""" Insert one sample record into Sample Table in SMS DB
:param one_sample_data: a list of the sample variables
:return: last inserted sample id (success) or false (fail)
"""
def insert_sample_into_sms_db(one_sample_data):
	args = (one_sample_data)
	# if Parent_UUID is NULL
	query = 'INSERT INTO Sample(UUID, Sample_ID, Local_Sample_ID, Patient_ID, Sample_Contributor_Consortium_ID, ' \
			'Sample_Contributor_Institute_ID, Procedure_Type, Date_Procedure, Parent_UUID, Date_Derive_From_Parent, ' \
            'Pathological_Status, Sample_Class, Sample_Type, Storage_Room, Cabinet_Type, Cabinet_Temperature, ' \
            'Cabinet_Number, Shelf_Number, Rack_Number, Box_Number, Position_Number, Quantity_Value, Quantity_Unit, ' \
            'Concentration_Value, Concentration_Unit, Specimen_Type, Nucleotide_Size_Group_200, Anatomical_Site, ' \
            'Anatomical_Laterality, Notes, CreateTime)' \
			'VALUES(%(UUID)s,%(Sample_ID)s,%(Local_Sample_ID)s,%(Patient_ID)s,%(Sample_Contributor_Consortium_ID)s, ' \
				'%(Sample_Contributor_Institute_ID)s,%(Procedure_Type)s,%(Date_Procedure)s,NULL,%(Date_Derive_From_Parent)s, ' \
				'%(Pathological_Status)s,%(Sample_Class)s,%(Sample_Type)s,%(Storage_Room)s,%(Cabinet_Type)s,%(Cabinet_Temperature)s, ' \
				'%(Cabinet_Number)s,%(Shelf_Number)s,%(Rack_Number)s,%(Box_Number)s,%(Position_Number)s,%(Quantity_Value)s,%(Quantity_Unit)s, ' \
				'%(Concentration_Value)s,%(Concentration_Unit)s,%(Specimen_Type)s,%(Nucleotide_Size_Group_200)s,%(Anatomical_Site)s,' \
				'%(Anatomical_Laterality)s,%(Notes)s,now())'
	db_config = read_db_config(section='mysqlSMS')
	insert_one_data_into_db(db_config, query, args)

# """ Insert one patient record into Patient Table in SMS DB
# :param one_patient_data: a list of the patient variables
# :return: last inserted patient id (success) or false (fail)
# """
# def insert_patient_into_sms_db(one_patient_data):
# 	args = one_patient_data
# 	query = 'INSERT INTO Patient(Patient_ID, Local_Patient_ID, Data_Contributor_Clinical_Trial_Group, Data_Contributor_Center, ' \
# 			'Age_At_Enrollment_In_Days, Age_At_First_Visit_In_Days, Relapsed_At_Enrollment, Relapsed_At_First_Visit, ' \
# 			'Age_At_Diagnosis_In_Days, Year_Of_Diagnosis, Dysgenetic_Gonad, Sex, Race, Ethnic, Vital_Status, ' \
# 			'COG_Stage, FIGO_Stage, AJCC_Stage, IGCCCG_RiskGroup, Note, CreateTime)' \
# 			'VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s, now())'
# 	db_config = read_db_config(section='mysqlSMS')
# 	return insert_one_data_into_db(db_config, query, args)

""" Insert one patient record with patient_id and local_patient_id into Patient Table in SMS DB
:param patient_id: system automatically-generated patient_id
:param local_patient_id: local_patient_id from user input 
:return: last inserted patient id (success) or false (fail)
"""
def insert_patient_into_sms_db(patient_id, local_patient_id):
	args = (patient_id, local_patient_id)
	query = 'INSERT INTO Patient(Patient_ID, Local_Patient_ID, CreateTime) ' \
			'VALUES(%s,%s, now())'
	db_config = read_db_config(section='mysqlSMS')
	insert_one_data_into_db(db_config, query, args)

""" Insert one enroll study record into EnrollStudy Table in SMS DB
:param enroll_study_data: a list of one enroll study fields and values
:return: last inserted enroll study id (success) or false (fail)
"""
def insert_enrollstudy_into_sms_db(enroll_study_data):
	args = (enroll_study_data)
	query = 'INSERT INTO EnrollStudy(Study_ID, Patient_ID, Within_Study_Patient_ID, ' \
			'Sample_UUID, Within_Study_Sample_ID, CreateTime) ' \
			'VALUES(%(Study_ID)s,%(Patient_ID)s,%(Within_Study_Patient_ID)s, ' \
				'%(Sample_UUID)s,%(Within_Study_Sample_ID)s,now())'
	db_config = read_db_config(section='mysqlSMS')
	return insert_one_data_into_db(db_config, query, args)

""" Insert success result records into SMSSuccessResults Table in RemoteR DB
:param job_id: job_id of RemoteR jobs
:param last_sample_uuid: the new inserted sample uuid
:param last_patient_id: the new inserted patient id
:param last_enroll_study_id: the new inserted enroll study id
:return: last inserted successful result ID
"""
def insert_success_result_into_remoteR_db(job_id, sample_uuid, is_new_patient, patient_id, is_new_enroll_study, enroll_study_id):
	if(is_new_patient == False):
		patient_id = "NULL"
	if(is_new_enroll_study == False):
		enroll_study_id = "NULL"
	args = (job_id, sample_uuid, patient_id, enroll_study_id)
	query = 'INSERT INTO SMSSuccessResults(JobID, NewSampleUUID, NewPatientID, NewEnrollStudyID) ' \
			'VALUES(%s,%s,%s,%s)'
	db_config = read_db_config(section='mysqlRemoteR')
	insert_one_data_into_db(db_config, query, args)

""" Insert error result record into SMSErrorResults Table in RemoteR DB
:param
:return: last inserted jobid in SMSErrorResults Table
"""
def insert_error_result_into_remoteR_db(job_id, msg):
	args = (job_id, msg)
	query = 'INSERT INTO SMSErrorResults(JobID, ErrorMsg) ' \
			'VALUES(%s,%s)'
	db_config = read_db_config(section='mysqlRemoteR')
	insert_one_data_into_db(db_config, query, args)

""" Insert warn result record into SMSWarnResults Table in RemoteR DB
:param
:return: last inserted jobid in SMSWarnResults Table
"""
def	insert_warn_result_into_remoteR_db(job_id, msg, existed_sam_uuids):
	args = (job_id, msg, existed_sam_uuids)
	query = 'INSERT INTO SMSWarnResults(JobID, WarnMsg, ExistedSampleUUIDs) ' \
			'VALUES(%s,%s,%s)'
	db_config = read_db_config(section='mysqlRemoteR')
	insert_one_data_into_db(db_config, query, args)

""" Update one record in DB
:param db_config: db configuration dictionary
:param query: SQL query statement
:param args: input arguments in SQL query statement
:return:
"""
def update_data_in_db(db_config, query, args):
	isSuccess = True
	try:
		conn = MySQLConnection(**db_config)
		cursor = conn.cursor()
		cursor.execute(query, args)
		conn.commit()
	except Error as error:
		print(error)
		isSuccess = False
	finally:
		cursor.close()
		conn.close()
		return isSuccess

""" Update local sample id by sample uuid in Sample Table in SMS DB
:param :
:return: 
"""
def update_local_sample_id_by_uuid_in_sms_db(local_sample_id, sample_uuid):
	args = (local_sample_id, sample_uuid)
	query = 'Update Sample SET Local_Sample_ID = %s WHERE UUID = %s'
	db_config = read_db_config(section='mysqlSMS')
	return update_data_in_db(db_config, query, args)

""" Update local patient id by patient id in Patient Table in SMS DB
:param :
:return: 
"""
def update_local_patient_id_by_patid_in_sms_db(local_patient_id, patient_id):
	args = (local_patient_id, patient_id)
	query = 'Update Patient SET Local_Patient_ID = %s WHERE Patient_ID = %s'
	db_config = read_db_config(section='mysqlSMS')
	return update_data_in_db(db_config, query, args)

""" Update enroll study by ((source_sample_id OR source_patient_id) AND (source_id)) in Sample Table in SMS DB
:param :
:return: 
"""
def update_enrollstudy_into_sms_db(enroll_study_data, by_variable):
	args = (enroll_study_data, by_variable)
	query = 'Update EnrollStudy SET Patient_ID = %(Patient_ID)s, Within_Study_Patient_ID = %(Within_Study_Patient_ID)s,' \
	' Sample_UUID = %(Sample_UUID)s, Within_Study_Sample_ID = %(Within_Study_Sample_ID)s WHERE '+by_variable+' = %('+by_variable+')s AND Study_ID = %(Study_ID)s'
	db_config = read_db_config(section='mysqlSMS')
	return update_data_in_db(db_config, query, args)


