#!/usr/bin/env python

import re
import sys
import os
import string


print os.getcwd()


def cur_dir():
	path=sys.path[0]
        print path
        if os.path.isdir(path):
             return path
        elif os.path.isfile(path):
             return os.path.dirname(path)

def get_test_cfg(cfg_index):
        cfg_file = cur_dir() +"/ip_assign.conf"
        try:
            ret_cfg = "NO_CFG"
            fh = open(cfg_file)

            for line in fh.readlines():
                print line
                print cfg_index
                print string.split(line)[0]
                if cfg_index == string.split(line)[0:-1] :
                       ret_Temp = string.split(line)[1]
                       ret_cfg = ret_Temp.replace("/@20/"," ")
                       return ret_cfg
                       break

            fh.close()
            return ret_cfg
	except IOError:
             print "Open file error, can't find file: %s" % cfg_file
             return "NO_CFG"

print cur_dir()+"/ip_assign.conf"
print get_test_cfg("ETH0_IP:")

