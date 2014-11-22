# -*- coding: utf-8 -*-
import re

CACHE_TIMEOUT={
    '^/lyk/v1/(mobile|android|iphone)/movie/info/(.+)$': 60, #(60*60*24*30)           # 影片信息
    '^/lyk/v1/(mobile|android|iphone)/movie/list/(.+)$': 60, #(60*60*24)              # 影片列表（城市）
    '^/lyk/v1/(mobile|android|iphone)/movie/hots/(.+)$': 60, #(60*60*24)              # 在映热片列表（城市）
    '^/lyk/v1/(mobile|android|iphone)/movie/wills/(.+)$': 60, #(60*60*24)             # 即将上映列表（城市）
    '^/lyk/v1/(mobile|android|iphone)/area/list/(.+)$': 60, #(60*60*24*30)            # 地区列表
    '^/lyk/v1/(mobile|android|iphone)/filmsession/info/(.+)$': 60, #(60*60*24*30)    # 排场信息
    '^/lyk/v1/(mobile|android|iphone)/activity/list/(.+)$': 60, #(60*60*24*30)    # 活动列表（城市，影片）
    '^/lyk/v1/(mobile|android|iphone)/activity/info/(.+)$': 60, #(60*60*24*30)    # 活动信息（城市，影片）
    'default': 60
}

USER_CACHE_TIMEOUT={
    '^/lyk/v1/(mobile|android|iphone)/home/info/(.+)$': 60, #(60*60*24*30)              # 首页（地图）随机生成sid和username
    '^/lyk/v1/(mobile|android|iphone)/cinema/list/(.+)$': 60, #(60*60*24*30)            # 影院列表（城市）
    '^/lyk/v1/(mobile|android|iphone)/cinema/info/(.+)$': 60, #(60*60*24*30)            # 影院信息
    '^/lyk/v1/(mobile|android|iphone)/user/favor/list/(0|1|2)$': 60, #(60*60*24*30)    # 关注列表
    '^/lyk/v1/(mobile|android|iphone)/user/activity/list$': 60, #(60*60*24*30)          # 用户参与的活动列表
    '^/lyk/v1/(mobile|android|iphone)/user/user/info$': 60, #(60*60*24*30)               # 用户信息
}

def cachetimeout(request):
    path = str(request.path_info)
    keys = CACHE_TIMEOUT.keys()
    for key in keys:
        pattern = re.compile(key)
        if pattern.match(path):
            return CACHE_TIMEOUT[key]
    return CACHE_TIMEOUT['default']

def usercache(request):
    path = str(request.path_info)
    keys = USER_CACHE_TIMEOUT.keys()
    for key in keys:
        pattern = re.compile(key)
        if pattern.match(path):
            return True
    return False