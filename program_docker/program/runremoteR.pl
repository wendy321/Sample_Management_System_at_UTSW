#######################################################################################
## RemoteR framework
##  a) Check new job in db; 
## 	b) read new job parameter from db; 
## 	c) call R analysis script; 
## 	d) save calculation results in db
## Author: Bo Yao, Shin-yi Lin, Danni Luo
## Date: 09/18/2017
##
## Record change history of this script here
#######################################################################################

#! /usr/bin/perl -w

use warnings;
use DBI;
use strict;

require "utilities.pl";

my $STATUS_NEWJOB = 0;
my $STATUS_SUCCESS = 1;
my $STATUS_PROCESSING = 2;
my $STATUS_FAIL = 9;

# set the max number of Python script processes in memory
# default value: 2
my $maxprocess = 2;

# if 
if(ismaxproccess($maxprocess) == 1){
    exit;
}

# get db config
#my $db_host = $ENV{'remoter_host'};
#my $db_username = $ENV{'remoter_usr'};
#my $db_password = $ENV{'remoter_passwd'};
#my $db_dbname = $ENV{'remoter_db'};
my $db_host = 'xxx.xxx.xx.xxx';
my $db_username = 'xxx';
my $db_password = 'xxx';
my $db_dbname = 'xxx';

# connect to database
print ("connet to db\n");
my $dbh = DBI->connect('DBI:mysql:' . $db_dbname . ';host=' . $db_host, $db_username, $db_password)
	           or die "Can't connect: " . DBI->errstr();

# Get jobid which has not been dealt with
print ("sql prepare\n");
my $sth1 = $dbh->prepare("SELECT JobID, Analysis, Software FROM Jobs where Status = ". $STATUS_NEWJOB ." order by CreateTime desc limit 1")
			or die("Prepare of SQL failed" . $dbh->errstr());
print ("sql execute\n");
$sth1->execute();
my @result1 = $sth1->fetchrow_array();

$sth1->finish();
print ("db disconnect\n");
$dbh->disconnect();

# if no available job id is found in database, the perl code will stop
if($#result1 eq -1)
{
	print ("jobid is not found");
	exit;
}

my $jobid = $result1[0];
my $analysis = $result1[1];
my $software = $result1[2];

# sample batch upload - sample management system 
if($analysis eq "samplebatchupload" && $software eq "samplemanagementsystem")
{
	print ("enter sms\n");
	require "sms/sms.pl";

	sms_samplebatchupload($jobid);	
}

# patient batch  upload - sample management system 
if($analysis eq "patientbatchupload" && $software eq "samplemanagementsystem")
{
	require "sms/sms.pl";

	sms_patientbatchupload($jobid);	
}
