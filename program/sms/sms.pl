####################################################################################
## Project: Sample Management System (v1)
## Analysis: sample batch upload / patient batch upload
## Input: JobID
## Process: detect whether tmp.txt is generated by Python script, and then change Job Status
##	   a) if tmp.txt is generated, change Job Status to SUCCESS and delete tmp.txt file
##	   b) if tmp.txt is not generated after 12 sec, change Job Status to FAIL
## Output: N/A
## Author: Shinyi Lin
## Date:  10/17/2018

## Please put the update history of script here	   
######################################################################################

#! /usr/bin/perl -w

use strict;

# get word directory
my $path = $ENV{'remoter_path'}."sms/";
my $pathoutput = $ENV{'remoter_path'};
# get db config
# my $db_host = $ENV{'remoter_host'};
# my $db_username = $ENV{'remoter_usr'};
# my $db_password = $ENV{'remoter_passwd'};
# my $db_dbname = $ENV{'remoter_db'};

my $db_host = '129.112.73.229';
my $db_username = 'wendy';
my $db_password = 'Dianalin';
my $db_dbname = 'RemoteR';

my $STATUS_NEWJOB = 0;
my $STATUS_SUCCESS = 1;
my $STATUS_PROCESSING = 2;
my $STATUS_FAIL = 9;

##########################################
## sample batch upload
## input: job ID
## output: tmp_sam.txt generated by Python script and results stored in database by Python script
##########################################
sub sms_samplebatchupload
{
	print ("enter sms sample batch upload\n");
	my ($jobid) = @_;

	if($jobid eq "") {exit;}

	my $text = "";
	
	print ("change job status to 2 - processing\n");
	changestatus($jobid, $STATUS_PROCESSING);

	# system call Python script
	print ("call "."python ". $path ."runsamplebatchupload.py \"" . $jobid . "\""." \n");
	system("python ". $path ."runsamplebatchupload.py \"" . $jobid . "\"");

	# if system call Python script exist code is not 0
	if($? !=0){
		print ("call python error. change job status to 9 - fail\n");
        	changestatus($jobid, $STATUS_FAIL);
	}

	# test number
	my $testnumber = 0;
        # < 12 secs, waiting image
	while((! -e $pathoutput . "tmp_sam.txt") && $testnumber < 4)
	{
		sleep 3;
		$testnumber++;
	}	

	# > 12 sec, fail, change status to 2 (fail)
	if((! -e $pathoutput."tmp_sam.txt") && $testnumber==4){
	    changestatus($jobid, $STATUS_FAIL);
	    exit;
	}

	# read text file
	open TEXT, $pathoutput."tmp_sam.txt";
	while(my $line = <TEXT>)
	{
		chomp($line);
		$text .= $line;
	}
	close TEXT;

	if($text eq "success"){
		changestatus($jobid, $STATUS_SUCCESS);
	}else{
		changestatus($jobid, $STATUS_FAIL);
	}

	system("rm -rf ".$pathoutput."tmp_sam.txt");
}

##########################################
## patient batch upload
## input: job ID
## output: tmp_pat.txt generated by Python script and results stored in database by Python script
##########################################
sub sms_patientbatchupload
{
	my ($jobid) = @_;

	if($jobid eq "") {exit;}
	
	changestatus($jobid, $STATUS_PROCESSING);

	# system call Python script
	system("python ". $path ."runpatientbatchupload.py \"" . $jobid . "\"");

	# if system call Python script exist code is not 0
	if($? !=0){
        	changestatus($jobid, $STATUS_FAIL);
	}

	# test number
	my $testnumber = 0;
    	# < 12 secs, waiting image
	while((! -e $pathoutput . "tmp_pat.txt") && $testnumber < 4)
	{
		sleep 3;
		$testnumber++;
	}	

	# > 12 sec, fail, change status to 2 (fail)
	if((! -e $pathoutput."tmp_pat.txt") && $testnumber==4){
	    changestatus($jobid, $STATUS_FAIL);
	    exit;
	}

	# read text file
	my $text = "";
	open TEXT, $pathoutput."tmp_pat.txt";
	while(my $line = <TEXT>)
	{
		chomp($line);

		$text .= $line;
	}
	close TEXT;

	if($text eq "success"){
		changestatus($jobid, $STATUS_SUCCESS);
	}else{
		changestatus($jobid, $STATUS_FAIL);
	}

	system("rm -rf ".$pathoutput."tmp_pat.txt");
}
