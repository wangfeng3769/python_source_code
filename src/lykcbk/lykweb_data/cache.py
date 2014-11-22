# -*- coding: utf-8 -*-
from datetime import datetime
import json
from django.core.cache import cache
import time
from cache_timeout import cachetimeout, usercache
from webtool import atom_att

def __lastmodifiedBycache(request):
    sid = atom_att(request, 'sid')
    if usercache(request) and sid: # 用户缓存
        return cache.get(''.join([request.get_full_path(), sid, '_lastmodified']))
    else:
        return cache.get(''.join([request.get_full_path(), '_lastmodified']))

def __lastmodified(request):
    timeout = cachetimeout(request)
    return datetime(*time.localtime(time.time() + timeout)[0:6])

def remove_lastmodified(request):
    cache.delete(''.join([request.get_full_path(), '_lastmodified']))

def set_lastmodified_cache(request, lastmodifiedtime):
    timeout = cachetimeout(request)
    sid = atom_att(request, 'sid')
    if usercache(request) and sid: # 用户缓存
        cache.set(''.join([request.get_full_path(), sid, '_lastmodified']), lastmodifiedtime, timeout)
    else:
        cache.set(''.join([request.get_full_path(), '_lastmodified']), lastmodifiedtime, timeout)

def lastmodified(request, *args):
    lastmodifiedtime = __lastmodifiedBycache(request)
    if lastmodifiedtime:
        return lastmodifiedtime
    lastmodifiedtime = __lastmodified(request)
    set_lastmodified_cache(request, lastmodifiedtime)
    return lastmodifiedtime
#    return datetime(*time.gmtime()[0:6])

def set_cache(request, result):
    timeout = cachetimeout(request)
    sid = atom_att(request, 'sid')
    if usercache(request) and sid: # 用户缓存
        cache.set(''.join([request.get_full_path(), sid]), result, timeout)
    else:
        cache.set(request.get_full_path(), result, timeout)

def get_cache(request):
    sid = atom_att(request, 'sid')
    if usercache(request) and sid: # 用户缓存
        return cache.get(''.join([request.get_full_path(), sid]))
    else:
        return cache.get(request.get_full_path())

def wrap_cache():
    def wrap(func):
        def wrapped_func(request, *args):
            result = get_cache(request)
            if result:
                return result
            result = func(request, *args)
            set_cache(request, result)
            return result
        return wrapped_func
    return wrap

def wrap_cache_key():
    def wrap(func):
        def wrapped_func(key, timeout, *args):
            result = cache.get(key)
            if result:
                return json.loads(result)
            result = func(key, timeout, *args)
            cache.set(key, json.dumps(result), timeout)
            return result
        return wrapped_func
    return wrap

def del_cacheBykey(key):
    cache.delete(key)