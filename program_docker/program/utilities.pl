################################################################################
## Common functions for RemoteR
## Should be included in runremoter.pl and all other pm files
## Author: Shin-yi Lin, Danni Luo, Bo Yao
## Date: 09/18/2017

## Caution: Any change history of script should be recorded here.
################################################################################


#! /usr/bin/perl -w

use warnings;
use DBI;
use strict;
use Proc::ProcessTable;


# get word directory
my $path = $ENV{'remoter_path'};
# get db config
#my $db_host = $ENV{'remoter_host'};
#my $db_username = $ENV{'remoter_usr'};
#my $db_password = $ENV{'remoter_passwd'};
#my $db_dbname = $ENV{'remoter_db'};

my $db_host = '129.112.73.229';
my $db_username = 'wendy';
my $db_password = 'Dianalin';
my $db_dbname = 'RemoteR';


# check whether the number of R script processes running reaches the maximum number
sub ismaxproccess
{
    my($maxprocess) = @_;

    # if maximum number is set as 0 or not set, ignore the limitation of R script processes in memory
    if($maxprocess == 0 || $maxprocess eq "")
    {
	return 0;
    }

    my $t = new Proc::ProcessTable;
    my $cnt=0;
    foreach my $p ( @{$t->table} ){
        if (index($p->cmndline, ".R") != -1) {
            $cnt++;
        }
    }

    # If 2 R scripts are running, .
    # 4 = 2 + 2 (2 parents and 2 children processes)
    if($cnt >= 2 * $maxprocess){
       return 1;
    }else{
        return 0;
    }
}

# Change the job/process running status in database
sub changestatus
{
    my($jobid,$status)=@_;

    # connect to database
    my $dbh = DBI->connect('DBI:mysql:' . $db_dbname . ';host=' . $db_host, $db_username, $db_password)
			or die "Can't connect: " . DBI->errstr();

    my $sth = $dbh->prepare("UPDATE Jobs SET Status=".$status." WHERE JobID='".$jobid."'")
    			or die("Prepare of SQL failed" . $dbh->errstr());
    $sth->execute();
    $sth->finish();
    $dbh->disconnect();
}

# Remove whitespace from the start and end of the string
sub trim($)
{
	my $string = shift;
	$string =~ s/^\s+//;
	$string =~ s/\s+$//;
	return $string;
}
1;
