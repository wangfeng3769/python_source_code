#!/usr/bin/perl

use FCGI;
#perl -MCPAN -e 'install FCGI'
use Socket;

#this keeps the program alive or something after exec'ing perl scripts
END() { } BEGIN() { }
*CORE::GLOBAL::exit = sub { die "fakeexit\nrc=".shift()."\n"; }; eval q{exit}; if ($@) { exit unless $@ =~ /^fakeexit/; } ;

&main;

sub main {
                #$socket = FCGI::OpenSocket( ":3461", 10 ); #use IP sockets
                $socket = FCGI::OpenSocket( "/var/run/nagios.sock", 10 ); #use UNIX sockets - user running this script must have w access to the 'nginx' folder!!
                $request = FCGI::Request( \*STDIN, \*STDOUT, \*STDERR, \%ENV, $socket );
                if ($request) {request_loop()};
                        FCGI::CloseSocket( $socket );
}

sub request_loop {
                while( $request->Accept() >= 0 ) {

                   #processing any STDIN input from WebServer (for CGI-GET actions)
                   $env = $request->GetEnvironment();
                   $stdin_passthrough ='';
                   $req_len = 0 + $ENV{CONTENT_LENGTH};
                   if ($ENV{REQUEST_METHOD} eq 'GET'){
                                $stdin_passthrough .= $ENV{'QUERY_STRING'};
                        }

                        #running the cgi app
                        if ( (-x $ENV{SCRIPT_FILENAME}) && #can I execute this?
                                 (-s $ENV{SCRIPT_FILENAME}) && #Is this file empty?
                                 (-r $ENV{SCRIPT_FILENAME})     #can I read this file?
                        ){
                                #http://perldoc.perl.org/perlipc.html#Safe-Pipe-Opens
                open $cgi_app, '-|', $ENV{SCRIPT_FILENAME}, $stdin_passthrough or print("Content-type: text/plain\r\n\r\n"); print "Error: CGI app returned no output - Executing $ENV{SCRIPT_FILENAME} failed !\n";
                                if ($cgi_app) {print <$cgi_app>; close $cgi_app;}
                        }
                        else {
                                print("Content-type: text/plain\r\n\r\n");
                                print "Error: No such CGI app - $req_len - $ENV{CONTENT_LENGTH} - $ENV{REQUEST_METHOD} - $ENV{SCRIPT_FILENAME} may not exist or is not executable by this process.\n";
                        }

                }
}

