# -*- coding: utf-8 -*-
import glob
import json
import random

def writefile(url, mode, obj):
    try:
        file = open(url, mode)
        file.write(obj)
    except IOError:
        pass

def readfile(url, mode):
    file = None
    try:
        file = open(url, mode)
    except IOError:
        pass
    return file

def readfilelist(url):
    files = []
    try:
        files = glob.glob(url)
    except IOError:
        pass
    return files

def random36Str(scale):
    result = []
    for i in range(scale):
        result.append(random.choice('1234567890abcdefghijklmnopqrstuvwxyz'))
    return ''.join(result)

def random26Str(scale):
    result = []
    for i in range(scale):
        result.append(random.choice('abcdefghijklmnopqrstuvwxyz'))
    return ''.join(result)

def random10Str(scale):
    result = []
    for i in range(scale):
        result.append(random.choice('1234567890'))
    return ''.join(result)

def __atominfo(atomStr):
    j = None
    if atomStr:
        if atomStr.find('{') > -1:
            atomStr = atomStr[atomStr.find('{'):]
            try:
                j = json.loads(atomStr)
            except Exception:
                pass
    return j

def atominfo(request):
#    atomStr = request.POST.get('atominfo')
    atomStr = request.META.get('HTTP_USER_AGENT')
    return __atominfo(atomStr)

def atom_att(request, key):
    atom = atominfo(request)
    if atom:
        try:
            att = atom[key]
            return att
        except Exception:
            pass

if __name__ == '__main__':
    print 1
    print random26Str(6)