# -*- coding: utf-8 -*-
from HTMLParser import HTMLParser
import hashlib
from math import sin, pi, sqrt, cos, asin, pow
import os
import re
import time
import redis
import requests

os.environ.setdefault("DJANGO_SETTINGS_MODULE", "leyingkeweb.settings")

rediscache = redis.StrictRedis(host='10.200.92.237', port=6379, db=2)

from django.db.models.aggregates import Count
from webtool import atom_att, random26Str, random10Str, random36Str
from datetime import date
import datetime
from django.core.paginator import EmptyPage, InvalidPage, PageNotAnInteger, Paginator
from django.db import connection

import json
from django.core.serializers.json import DjangoJSONEncoder
from lykweb_data.cache import wrap_cache, wrap_cache_key, del_cacheBykey
from lykweb_data.models import LYK_CLIENT_PLATFORM, Movie, ClientDevice, Cinema, Area, DistrictCinema, Filmsession, Activity, City, Condition4Cinema, ClientUser, Filmsession_hall, Movie_Will_City, Application, Movie_Cinecisms, Favorite, UserActivity, UserPhoneNumber, Activity_Movie, Activity_Cinema, IP_City, Cinema_Images, Cinema_Features, ClientVersion, LYK_MEDIA_SCORESOURCE_CHOICES, Movie_News_List


#===================== DB action ==================================
def __movieByDB(movie_id):
    try:
        movie = Movie.objects.get(id=movie_id)
        return movie
    except Exception:
        return None

def __moviesByDB(movie_ids):
    try:
        movies = Movie.objects.filter(id__in=movie_ids)
        return movies
    except Exception:
        return None

def __cinemaByDB(cinema_id):
    try:
        cinema = Cinema.objects.get(id=cinema_id)
        return cinema
    except Exception:
        return None

def __districtListByDB(city_id):
    return Area.objects.filter(cityid=city_id)

def __devicemoviesByDB(mac):
    return ClientDevice.objects.filter(mac=mac).prefetch_related('remind_movies')

def __districtcinemaByDB(district_id):
    return DistrictCinema.objects.filter(district_id=district_id)

def __activityByDB(act_id):
    try:
        activity = Activity.objects.get(id=act_id)
        return activity
    except Exception:
        return None

def __citylistByDB():
    return Area.objects.values_list('cityid', 'cityname').distinct()

def __filmsessionByDB(cinema_id):
    today = date.today()
    return Filmsession.objects.filter(cinema__id=cinema_id, showtime__gte=today).order_by('movie__id', 'showtime')

def __filmsession_hallByDB(cinema_id):
    today = date.today()
    return Filmsession_hall.objects.filter(cinema__id=cinema_id, showtime__gte=today).order_by('movie__id', 'showtime')

def __filmsession_today_ByDB(cinema_id):
    today = date.today()
    oneday = datetime.timedelta(days=1)
    tomorrow = today + oneday
    return Filmsession.objects.filter(cinema__id=cinema_id, showtime__gte=today, showtime__lt=tomorrow).order_by('showtime')

def __filmsession_hall_today_ByDB(cinema_id):
    today = date.today()
    oneday = datetime.timedelta(days=1)
    tomorrow = today + oneday
    return Filmsession_hall.objects.filter(cinema__id=cinema_id, showtime__gte=today, showtime__lt=tomorrow).order_by('showtime')

def __filmsession_tomorrow_ByDB(cinema_id):
    today = date.today()
    oneday = datetime.timedelta(days=1)
    tomorrow = today + oneday
    next = tomorrow + oneday
    return Filmsession.objects.filter(cinema__id=cinema_id, showtime__gte=tomorrow, showtime__lt=next).order_by('showtime')

def __filmsession_hall_tomorrow_ByDB(cinema_id):
    today = date.today()
    oneday = datetime.timedelta(days=1)
    tomorrow = today + oneday
    next = tomorrow + oneday
    return Filmsession_hall.objects.filter(cinema__id=cinema_id, showtime__gte=tomorrow, showtime__lt=next).order_by('showtime')

def __filmsessionInMovieAndCinemaOnTodayByDB(cinema_id, movie_id):
    now = datetime.datetime.now()
    oneday = datetime.timedelta(days=1)
    tomorrow = date.today() + oneday
    return Filmsession.objects.filter(cinema__id=cinema_id, movie__id=movie_id, showtime__gte=now, showtime__lt=tomorrow)

def __lastFilmsessionInMovieByDB(movie_id, city_id):
    today = date.today()
    oneday = datetime.timedelta(days=1)
    tomorrow = date.today() + oneday
    sessions = Filmsession.objects.filter(movie__id=movie_id, date__in=[today, tomorrow], city__cityid=city_id).order_by('showtime')
    return sessions

def __mostSessionsInMovieByDB(movie_id, city_id):
    today = date.today()
    oneday = datetime.timedelta(days=1)
    tomorrow = date.today() + oneday
    cinemas = Cinema.objects.filter(filmsession__date__in=[today, tomorrow], filmsession__city__cityid=city_id, filmsession__movie__id=movie_id)\
    .annotate(num_filmsessions=Count('filmsession')).order_by('-num_filmsessions')
    return cinemas

def __mostSessionsInMovieByCinemaList(cinemaInMovieInCityList):
    cinemas = cinemaInMovieInCityList.annotate(num_filmsessions=Count('filmsession')).order_by('-num_filmsessions')
    return cinemas

def __cinemasInAreaByDB(area_id):
    return Cinema.objects.filter(districtcinema__district_id=area_id).order_by('-onsale')

def __cinemasInCityByDB(city_id):
    return Cinema.objects.filter(city_id=city_id).order_by('-onsale')

def __cinemasInAllByDB():
    return Cinema.objects.all().order_by('-onsale')

def __conditionsInCityByDB(city_id):
    return Condition4Cinema.objects.filter(city_id=city_id)

# 根据用户坐标获取附近影院
def __cinemasOrderByDistance(lat, lon, district_id, city_id):
    if lat and lon: # 有用户坐标
        if district_id: # 城区
            sql = '''
                select (6378137*2*asin(Sqrt(power(sin((%s-c.latitude)*pi()/360),2)
            + Cos(%s*pi()/180)*Cos(c.latitude*pi()/180)*power(sin((%s-c.longitude)*pi()/360),2))))/1000
            as distance, c.* from cinema c, districtcinema d where c.outid=d.cinema_id and d.district_id=%s
            order by c.onsale desc, distance;
            ''' % (lat, lat, lon, district_id)
        elif city_id: # 全市
            sql = '''
                select (6378137*2*asin(Sqrt(power(sin((%s-c.latitude)*pi()/360),2)
            + Cos(%s*pi()/180)*Cos(c.latitude*pi()/180)*power(sin((%s-c.longitude)*pi()/360),2))))/1000
            as distance, c.* from cinema c where c.city_id=%s order by c.onsale desc, distance;
            ''' % (lat, lat, lon, city_id)
        else: # 全国
            sql = '''
                select (6378137*2*asin(Sqrt(power(sin((%s-c.latitude)*pi()/360),2)
            + Cos(%s*pi()/180)*Cos(c.latitude*pi()/180)*power(sin((%s-c.longitude)*pi()/360),2))))/1000
            as distance, c.* from cinema c order by c.onsale desc, distance;
            ''' % (lat, lat, lon)
        return list(Cinema.objects.raw(sql))
    else: # 无用户坐标
        if district_id: # 城区
            cinemas = __cinemasInAreaByDB(district_id)
        elif city_id: # 全市
            cinemas = __cinemasInCityByDB(city_id)
        else:
            cinemas = __cinemasInAllByDB()
        return cinemas.all()

def __citiesHasActivity():
    now = datetime.datetime.now()
    cities = City.objects.filter(activity__status=2, activity__starttime__lte=now, activity__endtime__gte=now).distinct()
    return cities

def __applicationByDB():
    return Application.objects.all().order_by('-createtime')

def __activitiesInMovieByDB(movie_id):
    now = datetime.datetime.now()
    activity_ids = Activity_Movie.objects.filter(movie__id=movie_id).values_list('activity__id', flat=True)
    return Activity.objects.filter(id__in=activity_ids,status=2, starttime__lte=now, endtime__gte=now)

def __applicationsInMovieByDB(movie_id):
    try:
        return Movie.objects.get(id=movie_id).application_set
    except Exception:
        return []

def __activitiesInCinemaByDB(cinema_id):
    now = datetime.datetime.now()
    activity_ids = Activity_Cinema.objects.filter(cinema__id=cinema_id).values_list('activity__id', flat=True)
    return Activity.objects.filter(id__in=activity_ids,status=2, starttime__lte=now, endtime__gte=now)


def __applicationsInCinemaByDB(cinema_id):
    try:
        return Cinema.objects.get(id=cinema_id).application_set
    except Exception:
        return []

def __movie_cinecismsListByDB(movie_id):
    return Movie_Cinecisms.objects.filter(movie__id=movie_id).order_by('-pubtime')

def __movie_cinecismpub2DB(movie_id, username, content):
    try:
        movie = Movie.objects.get(id=movie_id)
        movie_cinecisms = Movie_Cinecisms(movie=movie, username=username, content=content)
        movie_cinecisms.save()
    except Exception:
        pass

def __favorlistByDB(sid, ftype):
    clientuser_id = sid.split('-')[-1]
    return Favorite.objects.filter(clientuser_id=clientuser_id, ftype=ftype, status=1).order_by('-updatetime')

def __favorDataIdsByDB(sid, ftype):
    clientuser_id = sid.split('-')[-1]
    return Favorite.objects.filter(clientuser_id=clientuser_id, ftype=ftype, status=1).values_list('data', flat=True)

def __userActivityListByDB(sid):
    clientuser_id = sid.split('-')[-1]
    return UserActivity.objects.filter(clientuser__id=clientuser_id).order_by('-createtime')

def __userInfoByDB(sid):
    try:
        clientuser_id = sid.split('-')[-1]
        return ClientUser.objects.get(id=clientuser_id)
    except Exception:
        pass

def __userUpdate2DB(sid, username, phonenum, image, code):
    result = {'result': 'success'}
    try:
        clientuser_id = sid.split('-')[-1]
        clientuser = ClientUser.objects.get(id=clientuser_id)
        if username:
            if len(ClientUser.objects.filter(username=username).all()) > 0:
                result = {'result': 'error02'}
            else:
                clientuser.username = username
                clientuser.save()
                result['nickname'] = username
        if image:
            clientuser.portrait_imgurl = image
            clientuser.save()
            result['portrait_url'] = ''.join(['http://115.182.92.236/media/', clientuser.portrait_imgurl.url])
        if phonenum and code:
            cachecode = rediscache.get(phonenum)
            if cachecode == code:
                try:
                    UserPhoneNumber.objects.get(phonenumber=phonenum, clientuser=clientuser)
                except Exception:
                    userphonenumber = UserPhoneNumber(phonenumber=phonenum, clientuser=clientuser)
                    userphonenumber.save()
                rediscache.delete(phonenum)
                result['phonenum'] = phonenum
            else:
                result = {'result': 'error01'}
    except Exception:
        result = {'result': 'error01'}

    return result

#================== transaction ===============================
# 根据影片id和用户标识判断是否是提醒
def __reminded(movie_id, mac):
    reminded = False
    devicemovies = __devicemoviesByDB(mac).all()
    if devicemovies:
        devicemovie = devicemovies[0]
        remindedmovies = devicemovie.remind_movies_set
        for movie in remindedmovies:
            if movie_id == movie.id:
                reminded = True
                break
    return reminded

# 根据城市id获取影院列表
def __cinemaListByDB(city_id):
    listDistrict = __districtListByDB(city_id).all()
    districtcinema = []
    result = []
    for district in listDistrict:
        district_id = district.districtid
        districtcinema.extend(__districtcinemaByDB(district_id).all())

    result = Cinema.objects.filter(districtcinema__in=districtcinema)
    return result

# 根据城市id获取影片列表
def __moviesList(city_id):
    cinemas = __cinemasInCityByDB(city_id)
    cinemas_ids = cinemas.values_list('outid', flat=True)
    movies_ids = Filmsession.objects.filter(cinema_id__in=cinemas_ids).values_list('movie_id', flat=True).distinct()
    result = Movie.objects.filter(outid__in=movies_ids)
    return result

# 根据城市id获取正在热映影片列表
def __hotsList(city_id):
    today = date.today()
    oneday = datetime.timedelta(days=1)
    tomorrow = date.today() + oneday
    movies = Movie.objects.filter(filmsession__date__in=[today, tomorrow], filmsession__city_id=city_id)\
    .annotate(num_filmsessions=Count('filmsession'), ).order_by('-num_filmsessions')
    return movies

# 根据城市id获取即将上映影片列表
def __willsList(city_id):
    today = date.today()
    movies = Movie.objects.filter(movie_will_city__city_id=city_id, pubdate__gt=today).\
    order_by('-index', 'pubdate')
    return movies

def __hotCinemas(city_id):
    cinemas = Cinema.objects.filter(index__gt=0, city__cityid=city_id).order_by('-index')
    return cinemas

def __cinemaInBought(request, cinemas):
    mac = ''
    return cinemas

def __conditions4cinema_default(city_id):
    districts = __districtListByDB(city_id).all()
    return [
            {
            "key": "bought",
            "name": "已购票",
            "sub": [
                    {
                    "name": "已购票",
                    "value": True
                }
            ]
        },
            {
            "key": "areaid",
            "name": "按区域选择",
            "sub": [
                {
                "name": district.districtname,
                "value": district.districtid
            }
            for district in districts]
        }
    ]

def __activitiesInCityByDB(city_id):
    try:
        city = City.objects.get(cityid=city_id)
        now = datetime.datetime.now()
        return city.activity_set.filter(status=2, starttime__lte=now, endtime__gte=now).all()
    except Exception:
        return []

def __activitiesInCityAndMovieByDB(city_id, movie_id):
    try:
        activities = __activitiesInMovieByDB(movie_id)
        result = []
        for activity in activities:
            citys = activity.citys.filter(cityid=city_id)
            if citys:
                result.append(activity)
        return result
    except Exception:
        return []

def cinemaListByMovie(cinemas, movie_id):
    today = date.today()
    oneday = datetime.timedelta(days=1)
    tomorrow = date.today() + oneday
    result = []
    for cinema in cinemas:
        cinema_id = cinema.id
        filmsessions = Filmsession.objects.filter(cinema__id=cinema_id, movie__id=movie_id, showtime__gte=today, showtime__lt=tomorrow).all()
        if list(filmsessions):
            result.append(cinema)
    return result

# 无论上不上映，只要在该城市有活动的影片就返回
def __moviesHasActivity(city_id):
#    moviesByCity = __hotsList(city_id)
    try:
        now = datetime.datetime.now()
        activities = City.objects.get(cityid=city_id).activity_set.filter(status=2, starttime__lte=now, endtime__gte=now)
        movies = Movie.objects.filter(activity__in=activities).distinct()
        return movies
    except Exception:
        return []

def __favor2DB(sid, ftype, status, data):
    clientuser_id = sid.split('-')[-1]
    try:
        clientuser = ClientUser.objects.get(id=clientuser_id)
    except Exception:
        return 0

    try:
        favorDB = Favorite.objects.get(clientuser_id=clientuser_id, ftype=ftype, data=data)
    except Exception:
        favor = Favorite(ftype=ftype, status=status, data=data, clientuser=clientuser)
        favor.save()
        return 1
    favorDB.status=status
    favorDB.save()
    return 2

def __getCDKeyBy10(clientuser_id):
    data1 = random10Str(10)
    data = ''.join([clientuser_id, data1])
    return data[:10]

def __useractivity2DB(sid, activity_id):
    clientuser_id = sid.split('-')[-1]
    try:
        clientuser = ClientUser.objects.get(id=clientuser_id)
    except Exception:
        return 0

    try:
        activity = Activity.objects.get(id=activity_id)
    except Exception:
        return 0

    cdkey = __getCDKeyBy10(clientuser_id)
    favor = UserActivity(clientuser=clientuser, activity=activity, cdkey=cdkey)
    favor.save()
    return 1


def getSid(request):
    sid = atom_att(request, 'sid')
    lsh = request.GET.get('lsh')
    if not lsh and not sid: # Header里没有流水号，并且客户端也没有带流水号的意思
        prefix = random26Str(5)
        clientuser = ClientUser(prefix=prefix)
        clientuser.save()
        return '-'.join([prefix, str(clientuser.id)])

#================== tool ===============================
# 获取影院特点
def __cinemaFeature(strFeature, key):
    result = ''
    if strFeature:
        try:
            j = json.loads(strFeature)
            result = j[key]
        except Exception:
            pass
    return result

# 封装影院Feature
def __encapCinemaFeature(result, description):
    td = __cinemaFeature(description, 'Feature3DContent')
    fd = __cinemaFeature(description, 'Feature4DContent')
    food = __cinemaFeature(description, 'FeatureFoodContent')
    game = __cinemaFeature(description, 'FeatureGameContent')
    imax = __cinemaFeature(description, 'FeatureIMAXContent')
    leisure = __cinemaFeature(description, 'FeatureLeisureContent')
    park = __cinemaFeature(description, 'FeatureParkContent')
    vip = __cinemaFeature(description, 'FeatureVIPContent')
    features = []
    if td:
        features.append({'f_name': '3d', 'f_content': td,})
    if fd:
        features.append({'f_name': '4d', 'f_content': fd,})
    if food:
        features.append({'f_name': 'food', 'f_content': food,})
    if game:
        features.append({'f_name': 'game', 'f_content': game,})
    if imax:
        features.append({'f_name': 'imax', 'f_content': imax,})
    if leisure:
        features.append({'f_name': 'leisure', 'f_content': leisure,})
    if park:
        features.append({'f_name': 'park', 'f_content': park,})
    if vip:
        features.append({'f_name': 'vip', 'f_content': vip,})
    if features:
        result['features'] = features

def __timeformat(strtime):
    if strtime:
        try:
            return strtime.strftime('%Y-%m-%d %H:%M:%S')
        except Exception:
            pass
    return ''

def __timeformat2(strtime):
    if strtime:
        try:
            return strtime.strftime('%H:%M')
        except Exception:
            pass
    return ''

def __timeformat3(strtime):
    if strtime:
        try:
            return strtime.strftime('%Y-%m-%d')
        except Exception:
            pass
    return ''

def __timeformat4(strtime):
    if strtime:
        try:
            timestamp = time.mktime(strtime.timetuple())
            return timestamp
        except Exception:
            pass
    return ''

def __timeformat5(strtime):
    if strtime:
        try:
            return strtime.strftime('%m月%d日')
        except Exception:
            pass
    return ''

def __timeformat6(strtime):
    if strtime:
        try:
            return strtime.strftime('%Y-%m-%d %H:%M')
        except Exception:
            pass
    return ''

def __timeformat7(strtime):
    if strtime:
        try:
            return strtime.strftime('%Y年%m月%d日')
        except Exception:
            pass
    return ''

def __timformat8(strtime):
    if strtime:
        try:
            return datetime.datetime.strptime(strtime,'%Y-%m-%d %H:%M:%S')
        except Exception:
            pass

def __activityStatus(starttime, endtime):
    now = datetime.datetime.now()
    if now < starttime:
        return '即将开始'
    elif now < endtime:
        return '进行中'
    else:
        return '已结束'

def print_DB_SQL():
    for x in connection.queries:
        print 'time: %s, sql: %s' % (x['time'], x['sql'])

def pageOfMovies(list, perpage, page, total):
    try:
        paginator = Paginator(list, int(perpage))
        count = paginator.count # 总数
        movie_ids = paginator.page(int(page))
        nextpage = movie_ids.has_next() # paginator.num_pages > int(page)
        result = {
            'respond': {'status': 200, 'code': 0, 'msg': 'success'},
            'nextpage': nextpage,
            'page': page,
            'movies': [__movieInfoById(movie_id) for movie_id in movie_ids],
            'hot_total': total['hot_total'],
            'will_total': total['will_total'],
            }
    except (EmptyPage, InvalidPage, PageNotAnInteger):
        result = {'respond': {'status': 500, 'code': 20101, 'msg': '暂时没有数据'}}

    return result

def distance2float(strDistance):
    if strDistance:
        fDistance = float(strDistance)
        if fDistance < 1:
            return '%.0f米' % (fDistance * 1000)
        else:
            return '%.2f公里' % fDistance
    return '0米'

def __favorCinemaInfoByWeb(cinema):
    result = {
        'onsale': cinema.onsale,
        'id': str(cinema.id),
        'name': cinema.name,
        'score': cinema.score,
        'address': cinema.address,
        'longitude_baidu': str(cinema.longitude_baidu),
        'latitude_baidu': str(cinema.latitude_baidu),
        'telephone': cinema.telephone,
#        'roadline': cinema.roadline,
        'image_url': cinema.image_url,
    }
    return result

def __cinemaInfo(cinemaMap, movie_id):
    result = {
        'onsale': cinemaMap.onsale,
        'id': str(cinemaMap.id),
        'name': cinemaMap.name,
        'score': cinemaMap.score,
        'address': cinemaMap.address,
        'lon': str(cinemaMap.longitude),
        'lat': str(cinemaMap.latitude),
        'tel': cinemaMap.telephone,
        'roadline': cinemaMap.roadline,
        'image_url': cinemaMap.image_url,
        }
    # 封装影院Feature
    __encapCinemaFeature(result, cinemaMap.description)

    # 影院活动列表
    activityList = __activitiesInCinemaByDB(cinemaMap.id).all()
    if activityList:
        result['activities'] = [
            {
            "iconcolor": activity.iconcolor or '',
            "iconname": activity.iconname or '',
            }
        for activity in activityList]

    # 影院距离
    try:
        result['distance'] = distance2float(cinemaMap.distance)
    except Exception:
        pass

    # 影院某影片余场
    if movie_id:
        filmsessions = list(__filmsessionInMovieAndCinemaOnTodayByDB(cinemaMap.id, movie_id).all())
        result['morenum'] = len(filmsessions)

    return result

def __distanceByCinema(cinema, lon, lat):
    clon = cinema.longitude
    clat = cinema.latitude
    d = (6378137*2*asin(sqrt(pow(sin((float(lat)-float(clat))*pi/360),2)+cos(float(lat)*pi/180)*cos(float(clat)*pi/180)*pow(sin((float(lon)-float(clon))*pi/360),2))))/1000
    return str(d)

def __favorCinemas(favor, lon, lat):
    cinema_id = favor.data
    cinema = __cinemaByDB(cinema_id)
    cinema_images = __cinemaLogo(cinema_id).all()
    if cinema_images:
        cinema.image_url = __cinemaImageUrl2SizeByLYK(cinema_id, 'logo', cinema_images[0].image_url, '')
    # 计算距离
    if lon and lat:
        cinema.distance = __distanceByCinema(cinema, lon, lat)

    result = __favorCinemaInfoByWeb(cinema)
    result['updatetime'] = __timeformat(favor.updatetime)

    return result

def __cinemaHasFavor(result, cinema_id, request):
    sid = atom_att(request, 'sid')
    if sid:
        cinema_ids = __favorDataIdsByDB(sid, '0')
        if str(cinema_id) in cinema_ids:
            result['isfavor'] = 1
        else:
            result['isfavor'] = 0
    else:
        result['isfavor'] = 0


def __isFavorCinemaByClientUser(cinema_id, clientuser):
    if clientuser:
        return __isFavorCinemaByClientUserJson(''.join(['web_isfavorcinema_', cinema_id, str(clientuser.id)]), 60*60, cinema_id, clientuser)['isfavor']
    return -1

@wrap_cache_key()
def __isFavorCinemaByClientUserJson(key, timeout, cinema_id, clientuser):
    if clientuser:
        if clientuser.id:
            cinema_ids = __favorDataIdsByDB(str(clientuser.id), '0')
            if str(cinema_id) in cinema_ids:
                return {'isfavor': 1}
    return {'isfavor': -1}

def hasAct(cinema):
    activities = __activitiesInCinemaByDB(cinema.id).all()
    if activities:
        return True
    return False

def __nearMovieByCinema(cinema):
    movie_sessions = cinema.filmsession_set.filter(showtime__gte=datetime.datetime.now()).order_by('showtime').all()
    if movie_sessions:
        return movie_sessions[0]

def __cinemaByHome(cinema):
    activityList = __activitiesInCinemaByDB(cinema.id).all()
    result = {
        'cinema_id': str(cinema.id),
        'title1': cinema.name,
        'lon': str(cinema.longitude),
        'lat': str(cinema.latitude),
        }
    if activityList:
        activity = activityList[0]
        activity_cinema = activity.activity_cinema_set.get(cinema__id=cinema.id)
        result['optype'] = activity_cinema.optype
        result['pintype'] = activity_cinema.pintype
        if activity_cinema.optype == 3:
            movie_session = __nearMovieByCinema(cinema)
            if movie_session:
                result['movie_id'] = str(movie_session.movie.id)
                result['title2'] = movie_session.movie.title
                result['img_url'] = __imageUrl2SmallByMtime(movie_session.movie.poster_image_url)
                result['price'] = movie_session.price
                result['showtime'] = __timeformat2(movie_session.showtime)
        else:
            result['title2'] = activity.title
            result['img_url'] = activity.image_url
            result['intro'] = activity.introduction
            result['url'] = activity.url
    else:
        result['optype'] = 3
        result['pintype'] = 0
        movie_session = __nearMovieByCinema(cinema)
        if movie_session:
            result['movie_id'] = str(movie_session.movie.id)
            result['title2'] = movie_session.movie.title
            result['img_url'] = __imageUrl2SmallByMtime(movie_session.movie.poster_image_url)
            result['price'] = movie_session.price
            result['showtime'] = __timeformat2(movie_session.showtime)
    return result

def __activityByHome(activity):
    return {
        "id": str(activity.id),
        #        "icontype": activity.icontype,
        #        "iconname": activity.iconname or '',
        "iconurl": activity.iconurl or '',
        "optype": activity.optype,
        "data": activity.data,
        }

def __cinemasHasSessionOrAct(cinemas):
    result = []
    for cinema in cinemas:
        optype = cinema.get('optype')
        movie_id = cinema.get('movie_id')
        if optype == 3 and not movie_id:
            pass
        else:
            result.append(cinema)
    return result

def __activityInfo2Json(activity):
    return {
        'id': str(activity.id),
        'title': activity.title,
        'introduction': activity.introduction,
        'starttime': __timeformat(activity.starttime),
        'endtime': __timeformat(activity.endtime),
        'optype': activity.optype,
        'data': activity.data,
        }

def __appInfo2Json(application):
    return {
        'id': str(application.id),
        'title': application.title,
        'image_url': application.image_url,
        'introduction': application.introduction,
        'download_url': application.download_url,
        }

def __movieInfoById(movie_id):
    movie = __movieByDB(movie_id)
    return __movieInfo2Json(movie)

def __movieInfo2Json(movie):
    result = {
        'id': str(movie.id),
        'title': movie.title,
        'directors': movie.directors,
        'pubdate': __timeformat3(movie.pubdate),
        'plots': movie.plots,
        'poster_image_url': __imageUrl2SmallByMtime(movie.poster_image_url),
        'score': movie.score,
        'actors': movie.actors,
        'mins': movie.mins,
        }
    activityList = __activitiesInMovieByDB(movie.id).all()
    if activityList:
        result['activities'] = [
            {
            "iconcolor": activity.iconcolor or '',
            "iconname": activity.iconname or '',
            }
        for activity in activityList]
    return result

def __imageUrl2Orig(url):
    if url.find('_') > -1:
        suffix = url.split('.')[-1]
        prefix = url[0: url.rfind('_')]
        return ''.join([prefix, '.', suffix])
    else:
        return url

def __imageUrl2SizeByMtime(url, size):
    if url.find('_') > -1:
        prefix = url[0: url.rfind('_')]
    else:
        prefix = url[0: url.rfind('.')]
    suffix = url.split('.')[-1]
    return ''.join([prefix, '_', size, '.', suffix])

def __getFileName(url):
    suff = url.split('.')[-1]
    filename = url.split('/')[-1].replace(''.join(['.', suff]), '')
    return ''.join([filename, '.', suff])

def __imageUrl2SmallByMtime(url):
    return __imageUrl2SizeByMtime(url, '220X350')

def __imageUrl2LargeByMtime(url):
    return __imageUrl2SizeByMtime(url, '640X960')

def __imageUrl2SmallByGewara(url):
    return url.replace('sw600h600', 'sw150h200')

def __imageUrl2SizeByLYK(movie_id, type, url, size):
    if 'gewara.cn' in url or 'mtime.cn' in url or 'mtime.com' in url:
        url_pref = '%s%s' % ('http://115.182.92.236/m/', movie_id)
        filename = ''
        if 's' == size:
            if 'gewara.cn' in url:
                filename = ''.join(['s_', __getFileName(url)])
            else:
                filename = __getFileName(__imageUrl2SmallByMtime(url))
        elif 'l' == size:
            if 'gewara.cn' in url:
                filename = ''.join(['s_', __getFileName(url)])
            else:
                filename = __getFileName(__imageUrl2LargeByMtime(url))
        else:
            filename = __getFileName(url)

        if 'poster' == type:
            filename = '%s%s' % ('/', filename)
        if 'posters' == type:
            filename = '%s%s' % ('/posters/', filename)
        if 'stills' == type:
            filename = '%s%s' % ('/stills/', filename)
        if 'trailers' == type:
            filename = '%scompress_%s' % ('/trailers/', filename)

        return ''.join([url_pref, filename])
    return url

def __cinemaImageUrl2SizeByLYK(cinema_id, type, url, size):
    url_pref = '%s%s' % ('http://115.182.92.236/c/', cinema_id)
    filename = ''
    if 'l' == size:
        filename = __getFileName(__imageUrl2LargeByMtime(url))
    else:
        filename = __getFileName(url)

    if 'logo' == type:
        filename = '%s%s' % ('/', filename)
    elif 'images' == type:
        filename = '%s%s' % ('/images/', filename)

    return ''.join([url_pref, filename])

def __encap_movie_stills(result, still_set):
    if still_set and still_set.all():
        gewaralist = still_set.filter(source='gewara').all()
        mtimelist = still_set.filter(source='mtime').all()
        if gewaralist:
            result['movieinfo']['stills'] = [{'s_image_s_url': __imageUrl2SmallByGewara(still.image_url), 's_image_l_url': still.image_url} for still in gewaralist[0:30]]
        else:
            result['movieinfo']['stills'] = [{'s_image_s_url': __imageUrl2SmallByMtime(still.image_url), 's_image_l_url': __imageUrl2LargeByMtime(still.image_url)} for still in mtimelist[0:30]]

def __userActivity2Json(useractivity):
    result = {
        'id': str(useractivity.activity.id),
        'title': useractivity.activity.title,
        'image_url': useractivity.activity.image_url or '',
        'introduction': useractivity.activity.introduction,
        'starttime': __timeformat7(useractivity.activity.starttime),
        'endtime': __timeformat7(useractivity.activity.endtime),
        'status': __activityStatus(useractivity.activity.starttime, useractivity.activity.endtime),
        'optype': useractivity.activity.optype,
        'data': useractivity.activity.data,
        'cdkey': useractivity.cdkey,
        'updatetime': __timeformat(useractivity.updatetime),
        'qrcode': '' # TODO 二维码图片地址
    }

    return result

def __randomUserName(sid):
    username = ''.join(['乐影客', random36Str(6)])
    if sid.find('-') > -1:
        client_id = sid.split('-')[1]
        try:
            clientuser = ClientUser.objects.get(id=client_id)
            clientuser.username=username
            clientuser.save()
        except Exception:
            pass

    return username

def __sortCinemasByActivityCount(cinemas):
    now = datetime.datetime.now()
    result = sorted(cinemas, key=lambda cinema: len(Activity.objects.filter(id__in=Activity_Cinema.objects.filter(cinema__id=cinema.id).values_list('activity__id', flat=True), status=2, starttime__lte=now, endtime__gte=now).all()), reverse=True)
    return result

def __sortCinemasByIndex(cinemas):
    result = sorted(cinemas, key=lambda cinema: cinema.index,reverse=True)
    return result

def __getUserName(cinecism):
    if cinecism:
        if 'lyk' == cinecism.source:
            sid = cinecism.usersid
            clientuser = __userInfoByDB(sid)
            return clientuser.username
        else:
            return cinecism.username

def __getPortraitUrlByCinecism(cinecism):
    if cinecism and 'lyk' == cinecism.source:
        sid = cinecism.usersid
        user = __userInfoByDB(sid)
        if user:
            portrait_imgurl = user.portrait_imgurl
            if portrait_imgurl:
                return ''.join(['http://115.182.92.236/media/', portrait_imgurl.url])
    return ''

#========================== web ================================
@wrap_cache_key()
def __movieInfoByWeb(key, timeout, movie_id, city_id):
    movie = __movieByDB(movie_id)
    # 海报
    movie.poster_image_url = __imageUrl2SizeByLYK(movie.id, 'poster', movie.poster_image_url, 's')
    # 影片排场
    movie.sessionscount = __lastFilmsessionInMovieByDB(movie_id, city_id).count()

    result = {}
    if movie:
        result['id'] = str(movie.id)
        result['title'] = movie.title
        result['score'] = movie.score
        result['poster_image_url'] = movie.poster_image_url
    try:
        result['sessionscount'] = movie.sessionscount
    except Exception:
        pass

    return result

@wrap_cache_key()
def __movieInfoByWebHome(key, timeout, movie_id, city_id):
    movie = __movieByDB(movie_id)
    # 海报
    movie.poster_image_url = __imageUrl2SizeByLYK(movie.id, 'poster', movie.poster_image_url, 's')

    # 有活动的影院
    cinemaHasActivity = __cinemaHasActivitylistInMovie(movie_id, city_id)
    if cinemaHasActivity:
        movie.cinema_activity = cinemaHasActivity
    # 影片排场
    movie.sessionscount = __lastFilmsessionInMovieByDB(movie_id, city_id).count()

    # 电影报道：
    newsList = __newsListInMovie(movie_id)

    result = {}
    if movie:
        result['id'] = str(movie.id)
        result['title'] = movie.title
        result['score'] = __movieScore(__movieScores(movie, 'all'))
        result['poster_image_url'] = movie.poster_image_url
    try:
        result['sessionscount'] = movie.sessionscount
    except Exception:
        pass
    try:
        result['cinema_activity'] = {
            'id': str(movie.cinema_activity.id),
            'name': movie.cinema_activity.name,
            'activity_title': movie.cinema_activity.activity.title,
        }
    except Exception:
        pass
    try:
        result['newslist'] = [
            {
            'title': HTMLParser().unescape(HTMLParser().unescape(HTMLParser().unescape(news.title))),
            'url': news.url,
            'img_url': news.img_url or '',
            'content': HTMLParser().unescape(HTMLParser().unescape(HTMLParser().unescape(news.content))) or '',
            }
        for news in newsList[:3]]
    except Exception:
        pass

    return result
#    return movie

@wrap_cache_key()
def __HotMovieInfoByWebHome(key, timeout, movie_id, city_id):
    movie = __movieByDB(movie_id)
    # 海报
    movie.poster_image_url = __imageUrl2SizeByLYK(movie.id, 'poster', movie.poster_image_url, 's')
    # 最近一场排场
    movie.sessionscount = __lastFilmsessionInMovieByDB(movie_id, city_id).count()
    movie.sessionlist = __lastFilmsessionInMovieByDB(movie_id, city_id).all()
#    if __lastFilmsessionInMovieByDB(movie_id, city_id).filter(showtime__gte=datetime.datetime.now()).all():
#        movie.lastsession = __lastFilmsessionInMovieByDB(movie_id, city_id).filter(showtime__gte=datetime.datetime.now()).all()[0]
#        movie.lastsession.showtime = __timeformat2(movie.lastsession.showtime)

    # 此影片此城市的影院列表
    cinemaInMovieInCityList = __cinemaListInMovieAndCity(movie_id, city_id)
    # 最多排场影院
    movie.cinemascount = __mostSessionsInMovieByCinemaList(cinemaInMovieInCityList).count()
    if movie.cinemascount:
        movie.cinema_mostsession = __mostSessionsInMovieByCinemaList(cinemaInMovieInCityList)[0]
    # 有活动的影院
    cinemaHasActivity = __cinemaHasActivitylistInMovie(movie_id, city_id)
    if cinemaHasActivity:
        movie.cinema_activity = cinemaHasActivity
    # 有停车位的影院
    cinemalistByPark = __cinemaInMovieByFeatureByCinemaList(cinemaInMovieInCityList, 7)
    if cinemalistByPark:
        movie.cinemacountByPark = len(cinemalistByPark)
        movie.cinemaByPark = cinemalistByPark[0]
    # 可选座的影院
    cinemalistBySeat = __cinemaInMovieBySeatByCinemaList(cinemaInMovieInCityList)
    if cinemalistBySeat:
        movie.cinemacountBySeat = len(cinemalistBySeat)
        movie.cinemaBySeat = cinemalistBySeat[0]
    # 有3D/IMAX厅：
    cinemalistBy3DorIMAX = __cinemaInMovieByFeatureByCinemaList(cinemaInMovieInCityList, 1)
    if cinemalistBy3DorIMAX:
        movie.cinemacountBy3D = len(cinemalistBy3DorIMAX)
        movie.cinemaBy3D = cinemalistBy3DorIMAX[0]
    # 电影报道：
    newsList = __newsListInMovie(movie_id)

    result = {}
    if movie:
        result['id'] = str(movie.id)
        result['title'] = movie.title
        result['score'] = __movieScore(__movieScores(movie, 'all'))
        result['poster_image_url'] = movie.poster_image_url
    try:
        result['sessionscount'] = movie.sessionscount
    except Exception:
        pass
    try:
        result['sessionlist'] = [{
            'cinema_id': str(session.cinema.id),
            'cinema_name': session.cinema.name,
            'showtime': __timeformat(session.showtime),
        }for session in movie.sessionlist]
    except Exception:
        pass
    try:
        result['cinemascount'] = movie.cinemascount
    except Exception:
        pass
    try:
        result['cinema_mostsession'] = {
            'id': str(movie.cinema_mostsession.id),
            'name': movie.cinema_mostsession.name,
            'num_filmsessions': movie.cinema_mostsession.num_filmsessions,
        }
    except Exception:
        pass
    try:
        result['cinema_activity'] = {
            'id': movie.cinema_activity.id,
            'name': movie.cinema_activity.name,
            'activity_title': movie.cinema_activity.activity.title,
        }
    except Exception:
        pass
    try:
        result['cinemacountByPark'] = movie.cinemacountByPark
        result['cinemaByPark'] = {
            'id': str(movie.cinemaByPark.id),
            'name': movie.cinemaByPark.name,
        }
    except Exception:
        pass
    try:
        result['cinemacountBySeat'] = movie.cinemacountBySeat
        result['cinemaBySeat'] = {
            'id': str(movie.cinemaBySeat.id),
            'name': movie.cinemaBySeat.name,
        }
    except Exception:
        pass
    try:
        result['cinemacountBy3D'] = movie.cinemacountBy3D
        result['cinemaBy3D'] = {
            'id': str(movie.cinemaBy3D.id),
            'name': movie.cinemaBy3D.name,
            }
    except Exception:
        pass
    try:
        result['newslist'] = [
            {
                'title': news.title,
                'url': news.url,
                'img_url': __movieNews_ImgUrl(news.img_url),
                'content': news.content or '',
            }
        for news in newsList[:3]]
    except Exception:
        pass

    return result
#    return movie

def getLastSession(result):
    sessionlist = result.get('sessionlist', None)
    if sessionlist:
        for session in sessionlist:
            showtime = session['showtime']
            showtimeDate = __timformat8(showtime)
            if showtimeDate > datetime.datetime.now():
                result['lastsession'] = {
                    'cinema_id': str(session['cinema_id']),
                    'cinema_name': session['cinema_name'],
                    'showtime': __timeformat2(showtimeDate),
                    }
                return

def getRequest(url):
    try:
        response = requests.get(url, headers={'Accept-Charset':'GBK,utf-8;q=0.7,*;q=0.3'})
        if response.content:
        #        print response.content
            return response.content
    except Exception:
        pass

#@wrap_cache_key()
def __getCityId(key, timeout, request):
    cityid = '290'
    cityName = request.session.get('city_name', '北京')
    cityidSession = request.session.get('city_id', None)
    cityidParam = request.GET.get('cityid', None)
    if cityidParam:
        cityid = cityidParam
        city = City.objects.get(cityid = cityid)
        cityName = city.cityname
    elif cityidSession:
        cityid = cityidSession
    else:
        remoteIP = request.META['REMOTE_ADDR']
        try:
            ip_city = IP_City.objects.get(ip=remoteIP)
            cityid = ip_city.cityid
        except Exception:
            try:
                jsontext = getRequest('%s%s' % ('http://ip.taobao.com/service/getIpInfo.php?ip=', remoteIP))
                if jsontext:
                    try:
                        j = json.loads(jsontext)
                        try:
                            cityname = j['data']['city'] # 城市
                            if cityname.find(u'市') != -1:
                                cityname = cityname[:cityname.rfind(u'市')]
                            if cityname.find(u'自治') != -1:
                                cityname = cityname[:cityname.rfind(u'自治')]
                            try:
                                city = City.objects.get(cityname = cityname)
                                cityid = city.cityid
                                cityName = cityname
                                ip_city = IP_City(ip=remoteIP, cityid=cityid)
                                ip_city.save()
                            except Exception:
                                pass
                        except Exception:
                            region = j['data']['region'] # 省份
                            if region.find(u'省'):
                                region = region[:region.rfind(u'省')]
                            if region.find(u'自治'):
                                region = region[:region.rfind(u'自治')]
                    except Exception:
                        pass
            except Exception:
                pass
    request.session['city_id'] = cityid
    request.session['city_name'] = cityName

    return {'cityid': cityid, 'cityname': cityName}
#    return cityid

@wrap_cache_key()
def __hotCinemaListByWebHome(key, timeout, city_id):
    cinemas = Cinema.objects.filter(index__gt=0, city__cityid=city_id).order_by('-index')
    result = []
    for cinema in cinemas:
        cinemaJson = {'id': str(cinema.id),'name': cinema.name,}
        if cinema.cinema_features_set.all():
            cinemaJson['features'] = [cinema_feature.cinemafeaturetype.type for cinema_feature in cinema.cinema_features_set.all()]
        result.append(cinemaJson)
    return result

@wrap_cache_key()
def __cinemaListByWeb(key, timeout, city_id):
    cinemas = Cinema.objects.filter(city__cityid=city_id).order_by('-index', '-score')
    result = [
        {
        'id': str(cinema.id),
        'name': cinema.name,
        }
    for cinema in cinemas]
    return result

def __cinemaListHome2ByWeb(cinemaList):
    result = []
    count = 0
    for cinema in cinemaList:
        if count % 2 == 0:
            result.append([cinema,])
        else:
            result[len(result)-1].append(cinema)
        count += 1
    return result

def __cinemaImages(cinema_id):
    return Cinema_Images.objects.filter(cinema__id=cinema_id)

def __cinemaLogo(cinema_id):
    return Cinema_Images.objects.filter(cinema__id=cinema_id, islogo=1)

@wrap_cache_key()
def __cinemasListInCity(key, timeout, city_id):
    cinemalist = Cinema.objects.filter(city__cityid=city_id).all()
    for cinema in cinemalist:
        cinema.name = __strNameCut(cinema.name, 13)
        cinema.sessioncount = cinema.filmsession_set.filter(date=datetime.date.today()).count()
        cinema.hallcount = cinema.filmsession_hall_set.all().values_list('hall_num', flat=True).distinct().count()
        cinema.movielist = __movielistInCinema(cinema)
        cinema.moviecount = len(cinema.movielist)
        cinema.features = __featureTypeIDOfCinema(cinema.id)
        cinema_images = __cinemaLogo(cinema.id).all()
        if cinema_images:
            cinema.image_url = __cinemaImageUrl2SizeByLYK(cinema.id, 'logo', cinema_images[0].image_url, '')
    cinemalist = __sortCinemaListBySessioncount(cinemalist)
    result = []
    for cinema in cinemalist:
        cinemaJson = {
            'id': str(cinema.id),
            'name': cinema.name,
            'sessioncount': cinema.sessioncount,
            'hallcount': cinema.hallcount,
            'movielist': [{'id': movie.id, 'title': movie.title} for movie in cinema.movielist],
            'moviecount': cinema.moviecount,
            'features': cinema.features,
            'longitude_baidu': str(cinema.longitude_baidu),
            'latitude_baidu': str(cinema.latitude_baidu),
            'telephone': cinema.telephone,
            'address': cinema.address,
            }
        try:
            cinemaJson['image_url'] = cinema.image_url
        except Exception:
            pass
        try:
            cinemaJson['districtname'] = cinema.districtcinema.district.districtname
        except Exception:
            pass
        result.append(cinemaJson)
    return result

def __cinemaListByFeatures(featureidList, cinemas):
    result = []
    for featureid in featureidList:
        for cinema in cinemas:
            feature_set = cinema.cinema_features_set.all().values_list('cinemafeaturetype__id', flat=True)
            if featureid in feature_set:
                result.append(cinema)
    return result

def __cinemaListByKeyword(keyword, cinemas):
    result = []
    for cinema in cinemas:
        if cinema.name.find(keyword) != -1:
            result.append(cinema)
    return result

@wrap_cache_key()
def __cinemasSearch(key, timeout, city_id, keyword, feature_ids):
    featureidList = []
    if feature_ids:
        featureidList = feature_ids.split(',')

    cinemalist = Cinema.objects.filter(city__cityid=city_id).all()

    if featureidList:
        cinemalist = __cinemaListByFeatures(featureidList, cinemalist)
    if keyword:
        cinemalist = __cinemaListByKeyword(keyword, cinemalist)

    for cinema in cinemalist:
        cinema.name = __strNameCut(cinema.name, 13)
        cinema.sessioncount = cinema.filmsession_set.filter(date=datetime.date.today()).count()
        cinema.hallcount = cinema.filmsession_hall_set.all().values_list('hall_num', flat=True).distinct().count()
        cinema.movielist = __movielistInCinema(cinema)
        cinema.moviecount = len(cinema.movielist)
        cinema.features = __featureTypeIDOfCinema(cinema.id)
        cinema_images = __cinemaLogo(cinema.id).all()
        if cinema_images:
            cinema.image_url = __cinemaImageUrl2SizeByLYK(cinema.id, 'logo', cinema_images[0].image_url, '')
    cinemalist = __sortCinemaListBySessioncount(cinemalist)
    result = []
    for cinema in cinemalist:
        cinemaJson = {
            'id': str(cinema.id),
            'name': cinema.name,
            'sessioncount': cinema.sessioncount,
            'hallcount': cinema.hallcount,
            'movielist': [{'id': movie.id, 'title': movie.title} for movie in cinema.movielist],
            'moviecount': cinema.moviecount,
            'features': cinema.features,
            }
        try:
            cinemaJson['image_url'] = cinema.image_url
        except Exception:
            pass
        result.append(cinemaJson)
    return result

@wrap_cache_key()
def __cinemasCountInCity(key, timeout, city_id):
    result = {'cinemacount': 0}
    try:
        cinemacount = Cinema.objects.filter(city__cityid=city_id).all().count()
        result = {'cinemacount': cinemacount}
    except Exception:
        pass
    return result

@wrap_cache_key()
def __filmsessionsInCity(key, timeout, city_id):
    today = date.today()
    filmsessioncount = Filmsession.objects.filter(date=today, city__cityid=city_id).count()
    return {'filmsessioncount': filmsessioncount}

def __strNameCut(nameStr, length):
    if nameStr:
        nameStr = u''.join(nameStr)
        if len(nameStr) > length:
            return nameStr[:length]
    return nameStr

def __isPlayed(showtime):
    now = datetime.datetime.now()
    return showtime < now

def __sortCinemaListBySessioncount(cinemas):
    result = sorted(cinemas, key=lambda cinema: cinema.sessioncount,reverse=True)
    return result

def __movielistInCinema(cinema):
    result = []
    movielist = cinema.filmsession_set.filter(date=datetime.date.today()).values_list('movie_id','movie__title').distinct()
    for movie in movielist:
        movie_id = movie[0]
        movie_title = movie[1]
        movieObj = Movie(id=movie_id, title=movie_title)
        result.append(movieObj)
    return result

# 横版海报
def __movie_Horizon_Poster(movie):
    horizon_poster_list = movie.movie_posters_set.filter(type=1).all()
    if horizon_poster_list:
        return __imageUrl2SizeByLYK(movie.id, 'posters', horizon_poster_list[0].image_url, 'l')
    return __imageUrl2SizeByLYK(movie.id, 'poster', movie.poster_image_url, 'l')

#@wrap_cache_key()
def __citylistByPinyin(key, timeout):
#    citylist = City.objects.order_by('-index', 'pinyin').all()
#    result = [
#        {
#            'cityid': str(city.cityid),
#            'cityname': city.cityname,
#        }
#    for city in citylist]
    result = [{"clist": [{"id": "290", "name": "北京"}, {"id": "292", "name": "上海"}, {"id": "293", "name": "天津"}, {"id": "291", "name": "重庆"}], "pname": "直辖市"}, {"clist": [{"id": "296", "name": "安庆"}, {"id": "297", "name": "蚌埠"}, {"id": "302", "name": "亳州"}, {"id": "298", "name": "巢湖"}, {"id": "299", "name": "池州"}, {"id": "300", "name": "滁州"}, {"id": "295", "name": "合肥"}, {"id": "304", "name": "淮南"}, {"id": "305", "name": "黄山"}, {"id": "312", "name": "六安"}, {"id": "313", "name": "马鞍山"}, {"id": "317", "name": "铜陵"}, {"id": "320", "name": "芜湖"}, {"id": "321", "name": "宣城"}], "pname": "安徽"}, {"clist": [{"id": "324", "name": "长乐"}, {"id": "328", "name": "福州"}, {"id": "331", "name": "晋江"}, {"id": "333", "name": "龙岩"}, {"id": "334", "name": "南安"}, {"id": "335", "name": "南平"}, {"id": "336", "name": "宁德"}, {"id": "337", "name": "莆田"}, {"id": "338", "name": "泉州"}, {"id": "339", "name": "三明"}, {"id": "341", "name": "石狮"}, {"id": "323", "name": "厦门"}, {"id": "343", "name": "永安"}, {"id": "344", "name": "漳平"}, {"id": "345", "name": "漳州"}], "pname": "福建"}, {"clist": [{"id": "349", "name": "白银"}, {"id": "354", "name": "酒泉"}, {"id": "347", "name": "兰州"}, {"id": "355", "name": "陇南"}, {"id": "360", "name": "天水"}, {"id": "361", "name": "武威"}, {"id": "363", "name": "张掖"}], "pname": "甘肃"}, {"clist": [{"id": "369", "name": "潮州"}, {"id": "371", "name": "东莞"}, {"id": "373", "name": "佛山"}, {"id": "365", "name": "广州"}, {"id": "376", "name": "鹤山"}, {"id": "377", "name": "河源"}, {"id": "379", "name": "惠州"}, {"id": "380", "name": "江门"}, {"id": "381", "name": "揭阳"}, {"id": "389", "name": "茂名"}, {"id": "390", "name": "梅州"}, {"id": "394", "name": "清远"}, {"id": "367", "name": "汕头"}, {"id": "395", "name": "汕尾"}, {"id": "396", "name": "韶关"}, {"id": "366", "name": "深圳"}, {"id": "403", "name": "阳江"}, {"id": "404", "name": "英德"}, {"id": "405", "name": "云浮"}, {"id": "407", "name": "湛江"}, {"id": "408", "name": "肇庆"}, {"id": "409", "name": "中山"}, {"id": "368", "name": "珠海"}], "pname": "广东"}, {"clist": [{"id": "412", "name": "百色"}, {"id": "413", "name": "北海"}, {"id": "418", "name": "防城港"}, {"id": "419", "name": "贵港"}, {"id": "420", "name": "桂林"}, {"id": "423", "name": "河池"}, {"id": "422", "name": "贺州"}, {"id": "425", "name": "柳州"}, {"id": "411", "name": "南宁"}, {"id": "428", "name": "梧州"}], "pname": "广西"}, {"clist": [{"id": "434", "name": "安顺"}, {"id": "435", "name": "毕节"}, {"id": "437", "name": "都匀"}, {"id": "433", "name": "贵阳"}, {"id": "440", "name": "六盘水"}, {"id": "444", "name": "仁怀"}, {"id": "445", "name": "铜仁"}, {"id": "448", "name": "遵义"}], "pname": "贵州"}, {"clist": [{"id": "1755", "name": "澄迈县"}, {"id": "450", "name": "海口"}, {"id": "1748", "name": "琼海"}, {"id": "451", "name": "三亚"}], "pname": "海南"}, {"clist": [{"id": "455", "name": "保定"}, {"id": "458", "name": "沧州"}, {"id": "459", "name": "承德"}, {"id": "464", "name": "邯郸"}, {"id": "470", "name": "廊坊"}, {"id": "475", "name": "秦皇岛"}, {"id": "453", "name": "石家庄"}, {"id": "480", "name": "唐山"}, {"id": "482", "name": "邢台"}, {"id": "485", "name": "张家口"}], "pname": "河北"}, {"clist": [{"id": "533", "name": "大庆"}, {"id": "528", "name": "哈尔滨"}, {"id": "539", "name": "虎林"}, {"id": "540", "name": "佳木斯"}, {"id": "541", "name": "鸡西"}, {"id": "544", "name": "牡丹江"}, {"id": "545", "name": "讷河"}, {"id": "547", "name": "齐齐哈尔"}, {"id": "553", "name": "绥化"}], "pname": "黑龙江"}, {"clist": [{"id": "490", "name": "安阳"}, {"id": "495", "name": "鹤壁"}, {"id": "497", "name": "焦作"}, {"id": "498", "name": "济源"}, {"id": "499", "name": "开封"}, {"id": "502", "name": "漯河"}, {"id": "503", "name": "洛阳"}, {"id": "504", "name": "孟州"}, {"id": "505", "name": "南阳"}, {"id": "506", "name": "平顶山"}, {"id": "507", "name": "濮阳"}, {"id": "510", "name": "三门峡"}, {"id": "511", "name": "商丘"}, {"id": "517", "name": "新乡"}, {"id": "515", "name": "信阳"}, {"id": "520", "name": "许昌"}, {"id": "524", "name": "禹州"}, {"id": "489", "name": "郑州"}, {"id": "525", "name": "周口"}, {"id": "526", "name": "驻马店"}], "pname": "河南"}, {"clist": [{"id": "563", "name": "赤壁"}, {"id": "564", "name": "丹江口"}, {"id": "567", "name": "恩施"}, {"id": "568", "name": "鄂州"}, {"id": "572", "name": "黄冈"}, {"id": "573", "name": "黄石"}, {"id": "574", "name": "荆门"}, {"id": "575", "name": "荆州"}, {"id": "579", "name": "潜江"}, {"id": "581", "name": "十堰"}, {"id": "583", "name": "随州"}, {"id": "561", "name": "武汉"}, {"id": "586", "name": "襄阳"}, {"id": "587", "name": "咸宁"}, {"id": "588", "name": "仙桃"}, {"id": "589", "name": "孝感"}, {"id": "590", "name": "宜昌"}], "pname": "湖北"}, {"clist": [{"id": "600", "name": "常德"}, {"id": "598", "name": "长沙"}, {"id": "599", "name": "郴州"}, {"id": "602", "name": "衡阳"}, {"id": "604", "name": "怀化"}, {"id": "607", "name": "耒阳"}, {"id": "609", "name": "涟源"}, {"id": "610", "name": "醴陵"}, {"id": "612", "name": "浏阳"}, {"id": "613", "name": "娄底"}, {"id": "617", "name": "邵阳"}, {"id": "618", "name": "湘潭"}, {"id": "619", "name": "湘西"}, {"id": "620", "name": "湘乡"}, {"id": "621", "name": "益阳"}, {"id": "623", "name": "岳阳"}, {"id": "624", "name": "张家界"}, {"id": "625", "name": "株洲"}], "pname": "湖南"}, {"clist": [{"id": "756", "name": "包头"}, {"id": "757", "name": "巴彦淖尔"}, {"id": "764", "name": "呼伦贝尔"}, {"id": "758", "name": "赤峰"}, {"id": "759", "name": "鄂尔多斯"}, {"id": "753", "name": "呼和浩特"}, {"id": "767", "name": "通辽"}, {"id": "768", "name": "乌海"}, {"id": "770", "name": "乌兰浩特"}], "pname": "内蒙古"}, {"clist": [{"id": "1332", "name": "苏州"}, {"id": "629", "name": "常熟"}, {"id": "630", "name": "常州"}, {"id": "631", "name": "大丰"}, {"id": "632", "name": "丹阳"}, {"id": "633", "name": "东台"}, {"id": "634", "name": "高邮"}, {"id": "635", "name": "海门"}, {"id": "636", "name": "淮安"}, {"id": "639", "name": "江都"}, {"id": "640", "name": "姜堰"}, {"id": "642", "name": "靖江"}, {"id": "643", "name": "金坛"}, {"id": "644", "name": "句容"}, {"id": "645", "name": "昆山"}, {"id": "646", "name": "连云港"}, {"id": "647", "name": "溧阳"}, {"id": "628", "name": "南京"}, {"id": "649", "name": "南通"}, {"id": "651", "name": "邳州"}, {"id": "652", "name": "启东"}, {"id": "653", "name": "如皋"}, {"id": "654", "name": "宿迁"}, {"id": "655", "name": "太仓"}, {"id": "656", "name": "泰兴"}, {"id": "660", "name": "吴江"}, {"id": "662", "name": "无锡"}, {"id": "664", "name": "徐州"}, {"id": "665", "name": "盐城"}, {"id": "667", "name": "扬州"}, {"id": "668", "name": "宜兴"}, {"id": "669", "name": "仪征"}, {"id": "670", "name": "张家港"}, {"id": "671", "name": "镇江"}, {"id": "641", "name": "江阴"}, {"id": "4954", "name": "射阳"}], "pname": "江苏"}, {"clist": [{"id": "676", "name": "丰城"}, {"id": "677", "name": "赣州"}, {"id": "682", "name": "吉安"}, {"id": "681", "name": "景德镇"}, {"id": "680", "name": "九江"}, {"id": "684", "name": "乐平"}, {"id": "674", "name": "南昌"}, {"id": "688", "name": "上饶"}, {"id": "689", "name": "新余"}, {"id": "1744", "name": "宜春"}, {"id": "690", "name": "鹰潭"}, {"id": "1349", "name": "萍乡"}], "pname": "江西"}, {"clist": [{"id": "693", "name": "长春"}, {"id": "703", "name": "吉林市"}, {"id": "713", "name": "四平"}, {"id": "714", "name": "松原"}, {"id": "716", "name": "通化"}, {"id": "718", "name": "延边"}], "pname": "吉林"}, {"clist": [{"id": "724", "name": "鞍山"}, {"id": "727", "name": "本溪"}, {"id": "729", "name": "大连"}, {"id": "730", "name": "丹东"}, {"id": "735", "name": "抚顺"}, {"id": "736", "name": "阜新"}, {"id": "739", "name": "葫芦岛"}, {"id": "1345", "name": "锦州"}, {"id": "744", "name": "盘锦"}, {"id": "722", "name": "沈阳"}, {"id": "746", "name": "铁岭"}, {"id": "749", "name": "兴城"}, {"id": "750", "name": "营口"}], "pname": "辽宁"}, {"clist": [{"id": "778", "name": "固原"}, {"id": "781", "name": "石嘴山"}, {"id": "777", "name": "银川"}, {"id": "783", "name": "中卫"}], "pname": "宁夏"}, {"clist": [{"id": "787", "name": "格尔木"}, {"id": "785", "name": "西宁"}], "pname": "青海"}, {"clist": [{"id": "1342", "name": "滨州"}, {"id": "808", "name": "德州"}, {"id": "809", "name": "东营"}, {"id": "813", "name": "菏泽"}, {"id": "816", "name": "胶州"}, {"id": "817", "name": "即墨"}, {"id": "805", "name": "济南"}, {"id": "818", "name": "济宁"}, {"id": "820", "name": "莱芜"}, {"id": "821", "name": "莱阳"}, {"id": "822", "name": "莱州"}, {"id": "823", "name": "聊城"}, {"id": "826", "name": "龙口"}, {"id": "828", "name": "平度"}, {"id": "829", "name": "青岛"}, {"id": "833", "name": "日照"}, {"id": "836", "name": "寿光"}, {"id": "837", "name": "泰安"}, {"id": "838", "name": "滕州"}, {"id": "839", "name": "潍坊"}, {"id": "840", "name": "威海"}, {"id": "843", "name": "烟台"}, {"id": "847", "name": "枣庄"}, {"id": "849", "name": "招远"}, {"id": "851", "name": "淄博"}, {"id": "1687", "name": "临沂"}], "pname": "山东"}, {"clist": [{"id": "855", "name": "长治"}, {"id": "857", "name": "大同"}, {"id": "862", "name": "侯马"}, {"id": "865", "name": "晋城"}, {"id": "866", "name": "晋中"}, {"id": "854", "name": "太原"}, {"id": "874", "name": "阳泉"}, {"id": "878", "name": "运城"}], "pname": "山西"}, {"clist": [{"id": "792", "name": "安康"}, {"id": "793", "name": "宝鸡"}, {"id": "796", "name": "汉中"}, {"id": "798", "name": "商洛"}, {"id": "799", "name": "铜川"}, {"id": "800", "name": "渭南"}, {"id": "791", "name": "西安"}, {"id": "801", "name": "咸阳"}, {"id": "803", "name": "延安"}, {"id": "1763", "name": "榆林"}], "pname": "陕西"}, {"clist": [{"id": "882", "name": "巴中"}, {"id": "880", "name": "成都"}, {"id": "885", "name": "达州"}, {"id": "886", "name": "德阳"}, {"id": "887", "name": "都江堰"}, {"id": "888", "name": "峨眉山"}, {"id": "892", "name": "广汉"}, {"id": "895", "name": "江油"}, {"id": "898", "name": "乐山"}, {"id": "900", "name": "泸州"}, {"id": "903", "name": "眉山"}, {"id": "901", "name": "绵阳"}, {"id": "904", "name": "南充"}, {"id": "905", "name": "内江"}, {"id": "906", "name": "攀枝花"}, {"id": "910", "name": "遂宁"}, {"id": "912", "name": "西昌"}, {"id": "914", "name": "宜宾"}, {"id": "915", "name": "自贡"}, {"id": "916", "name": "资阳"}], "pname": "四川"}, {"clist": [{"id": "918", "name": "拉萨"}], "pname": "西藏"}, {"clist": [{"id": "932", "name": "昌吉"}, {"id": "936", "name": "伊犁"}, {"id": "938", "name": "喀什"}, {"id": "942", "name": "石河子"}, {"id": "926", "name": "乌鲁木齐"}], "pname": "新疆"}, {"clist": [{"id": "952", "name": "保山"}, {"id": "953", "name": "楚雄"}, {"id": "954", "name": "大理"}, {"id": "956", "name": "迪庆"}, {"id": "958", "name": "红河"}, {"id": "950", "name": "昆明"}, {"id": "961", "name": "丽江"}, {"id": "960", "name": "临沧"}, {"id": "964", "name": "曲靖"}, {"id": "966", "name": "思茅"}, {"id": "969", "name": "西双版纳"}, {"id": "971", "name": "玉溪"}, {"id": "4966", "name": "芒市"}], "pname": "云南"}, {"clist": [{"id": "978", "name": "富阳"}, {"id": "975", "name": "慈溪"}, {"id": "976", "name": "东阳"}, {"id": "977", "name": "奉化"}, {"id": "979", "name": "海宁"}, {"id": "974", "name": "杭州"}, {"id": "980", "name": "湖州"}, {"id": "982", "name": "建德"}, {"id": "983", "name": "江山"}, {"id": "981", "name": "嘉兴"}, {"id": "984", "name": "金华"}, {"id": "986", "name": "兰溪"}, {"id": "989", "name": "临安"}, {"id": "988", "name": "临海"}, {"id": "990", "name": "丽水"}, {"id": "991", "name": "龙泉"}, {"id": "992", "name": "宁波"}, {"id": "993", "name": "平湖"}, {"id": "994", "name": "衢州"}, {"id": "995", "name": "瑞安"}, {"id": "996", "name": "上虞"}, {"id": "997", "name": "绍兴"}, {"id": "998", "name": "嵊州"}, {"id": "1355", "name": "台州"}, {"id": "999", "name": "桐乡"}, {"id": "1000", "name": "温岭"}, {"id": "1001", "name": "温州"}, {"id": "1003", "name": "义乌"}, {"id": "1004", "name": "永康"}, {"id": "1005", "name": "余姚"}, {"id": "1006", "name": "舟山"}, {"id": "1007", "name": "诸暨"}], "pname": "浙江"}]

    return result

@wrap_cache_key()
def __districtListWebByDB(key, timeout, city_id):
    districts = Area.objects.filter(cityid=city_id).all()
    result = [
        {
            'districtname': district.districtname,
            'districtid': str(district.districtid),
        }
    for district in districts]
    return result

def strStrip(str):
    if str:
        return str.strip()

@wrap_cache_key()
def __cinemaWebByDB(key, timeout, cinema_id):
    try:
        cinema = Cinema.objects.get(id=cinema_id)
        cinema_images = __cinemaLogo(cinema_id).all()
        if cinema_images:
            cinema.image_url = __cinemaImageUrl2SizeByLYK(cinema_id, 'logo', cinema_images[0].image_url, '')
        result = {
            'id': str(cinema.id),
            'name': cinema.name,
            'image_url': cinema.image_url,
            'address': cinema.address,
            'telephone': strStrip(cinema.telephone),
            'businesshours': strStrip(cinema.businesshours),
            'weibourl': strStrip(cinema.weibourl),
            'weburl': strStrip(cinema.weburl),
            'longitude_baidu': str(cinema.longitude_baidu),
            'latitude_baidu': str(cinema.latitude_baidu),
            'introduction': strStrip(cinema.introduction),
            }
        if cinema.activity_cinema_set.all():
            now = datetime.datetime.now()
            result['activities'] = []
            for activity_cinema in cinema.activity_cinema_set.all():
                if activity_cinema.activity.status==2 and activity_cinema.activity.starttime <= now < activity_cinema.activity.endtime:
                    result['activities'].append({'id': activity_cinema.activity.id,'description': activity_cinema.activity.description,})
        return result
    except Exception, e:
        pass

def __cinemaHasActivitylistInMovie(movie_id, city_id):
    try:
        now = datetime.datetime.now()
        activity_ids = Activity_Movie.objects.filter(movie__id=movie_id).values_list('activity__id', flat=True)
        activity_ids = Activity.objects.filter(id__in=activity_ids,status=2, starttime__lte=now, endtime__gte=now).values_list('id', flat=True)
        activity_id = activity_ids[0]
        cinema_ids = Activity_Cinema.objects.filter(activity__id=activity_id, cinema__city_id=city_id).values_list('cinema__id', flat=True)
        cinema = Cinema.objects.filter(id__in=cinema_ids).all()[0]
        cinema.activity = Activity.objects.get(id=activity_id)
        return cinema
    except Exception:
        pass

def __cinemaListInMovieAndCity(movie_id, city_id):
    today = date.today()
    oneday = datetime.timedelta(days=1)
    tomorrow = date.today() + oneday
    cinemas = Cinema.objects.filter(filmsession__date__in=[today, tomorrow], filmsession__city__cityid=city_id, filmsession__movie__id=movie_id).distinct()
    return cinemas

@wrap_cache_key()
def __cinemaListInMovieAndCityToday(key, timeout, movie_id, city_id):
    today = date.today()
    cinemas = Cinema.objects.filter(filmsession__date=today, filmsession__city__cityid=city_id, filmsession__movie__id=movie_id)\
                .annotate(num_filmsessions=Count('filmsession')).order_by('-num_filmsessions')
    result = []
    for cinema in cinemas:
        cinemaJson = {'id': str(cinema.id), 'name': cinema.name, 'num_filmsessions': cinema.num_filmsessions, 'districtname': cinema.districtcinema.district.districtname}
        if cinema.activity_cinema_set.all():
            now = datetime.datetime.now()
            cinemaJson['activitylist'] = []
            for activity_cinema in cinema.activity_cinema_set.all():
                if activity_cinema.activity.status==2 and activity_cinema.activity.starttime <= now < activity_cinema.activity.endtime:
                    cinemaJson['activitylist'].append({'iconname': activity_cinema.activity.iconname, 'id': str(activity_cinema.activity.id)})
        result.append(cinemaJson)

    return result

@wrap_cache_key()
def __cinemaListInMovieAndCityTomorrow(key, timeout, movie_id, city_id):
    oneday = datetime.timedelta(days=1)
    tomorrow = date.today() + oneday
    cinemas = Cinema.objects.filter(filmsession__date=tomorrow, filmsession__city__cityid=city_id, filmsession__movie__id=movie_id)\
                .annotate(num_filmsessions=Count('filmsession')).order_by('-num_filmsessions')
    result = [
        {
        'id': str(cinema.id),
        'name': cinema.name,
        'num_filmsessions': cinema.num_filmsessions,
        'districtname': cinema.districtcinema.district.districtname
        }
    for cinema in cinemas]

    return result

def __cinemaInMovieByFeature(movie_id, city_id, feature_id):
    today = date.today()
    oneday = datetime.timedelta(days=1)
    tomorrow = date.today() + oneday
    result = []
    cinemas = Cinema.objects.filter(filmsession__date__in=[today, tomorrow], filmsession__city__cityid=city_id, filmsession__movie__id=movie_id).distinct()
    for cinema in cinemas:
        cinema_features_set = cinema.cinema_features_set.values_list('cinemafeaturetype_id', flat=True)
        if feature_id in cinema_features_set:
            result.append(cinema)
    return result

def __cinemaInMovieByFeatureByCinemaList(cinemaInMovieInCityList, feature_id):
    result = []
    for cinema in cinemaInMovieInCityList:
        cinema_features_set = cinema.cinema_features_set.values_list('cinemafeaturetype_id', flat=True)
        if feature_id in cinema_features_set:
            result.append(cinema)
    return result

def __newsListInMovie(movie_id):
    return Movie_News_List.objects.filter(movie_id=movie_id).order_by('-index')

def __cinemaInMovieBySeatByCinemaList(cinemaInMovieInCityList):
    result = []
    return result

def __filmsessionlistByMovieAndCinemaList(movie_id, cinema_id_list, date):
    result = []
    now = datetime.datetime.now()
    for cinema_id in cinema_id_list:
        filmsessions = Filmsession_hall.objects.filter(cinema__id=cinema_id, movie__id=movie_id, date=date) # showtime__gte=now
        if not filmsessions:
            filmsessions = Filmsession.objects.filter(cinema__id=cinema_id, movie__id=movie_id, date=date) # showtime__gte=now
        if filmsessions:
            result.extend(list(filmsessions.all()))
    result = sorted(result, key=lambda filmsession: filmsession.showtime)
    return result

def __jsonFilmsession_Contrast(filmsessionlist):
    result = {}
    if filmsessionlist:
        result = {
            'sessionlist':[
                {
                    'showtime': __timeformat2(filmsession.showtime),
                    'cinemaname': __strNameCut(filmsession.cinema.name, 13),
                    'hallnum': filmsession.hall_num or ' ',
                    'sessiontype': ''.join([filmsession.language_version or ' ', filmsession.screening_mode or ' ']),
                    'price': filmsession.price or ' ',
                    'played': __isPlayed(filmsession.showtime)
                    # todo 活动类型
                }
        for filmsession in filmsessionlist]}
    return result

@wrap_cache_key()
def __jsonFilmsessions_movie_cinemas_Contrast(key, timeout, movie_id, cinema_id_list, pdate):
    jsonResult = {}
    filmsessionlist = __filmsessionlistByMovieAndCinemaList(movie_id, cinema_id_list, pdate)
    if filmsessionlist:
        jsonResult = __jsonFilmsession_Contrast(filmsessionlist)
    return jsonResult

@wrap_cache_key()
def __cinemaFilmsessions(key, timeout, cinema_id, keyword):
    today = date.today()
    if keyword:
        filmsessions = Filmsession_hall.objects.filter(date=today, cinema__id=cinema_id, movie__title__contains=keyword)
        if not filmsessions:
            filmsessions = Filmsession.objects.filter(date=today, cinema__id=cinema_id, movie__title__contains=keyword)
    else:
        filmsessions = Filmsession_hall.objects.filter(date=today, cinema__id=cinema_id)
        if not filmsessions:
            filmsessions = Filmsession.objects.filter(date=today, cinema__id=cinema_id)
    result = {
        'filmsessions':[
            {
            'movie_title': session.movie.title or '',
            'movie_mins': session.movie.mins or '',
            'movie_language': session.movie.language or '',
            'showtime': __timeformat2(session.showtime) or '',
            'price': session.price or '',
            'hallnum': session.hall_num or '',
        }
        for session in filmsessions]
    }
    return result

@wrap_cache_key()
def __jsonCinemaMapInfo(key, timeout, cinema_id):
    cinema = __cinemaByDB(cinema_id)
    result = {}
    if cinema:
        cinema_images = __cinemaLogo(cinema.id).all()
        cinema_logo = ''
        if cinema_images:
            cinema_logo = __cinemaImageUrl2SizeByLYK(cinema.id, 'logo', cinema_images[0].image_url, '')
        result = {
            'cinema_name': cinema.name,
            'cinema_tel': cinema.telephone,
            'cinema_addr': cinema.address,
            'cinema_logo': cinema_logo,
            'cinema_lon': cinema.longitude_baidu,
            'cinema_lat': cinema.latitude_baidu,
        }
    return result

def __userLogin(request, email, pwd):
    if email and pwd:
        try:
            pwdMD5 = hashlib.md5(pwd).hexdigest().upper()
            clientuser = ClientUser.objects.get(email=email, password=pwdMD5)
            request.session['clientuser'] = clientuser
        except Exception:
            pass

def __userRegister(request, email, pwd):
    if email and pwd:
        try:
            clientuser = ClientUser.objects.get(email=email)
            result = {'status': 500, 'msg' : '该邮箱已被注册'}
            return json.dumps(result, cls=DjangoJSONEncoder, sort_keys=True)
        except Exception:
            prefix = random26Str(5)
            username = ''.join(['乐影客', random36Str(6)])
            pwdMD5 = hashlib.md5(pwd).hexdigest().upper()
            try:
                clientuser = ClientUser(email=email, password=pwdMD5, prefix=prefix, username=username, source='web')
                clientuser.save()
                request.session['clientuser'] = clientuser
                result = {'status' : 200}
                return json.dumps(result, cls=DjangoJSONEncoder, sort_keys=True)
            except Exception, e:
                pass
    result = {'status': 500, 'msg' : '注册失败，请重试'}
    return json.dumps(result, cls=DjangoJSONEncoder, sort_keys=True)

@wrap_cache_key()
def __activitiesInCityByMovie(key, timeout, city_id):
    result = []
    try:
        now = datetime.datetime.now()
        city = City.objects.get(cityid=city_id)
        activitylist = city.activity_set.filter(status=2, starttime__lte=now, endtime__gte=now).order_by('-index').all()
        for activity in activitylist:
            if activity.activity_movie_set.all():
                activity.starttime = __timeformat7(activity.starttime)
                activity.endtime = __timeformat7(activity.endtime)
                result.append({'id': activity.id, 'title': activity.title, 'image_url': __activity_imageurl(activity.image_url), 'starttime': activity.starttime, 'endtime': activity.endtime, 'description': activity.description, 'url': activity.url})
    except Exception:
        pass
    return result

@wrap_cache_key()
def __activitiesInCityByCinema(key, timeout, city_id):
    result = []
    try:
        now = datetime.datetime.now()
        city = City.objects.get(cityid=city_id)
        activitylist = city.activity_set.filter(status=2, starttime__lte=now, endtime__gte=now).order_by('-index').all()
        for activity in activitylist:
            if activity.activity_cinema_set.all():
                activity.starttime = __timeformat7(activity.starttime)
                activity.endtime = __timeformat7(activity.endtime)
                result.append({'id': activity.id, 'title': activity.title, 'image_url': __activity_imageurl(activity.image_url), 'starttime': activity.starttime, 'endtime': activity.endtime, 'description': activity.description, 'url': activity.url})
    except Exception:
        pass
    return result

@wrap_cache_key()
def __activitiesInCity(key, timeout, city_id):
    result = []
    try:
        now = datetime.datetime.now()
        city = City.objects.get(cityid=city_id)
        activitylist = city.activity_set.filter(status=2, starttime__lte=now, endtime__gte=now).order_by('-index').all()
        for activity in activitylist:
            activity.starttime = __timeformat7(activity.starttime)
            activity.endtime = __timeformat7(activity.endtime)
            result.append({'id': activity.id, 'title': activity.title, 'image_url': __activity_imageurl(activity.image_url), 'starttime': activity.starttime, 'endtime': activity.endtime, 'description': activity.description, 'url': activity.url})
    except Exception:
        pass
    return result

def __activity_imageurl(image_url):
    if image_url.startswith('http://') or image_url.startswith('/'):
        return image_url

def __movieNews_ImgUrl(img_url):
    if img_url:
        return '%s' % img_url

@wrap_cache_key()
def __activityListByWeb(key, timeout, city_id):
    result = []
    try:
        now = datetime.datetime.now()
        city = City.objects.get(cityid=city_id)
        activitylist = city.activity_set.filter(status=2, starttime__lte=now, endtime__gte=now).order_by('-index').all()
        for activity in activitylist:
            activity.starttime = __timeformat7(activity.starttime)
            activity.endtime = __timeformat7(activity.endtime)
            result.append({'id': activity.id, 'title': activity.title, 'image_url': __activity_imageurl(activity.image_url), 'starttime': activity.starttime, 'endtime': activity.endtime, 'description': activity.description, 'url': activity.url})
    except Exception, e:
        pass
    return result

def __getIntPlatformByStr(platform):
    for client_platform in LYK_CLIENT_PLATFORM:
        if platform == client_platform[1]:
            return client_platform[0]

@wrap_cache_key()
def __getClientVersion(key, timeout, platform):
    result = ''
    clientversions = ClientVersion.objects.filter(platform=__getIntPlatformByStr(platform), status=1).order_by('-client_version').all()
    if clientversions:
        result = clientversions[0].dl_url.url
    return {'url': result}

# 首页页码
def __getHomePage(list, pagenum, pagecount):
    start = 0
    end = len(list)
    hasnext = None
    hasprev = None
    if end > pagecount:
        if pagenum:
            if '1' == pagenum:
                hasnext = 2
                end = pagecount - 1
            else:
                hasprev = int(pagenum) - 1
                start = (int(pagenum) - 1)*(pagecount - 2)+1
                end = int(pagenum)*(pagecount - 2)+1
                if end < len(list) - 1:
                    hasnext = int(pagenum) + 1
                elif end == len(list) - 1:
                    end = len(list)
        else:
            hasnext = 2
            end = pagecount - 1
    result = {
        'hasnext': hasnext,
        'hasprev': hasprev,
        'start': start,
        'end': end,
        'pagenumlist': [i for i in range(start + 1, end + 1)]
    }
    return result

# 根据城市id获取正在热映影片列表
@wrap_cache_key()
def __hotsList_web(key, timeout, city_id):
    movies = __hotsList(city_id)
    result = [
        {
            'id': str(movie.id),
        }
    for movie in movies]

    return result

# 根据城市id获取即将上映影片列表
@wrap_cache_key()
def __willsList_web(key, timeout, city_id):
    movies = __willsList(city_id)
    result = [
        {
        'id': str(movie.id),
        }
    for movie in movies]

    return result

@wrap_cache_key()
def __filmsession_today_web_ByDB(key, timeout, cinema_id):
    filmsessions_today = __filmsession_hall_today_ByDB(cinema_id).all()
    if not filmsessions_today:
        filmsessions_today = __filmsession_today_ByDB(cinema_id).all()
    result = [
        {
            'movie_title': session.movie.title,
            'movie_mins': session.movie.mins,
            'movie_language': session.movie.language,
            'showtime': __timeformat2(session.showtime),
            'price': session.price,
            'hallnum': __strNameCut(session.hall_num, 6),
        }
    for session in filmsessions_today]

    return result

def __moviecount_by_cinema_web_ByDB(filmsessions_today):
    result = {}
    if filmsessions_today:
        for session in filmsessions_today:
            if not result.get(session['movie_title'], None):
                result[session['movie_title']] = 1
    return len(result)

def __getStrScoreSourceById(scoreSourceId):
    for scoreSource in LYK_MEDIA_SCORESOURCE_CHOICES:
        if scoreSourceId == scoreSource[0]:
            return scoreSource[1]

def __movieScores(movie, type):
    result = []
    list = movie.movie_score_set.all()
    if type == 'all':
        return list
    else:
        for score in list:
            if score.mediachannel.scoresource == type:
                result.append(score)
        return result

def __movieScore(list):
    count = 0
    totalscore = 0
    for score in list:
        if score.score:
            totalscore += float(score.score)
            count += 1
    if count:
        return '%.1f' % float(totalscore/count)
    return 0.0

@wrap_cache_key()
def __MovieMessageByWeb(key, timeout, movie_id, city_id):
    movie = __movieByDB(movie_id)
    # 海报
    movie.poster_image_url = __movie_Horizon_Poster(movie)
    # 预告片
    movie_trailers = movie.movie_trailers_set.all()
    for trailer in movie_trailers:
        trailer.image_url = __imageUrl2SizeByLYK(movie.id, 'trailers', trailer.image_url, '')
    # 剧照
    movie_stills = movie.movie_stills_set.all()
    for still in movie_stills:
        still.image_url = __imageUrl2SizeByLYK(movie.id, 'stills', still.image_url, 's')
    # 发布时间
    movie.pubdate = __timeformat3(movie.pubdate)
    # 专业影评
    movie_pro_scores = __movieScores(movie, 1)
    # 用户影评
    movie_user_scores = __movieScores(movie, 0)
    movie.score = __movieScore(__movieScores(movie, 'all'))
    movie_cinecisms = movie.movie_cinecisms_set.all().order_by('-pubtime')
    # 全部排场数
    movie.sessionscount = __lastFilmsessionInMovieByDB(movie_id, city_id).count()
    movie.sessionlist = __lastFilmsessionInMovieByDB(movie_id, city_id).all()
#    # 剩余排场数
#    movie.sessionscountlast = __lastFilmsessionInMovieByDB(movie_id, city_id).filter(showtime__gte=datetime.datetime.now()).count()
#    # 最近一场排场
#    if __lastFilmsessionInMovieByDB(movie_id, city_id).filter(showtime__gte=datetime.datetime.now()).all():
#        movie.lastsession = __lastFilmsessionInMovieByDB(movie_id, city_id).filter(showtime__gte=datetime.datetime.now()).all()[0]
#        movie.lastsession.showtime = __timeformat2(movie.lastsession.showtime)

    # 此影片此城市的影院列表
    cinemaInMovieInCityList = __cinemaListInMovieAndCity(movie_id, city_id)
    # 最多排场影院
    movie.cinemascount = __mostSessionsInMovieByCinemaList(cinemaInMovieInCityList).count()
    if movie.cinemascount:
        movie.cinema_mostsession = __mostSessionsInMovieByCinemaList(cinemaInMovieInCityList)[0]
    # 有活动的影院
    cinemaHasActivity = __cinemaHasActivitylistInMovie(movie_id, city_id)
    if cinemaHasActivity:
        movie.cinema_activity = cinemaHasActivity
    # 有停车位的影院
    cinemalistByPark = __cinemaInMovieByFeatureByCinemaList(cinemaInMovieInCityList, 7)
    if cinemalistByPark:
        movie.cinemacountByPark = len(cinemalistByPark)
        movie.cinemaByPark = cinemalistByPark[0]
    # 可选座的影院
    cinemalistBySeat = __cinemaInMovieBySeatByCinemaList(cinemaInMovieInCityList)
    if cinemalistBySeat:
        movie.cinemacountBySeat = len(cinemalistBySeat)
        movie.cinemaBySeat = cinemalistBySeat[0]
    # 有3D/IMAX厅：
    cinemalistBy3DorIMAX = __cinemaInMovieByFeatureByCinemaList(cinemaInMovieInCityList, 1)
    if cinemalistBy3DorIMAX:
        movie.cinemacountBy3D = len(cinemalistBy3DorIMAX)
        movie.cinemaBy3D = cinemalistBy3DorIMAX[0]
    # 电影报道：
    newsList = __newsListInMovie(movie_id)

    result = {}
    if movie:
        result['id'] = str(movie.id)
        result['title'] = movie.title
        result['score'] = movie.score
        result['pubdate'] = movie.pubdate
        result['directors'] = movie.directors
        result['actors'] = movie.actors
        result['filmtype'] = movie.filmtype
        result['mins'] = movie.mins
        result['certification'] = movie.certification
        result['language'] = movie.language
        result['plots'] = movie.plots
        result['poster_image_url'] = movie.poster_image_url
    if movie_trailers:
        result['movie_trailers'] = [{'video_url': trailer.video_url, 'image_url': trailer.image_url} for trailer in movie_trailers]
        result['movie_trailers_count'] = len(movie_trailers)
    if movie_stills:
        result['movie_stills'] = [still.image_url for still in movie_stills]
        result['movie_stills_count'] = len(movie_stills)
    if movie_pro_scores:
        result['movie_pro_scores_count'] = len(movie_pro_scores)
        result['movie_pro_scores'] = [
            {
                'medianame': score.mediachannel.medianame,
                'scoresource': __getStrScoreSourceById(score.mediachannel.scoresource),
                'mediaicon': 'http://115.182.92.238/%s' % score.mediachannel.mediaicon,
                'score': score.score or '未评分'
            }
        for score in movie_pro_scores]
    if movie_user_scores:
        result['movie_user_scores_count'] = len(movie_user_scores)
        result['movie_user_scores'] = [
            {
            'medianame': score.mediachannel.medianame,
            'scoresource': __getStrScoreSourceById(score.mediachannel.scoresource),
            'mediaicon': 'http://115.182.92.238/%s' % score.mediachannel.mediaicon,
            'score': score.score or '未评分'
        }
        for score in movie_user_scores]
    if movie_cinecisms:
        result['movie_cinecisms'] = [
            {
            'username': __getUserName(cinecism),
            'pubtime': __timeformat6(cinecism.pubtime),
            'content': cinecism.content,
            'portrait_url': __getPortraitUrlByCinecism(cinecism)
        }
        for cinecism in movie_cinecisms]
    try:
        result['sessionscount'] = movie.sessionscount
    except Exception:
        pass
    try:
        result['sessionlist'] = [{
            'cinema_id': str(session.cinema.id),
            'cinema_name': session.cinema.name,
            'showtime': __timeformat(session.showtime),
            }for session in movie.sessionlist]
    except Exception:
        pass
    try:
        result['cinemascount'] = movie.cinemascount
    except Exception:
        pass
    try:
        result['cinema_mostsession'] = {
            'id': str(movie.cinema_mostsession.id),
            'name': movie.cinema_mostsession.name,
            'num_filmsessions': movie.cinema_mostsession.num_filmsessions,
        }
    except Exception:
        pass
    try:
        result['cinema_activity'] = {
            'id': str(movie.cinema_activity.id),
            'name': movie.cinema_activity.name,
            'activity_title': movie.cinema_activity.activity.title,
        }
    except Exception:
        pass
    try:
        result['cinemacountByPark'] = movie.cinemacountByPark
        result['cinemaByPark'] = {
            'id': str(movie.cinemaByPark.id),
            'name': movie.cinemaByPark.name,
        }
    except Exception:
        pass
    try:
        result['cinemacountBySeat'] = movie.cinemacountBySeat
        result['cinemaBySeat'] = {
            'id': str(movie.cinemaBySeat.id),
            'name': movie.cinemaBySeat.name,
        }
    except Exception:
        pass
    try:
        result['cinemacountBy3D'] = movie.cinemacountBy3D
        result['cinemaBy3D'] = {
            'id': str(movie.cinemaBy3D.id),
            'name': movie.cinemaBy3D.name,
            }
    except Exception:
        pass
    try:
        result['newslist'] = [
            {
            'title': news.title,
            'url': news.url,
            'img_url': __movieNews_ImgUrl(news.img_url),
            'content': news.content or '',
            }
        for news in newsList[:3]]
    except Exception:
        pass

    return result
#    return movie

def getMovieMessageSessionsCountLast(result):
    sessionlist = result.get('sessionlist', None)
    if sessionlist:
        count = 0
        for session in sessionlist:
            showtime = session['showtime']
            showtimeDate = __timformat8(showtime)
            if showtimeDate > datetime.datetime.now():
                count += 1
        result['sessionscountlast'] = str(count)
    else:
        result['sessionscountlast'] = str(0)

@wrap_cache_key()
def __clientuserJson(key, timeout, clientuser):
    if clientuser:
        clientuser = ClientUser.objects.get(id=clientuser.id)
        result = {
            'id': str(clientuser.id),
            'nickname': clientuser.username,
        }
        if clientuser.userphonenumber_set.all():
            result['phonenumber'] = clientuser.userphonenumber_set.order_by('-id').all()[0].phonenumber

        if clientuser.portrait_imgurl:
            result['portrait_imgurl'] = ''.join(['http://115.182.92.236/media/', clientuser.portrait_imgurl.url])
        return result

@wrap_cache_key()
def __favorCinemaListJson(key, timeout, clientuser_id):
    favorlist = __favorlistByDB(clientuser_id, '0')
    result = [__favorCinemas(favor, '', '') for favor in favorlist]
    return result

@wrap_cache_key()
def __userActivityListJson(key, timeout, clientuser_id):
    useractivitylist = __userActivityListByDB(clientuser_id)
    result = [__userActivity2Json(useractivity) for useractivity in useractivitylist]
    return result

def __userMovieLife(favorcinemalist, useractivitylist):
    orilist = []
    orilist.extend(favorcinemalist)
    orilist.extend(useractivitylist)
    result = {'list1':[], 'list2':[], 'list3': [], 'list4': []}
    orilist = sorted(orilist, key=lambda object: object['updatetime'], reverse=True)
    count = 0
    for object in orilist:
        if object.get('title', None):
            object['fallstype'] = 'activity'
        elif object.get('name', None):
            object['fallstype'] = 'cinema'
        object['updatetime'] = __timeformat7(__timformat8(object['updatetime']))
        if count % 4 == 0:
            result['list1'].append(object)
        elif count % 4 == 1:
            result['list2'].append(object)
        elif count % 4 == 2:
            result['list3'].append(object)
        else:
            result['list4'].append(object)
        count += 1

    return result

@wrap_cache_key()
def __hasFeatureOfCinema(key, timeout, cinema_id):
    features = {'cinema_features': [], 'typelist': []}
    try:
        cinema = Cinema.objects.get(id=cinema_id)
        cinema_features = cinema.cinema_features_set.all()
        for cinema_feature in cinema_features:
            features['typelist'].append(cinema_feature.cinemafeaturetype.type)
            features['cinema_features'].append({'type': cinema_feature.cinemafeaturetype.type, 'content': cinema_feature.content})
    except Exception:
        pass
    return features

def __featureTypeIDOfCinema(cinema_id):
    features = []
    try:
        cinema = Cinema.objects.get(id=cinema_id)
        cinema_features = cinema.cinema_features_set.all()
        for cinema_feature in cinema_features:
            features.append(str(cinema_feature.cinemafeaturetype.id))
    except Exception:
        pass
    return features

def __activityByDB(act_id):
    try:
        activity = Activity.objects.get(id=act_id)
        return activity
    except Exception:
        pass

def __activityType(activity):
    act_id = activity.id
    activity_cinemas = Activity_Cinema.objects.filter(activity_id=act_id).all()
    activity_movies = Activity_Movie.objects.filter(activity_id=act_id).all()
    if activity_movies:
        return u'电影'
    elif activity_cinemas:
        return u'影院'

@wrap_cache_key()
def __activityByWeb(key, timeout, act_id):
    activity = __activityByDB(act_id)
    result = {}
    if activity:
        result = {
            'title': activity.title,
            'image_url': __activity_imageurl(activity.image_url),
            'introduction': activity.introduction,
            'description': activity.description,
            'starttime': __timeformat7(activity.starttime),
            'endtime': __timeformat7(activity.endtime),
            'citylist': [city.cityname
            for city in activity.citys.order_by('-index', 'pinyin').all()],
            'type': __activityType(activity),
        }
    return result








#=================== controller ================================
@wrap_cache()
def __homeinfo(request, city_id):
    lon = request.GET.get('lon', None)
    lat = request.GET.get('lat', None)
    sid = getSid(request)

    result = {'respond': {'status': 200, 'code': 0, 'msg': 'success'}}
    if sid:
        result['sid'] = sid
        result['username'] = __randomUserName(sid)
        # 影院列表
    cinemas = __cinemasOrderByDistance(lat, lon, '', city_id)
    result['total_cinema'] = len(cinemas)
    # 按照活动多少排序，倒序
    cinemas = __sortCinemasByActivityCount(cinemas)
    # 按照index倒序
    cinemas = __sortCinemasByIndex(cinemas)
    # 最近的排场信息
    result['pins'] = [__cinemaByHome(cinema) for cinema in cinemas]
    result['pins'] = __cinemasHasSessionOrAct(result['pins'])

    # 活动列表
    activities = __activitiesInCityByDB(city_id)
    result['activities'] = [__activityByHome(activity) for activity in activities]

    return json.dumps(result, cls=DjangoJSONEncoder, sort_keys=True)

@wrap_cache()
def __movieinfo(request, movie_id):
    movieinfo = __movieByDB(movie_id)
    activities = __activitiesInMovieByDB(movie_id).all()
    applications = __applicationsInMovieByDB(movie_id).all()

    if movieinfo:
        result = {
            'respond': {'status': 200, 'code': 0, 'msg': 'success'},
            'movieinfo': {
                'title': movieinfo.title,
                'id': str(movieinfo.id),
                'poster_image_url': __imageUrl2SmallByMtime(movieinfo.poster_image_url),
                'directors': movieinfo.directors,
                'actors': movieinfo.actors,
                'mins': movieinfo.mins,
                'score': movieinfo.score,
                'plots': movieinfo.plots,
                'trailers':[{
                    'p_image_url': trailer.image_url,
                    'p_video_url': trailer.video_url
                }for trailer in movieinfo.movie_trailers_set.all()[0:10]],
                'cinecisms':[{
                    'c_username': cinecism.username,
                    'c_pubtime': __timeformat(cinecism.pubtime),
                    'c_content': cinecism.content
                }for cinecism in movieinfo.movie_cinecisms_set.order_by('-pubtime').all()[0:10]]
            },
            }
        # 剧照封装
        __encap_movie_stills(result, movieinfo.movie_stills_set)

        if activities:
            result['activities'] = [__activityInfo2Json(activity) for activity in activities]
        if applications:
            result['applications'] = [__appInfo2Json(application) for application in applications]
    else:
        result = {'respond': {'status': 500, 'code': 20101, 'msg': '暂时没有数据'}}

    return json.dumps(result, cls=DjangoJSONEncoder, sort_keys=True)

@wrap_cache()
def __moviecinecismlist(request, movie_id):
    page = request.GET.get('page', '1')
    perpage = request.GET.get('perpage', '10')
    cinecisms = __movie_cinecismsListByDB(movie_id).all()

    if cinecisms:
        try:
            paginator = Paginator(cinecisms, int(perpage))
            count = paginator.count # 总数
            cinecisms = paginator.page(int(page))
            nextpage = cinecisms.has_next()
            result = {
                'respond': {'status': 200, 'code': 0, 'msg': 'success'},
                'nextpage': nextpage,
                'page': page,
                'cinecisms': [{
                    'username': cinecism.username,
                    'pubtime': __timeformat(cinecism.pubtime),
                    'content': cinecism.content
                }for cinecism in cinecisms]
            }
        except (EmptyPage, InvalidPage, PageNotAnInteger):
            result = {'respond': {'status': 500, 'code': 20101, 'msg': '暂时没有数据'}}
    else:
        result = {'respond': {'status': 500, 'code': 20101, 'msg': '暂时没有数据'}}

    return json.dumps(result, cls=DjangoJSONEncoder, sort_keys=True)

def __moviecinecismpub(request, movie_id, username, content):
    __movie_cinecismpub2DB(movie_id, username, content)
    result = {'respond': {'status': 200, 'code': 0, 'msg': 'success'}}
    return json.dumps(result, cls=DjangoJSONEncoder, sort_keys=True)

@wrap_cache()
def __cinemaInfoById(request, cinema_id):
    cinemainfo = __cinemaByDB(cinema_id)
    if cinemainfo:
        result = {
            'respond': {'status': 200, 'code': 0, 'msg': 'success'},
            'cinemainfo': {
                'onsale': cinemainfo.onsale,
                'id': str(cinemainfo.id),
                'name': cinemainfo.name,
                'score': cinemainfo.score,
                'address': cinemainfo.address,
                'lon': str(cinemainfo.longitude),
                'lat': str(cinemainfo.latitude),
                'tel': cinemainfo.telephone,
                'roadline': cinemainfo.roadline,
                }
        }
        # 用户是否关注
        __cinemaHasFavor(result['cinemainfo'], cinemainfo.id, request)
        # 封装影院Feature
        __encapCinemaFeature(result['cinemainfo'], cinemainfo.description)
        # 影院的活动列表
        activityList = __activitiesInCinemaByDB(cinemainfo.id)
        if activityList:
            result['activities'] = [__activityInfo2Json(activity) for activity in activityList.all()]
            # 影院的应用列表
        if cinemainfo.application_set.all():
            result['applications'] = [__appInfo2Json(application) for application in cinemainfo.application_set.all()]
    else:
        result = {'respond': {'status': 500, 'code': 20101, 'msg': '暂时没有数据'}}

    return json.dumps(result, cls=DjangoJSONEncoder, sort_keys=True)

@wrap_cache()
def __moviesOfCity(request, city_id):
    movies = __moviesList(city_id)
    result = {
        'respond': {'status': 200, 'code': 0, 'msg': 'success'},
        'movies': [__movieInfo2Json(movie) for movie in movies]
    }
    return json.dumps(result, cls=DjangoJSONEncoder, sort_keys=True)

@wrap_cache()
def __hotsOfCity(request, city_id):
    page = request.GET.get('page', '1')
    perpage = request.GET.get('perpage', '10')
    hot = __hotsList(city_id)
    will = __willsList(city_id)
    total = {'hot_total': hot.count(), 'will_total': will.count()}
    result = pageOfMovies(hot.all(), perpage, page, total)
    print_DB_SQL()
    return json.dumps(result, cls=DjangoJSONEncoder, sort_keys=True)

@wrap_cache()
def __willsOfCity(request, city_id):
    page = request.GET.get('page', '1')
    perpage = request.GET.get('perpage', '10')
    hot = __hotsList(city_id)
    will = __willsList(city_id)
    total = {'hot_total': hot.count(), 'will_total': will.count()}
    result = pageOfMovies(will.all(), perpage, page, total)

    return json.dumps(result, cls=DjangoJSONEncoder, sort_keys=True)

@wrap_cache()
def __cinemasOfCity(request, city_id, movie_id):
    bought = request.GET.get('bought', None)
    areaid = request.GET.get('areaid', None)
    lon = request.GET.get('lon', None)
    lat = request.GET.get('lat', None)
    page = request.GET.get('page', '1')
    perpage = request.GET.get('perpage', '10')
    haveCondition = request.GET.get('condi')

    nextpage = False

    # 影院列表
    cinemas = __cinemasOrderByDistance(lat, lon, areaid, city_id)
    # 按照活动多少排序，倒序
    cinemas = __sortCinemasByActivityCount(cinemas)
    # 按照index倒序
    cinemas = __sortCinemasByIndex(cinemas)

    if bought: # 已购买
        cinemas = __cinemaInBought(request, list(cinemas)) # TODO

    if movie_id:
        cinemas = cinemaListByMovie(cinemas, movie_id)

    try:
        paginator = Paginator(list(cinemas), int(perpage))
        count = paginator.count # 总数
        cinemas = paginator.page(int(page))
        nextpage = cinemas.has_next()
        result = {'respond': {'status': 200, 'code': 0, 'msg': 'success'},
                  'nextpage': nextpage,
                  'page': page,
                  'cinemas': [__cinemaInfo(cinemaMap, movie_id) for cinemaMap in cinemas]}
    except (EmptyPage, InvalidPage, PageNotAnInteger):
        result = {'respond': {'status': 500, 'code': 20101, 'msg': '暂时没有数据'}}

        # 选择条件
    if haveCondition:
        conditions = __conditionsInCityByDB(city_id) # TODO
        result['conditions'] = []
        if conditions:
            for condition in conditions:
                if condition.key == 'bought':
                    result['conditions'].append({"key": condition.key,
                                                 "name": condition.name,
                                                 "sub": [
                                                         {
                                                         "name": condition.name,
                                                         "value": True
                                                     }
                                                 ]})
                if condition.key == 'areaid':
                    districts = __districtListByDB(city_id).all()
                    result['conditions'].append({
                        "key": condition.key,
                        "name": condition.name,
                        "sub": [
                            {
                            "name": district.districtname,
                            "value": district.districtid
                        }
                        for district in districts]
                    })
                    # 其他条件...
        else:
            result['conditions'] = __conditions4cinema_default(city_id)

    return json.dumps(result, cls=DjangoJSONEncoder, sort_keys=True)

@wrap_cache()
def __citylist(request):
    cities = __citylistByDB()
    result = {
        'respond': {'status': 200, 'code': 0, 'msg': 'success'},
        'areas': [
            {
            'cityid': city[0],
            'cityname': city[1],
            }
        for city in cities]
    }
    return json.dumps(result, cls=DjangoJSONEncoder, sort_keys=True)

@wrap_cache()
def __filmsessionbymovie(request, cinema_id, movie_id):
    filmsessions = __filmsession_hallByDB(cinema_id).all()
    if not filmsessions:
        filmsessions = __filmsessionByDB(cinema_id).all()
    cinema = __cinemaByDB(cinema_id)

    movie_index = 0
    totalList = []
    movie_idMap = {}
    oneday = datetime.timedelta(days=1)
    tomorrow = date.today() + oneday
    for filmsession in filmsessions:
        f_movie_id = filmsession.movie.id
        f_showday = filmsession.showtime
        if f_movie_id not in movie_idMap:
            totalList.append({'movie':filmsession.movie, 'today':[], 'tomorrow':[]})
            movie_idMap[f_movie_id] = True
            if movie_id == str(f_movie_id):
                movie_index = len(totalList)-1
        if f_showday.date() < tomorrow:
            totalList[len(totalList)-1]['today'].append(filmsession)
        else:
            totalList[len(totalList)-1]['tomorrow'].append(filmsession)
    result = {
        'respond': {'status': 200, 'code': 0, 'msg': 'success'},
        'movie_index': movie_index,
        'filmsessions': [
            {
            'movie_info': {
                'movie_id': str(filmsessions['movie'].id),
                'movie_title': filmsessions['movie'].title,
                'poster_image_url': __imageUrl2SmallByMtime(filmsessions['movie'].poster_image_url),
                },
            'today_sessions': [
                {
                'showtime': __timeformat2(filmsession.showtime),
                'timestamp': __timeformat4(filmsession.showtime),
                'id': str(filmsession.id),
                'price': filmsession.price,
                'language_version': filmsession.language_version or '',
                'screening_mode': filmsession.screening_mode or '',
                'hall_num': filmsession.hall_num or '',
                }
            for filmsession in filmsessions['today']],
            'tomorrow_sessions': [
                {
                'showtime': __timeformat2(filmsession.showtime),
                'timestamp': __timeformat4(filmsession.showtime),
                'id': str(filmsession.id),
                'price': filmsession.price,
                'language_version': filmsession.language_version or '',
                'screening_mode': filmsession.screening_mode or '',
                'hall_num': filmsession.hall_num or '',
                }
            for filmsession in filmsessions['tomorrow']],
            }
        for filmsessions in totalList]
    }
    if cinema:
        result['onsale'] = cinema.onsale
    else:
        result['onsale'] = 0

    return json.dumps(result, cls=DjangoJSONEncoder, sort_keys=True)

@wrap_cache()
def __filmsessionbytime(request, cinema_id):
    filmsessions_today = __filmsession_hall_today_ByDB(cinema_id).all()
    filmsessions_tomorrow = __filmsession_hall_tomorrow_ByDB(cinema_id).all()
    if not filmsessions_today:
        filmsessions_today = __filmsession_today_ByDB(cinema_id).all()
    if not filmsessions_tomorrow:
        filmsessions_tomorrow = __filmsession_tomorrow_ByDB(cinema_id).all()
    cinema = __cinemaByDB(cinema_id)

    result = {
        'respond': {'status': 200, 'code': 0, 'msg': 'success'},
        'today_sessions':[
            {
            'showtime': __timeformat2(filmsession.showtime),
            'timestamp': __timeformat4(filmsession.showtime),
            'id': str(filmsession.id),
            'price': filmsession.price,
            'language_version': filmsession.language_version or '',
            'screening_mode': filmsession.screening_mode or '',
            'movie_title': filmsession.movie.title or '',
            }
        for filmsession in filmsessions_today],
        'tomorrow_sessions':[
            {
            'showtime': __timeformat2(filmsession.showtime),
            'timestamp': __timeformat4(filmsession.showtime),
            'id': str(filmsession.id),
            'price': filmsession.price,
            'language_version': filmsession.language_version or '',
            'screening_mode': filmsession.screening_mode or '',
            'movie_title': filmsession.movie.title or '',
            }
        for filmsession in filmsessions_tomorrow],
        }

    if cinema:
        result['onsale'] = cinema.onsale
    else:
        result['onsale'] = 0

    return json.dumps(result, cls=DjangoJSONEncoder, sort_keys=True)

@wrap_cache()
def __activityList(request, city_id, movie_id):
    page = request.GET.get('page', '1')
    perpage = request.GET.get('perpage', '10')
    haveCondition = request.GET.get('condi')

    if movie_id:
        activityList = __activitiesInCityAndMovieByDB(city_id, movie_id)
    else:
        activityList = __activitiesInCityByDB(city_id)
    if activityList:
        try:
            paginator = Paginator(list(activityList), int(perpage))
            count = paginator.count # 总数
            activityList = paginator.page(int(page))
            nextpage = activityList.has_next()
            result = {
                'respond': {'status': 200, 'code': 0, 'msg': 'success'},
                'activities': [__activityInfo2Json(activity) for activity in activityList],
                'nextpage': nextpage,
                'page': page,
                }
        except (EmptyPage, InvalidPage, PageNotAnInteger):
            result = {'respond': {'status': 500, 'code': 20101, 'msg': '暂时没有数据'}}
    else:
        result = {'respond': {'status': 500, 'code': 20101, 'msg': '暂时没有数据'}}

    if haveCondition:
        cities = __citiesHasActivity()
        movies = __moviesHasActivity(city_id)
        result['conditions'] = {
            'cities': [
                {
                'id': str(city.cityid),
                'name': city.cityname,
                }
            for city in cities],
            }
        result['conditions']['movies'] = [{'id': '', 'title': '全部'}]
        for movie in movies:
            result['conditions']['movies'].append({'id': str(movie.id), 'title': movie.title,})

    return json.dumps(result, cls=DjangoJSONEncoder, sort_keys=True)

@wrap_cache()
def __activityInfo(request, act_id):

    activity = __activityByDB(act_id)
    if activity:
        result = {'respond': {'status': 200, 'code': 0, 'msg': 'success'},
                  'activity': __activityInfo2Json(activity)}
    else:
        result = {'respond': {'status': 500, 'code': 20101, 'msg': '暂时没有数据'}}

    return json.dumps(result, cls=DjangoJSONEncoder, sort_keys=True)

@wrap_cache()
def __applicationList(request):
    page = request.GET.get('page', '1')
    perpage = request.GET.get('perpage', '10')

    applications = __applicationByDB()
    if applications:
        try:
            paginator = Paginator(list(applications), int(perpage))
            count = paginator.count # 总数
            applicationList = paginator.page(int(page))
            nextpage = applicationList.has_next()
            result = {
                'respond': {'status': 200, 'code': 0, 'msg': 'success'},
                'applications': [__appInfo2Json(application) for application in applicationList],
                'nextpage': nextpage,
                'page': page,
                }
        except (EmptyPage, InvalidPage, PageNotAnInteger):
            result = {'respond': {'status': 500, 'code': 20101, 'msg': '暂时没有数据'}}
    else:
        result = {'respond': {'status': 500, 'code': 20101, 'msg': '暂时没有数据'}}

    return json.dumps(result, cls=DjangoJSONEncoder, sort_keys=True)

def __favorupdate(request, sid, ftype, status, data):

    __favor2DB(sid, ftype, status, data)
    result = {'respond': {'status': 200, 'code': 0, 'msg': 'success'}}
    return json.dumps(result, cls=DjangoJSONEncoder, sort_keys=True)

@wrap_cache()
def __favorlist(request, sid, ftype):
    page = request.GET.get('page', '1')
    perpage = request.GET.get('perpage', '10')
    lon = request.GET.get('lon', None)
    lat = request.GET.get('lat', None)

    favorlist = __favorlistByDB(sid, ftype)
    try:
        paginator = Paginator(favorlist, int(perpage))
        count = paginator.count # 总数
        favors = paginator.page(int(page))
        nextpage = favors.has_next()
        result = {
            'respond': {'status': 200, 'code': 0, 'msg': 'success'},
            'nextpage': nextpage,
            'page': page,
            'cinemas': [__favorCinemas(favor, lon, lat) for favor in favors]
        }
    except (EmptyPage, InvalidPage, PageNotAnInteger):
        result = {'respond': {'status': 500, 'code': 20101, 'msg': '暂时没有数据'}}


    return json.dumps(result, cls=DjangoJSONEncoder, sort_keys=True)

@wrap_cache()
def __activityListByUser(request, sid):
    page = request.GET.get('page', '1')
    perpage = request.GET.get('perpage', '10')

    useractivitylist = __userActivityListByDB(sid)
    try:
        paginator = Paginator(useractivitylist, int(perpage))
        count = paginator.count # 总数
        useractivities = paginator.page(int(page))
        nextpage = useractivities.has_next()
        result = {
            'respond': {'status': 200, 'code': 0, 'msg': 'success'},
            'nextpage': nextpage,
            'page': page,
            'activities': [__userActivity2Json(useractivity) for useractivity in useractivities]
        }
    except (EmptyPage, InvalidPage, PageNotAnInteger):
        result = {'respond': {'status': 500, 'code': 20101, 'msg': '暂时没有数据'}}

    return json.dumps(result, cls=DjangoJSONEncoder, sort_keys=True)

def __useractivity_add(request, sid, activity_id):
    __useractivity2DB(sid, activity_id)
    result = {'respond': {'status': 200, 'code': 0, 'msg': 'success'}}
    return json.dumps(result, cls=DjangoJSONEncoder, sort_keys=True)

@wrap_cache()
def __userInfo(request, sid):
    clientuser = __userInfoByDB(sid)
    if clientuser:
        result = {
            'respond': {'status': 200, 'code': 0, 'msg': 'success'},
            'userinfo': {
                'username': clientuser.username,
                # TODO 积分
                'point': '0',
                }
        }
        if clientuser.userphonenumber_set.all():
            result['userinfo']['phonenum'] = clientuser.userphonenumber_set.order_by('-id').all()[0].phonenumber
    else:
        result = {'respond': {'status': 500, 'code': 20101, 'msg': '暂时没有数据'}}
    return json.dumps(result, cls=DjangoJSONEncoder, sort_keys=True)

def __userUpdate(request, sid, username, phonenum, code):
    image = None
    if 'portrait' in request.FILES:
        image = request.FILES["portrait"]

    result = __userUpdate2DB(sid, username, phonenum, image, code)

    return json.dumps(result, cls=DjangoJSONEncoder, sort_keys=True)

def __phonebind(sid, phonenum, code):
    clientuser = __userInfoByDB(sid)
    if clientuser and phonenum and code:
        cachecode = rediscache.get(phonenum)
        if cachecode == code:
            try:
                UserPhoneNumber.objects.get(phonenumber=phonenum, clientuser=clientuser)
            except Exception:
                userphonenumber = UserPhoneNumber(phonenumber=phonenum, clientuser=clientuser)
                userphonenumber.save()
            rediscache.delete(phonenum)
            return 1
        else:
            return 0

def __sendPhoneNumCode(request, phonenum):
    ret = {'result': 'success'}
    try:
        code = random10Str(4)

        rediscache.lpush('phonenumcode', '-'.join([phonenum, code]))
    except Exception, e:
        ret = {'result': 'error01'}

    return json.dumps(ret, cls=DjangoJSONEncoder, sort_keys=True)






if __name__ == '__main__':
    print 1

#    now = datetime.datetime.now()
#    onehour = datetime.timedelta(hours=1)
#    favorcinemalist = [{'updatetime': __timeformat(now), 'lon': '123'},{'updatetime': __timeformat(now+onehour), 'lon': '234'},]
#    time.sleep(5)
#    now = datetime.datetime.now()
#    useractivitylist = [{'updatetime': __timeformat(now), 'cdkey': '222'},{'updatetime': __timeformat(now+onehour), 'cdkey': '333'},]
#    print __userMovieLife(favorcinemalist, useractivitylist)

#    result = ['1','2','3','4','5','6']
#    result.insert(0, 'a')
#    print result
#    sss =  u'&amp;quot;泰&amp;quot;'
#    print HTMLParser().unescape(sss)
#    hotsList_web = __hotsList_web('web_hotslist_290', 60*60, '290')
#    willsList_web = __willsList_web('web_willlist_290', 60*60, '290')
#    hots = []
#    wills = []
#    for hot in hotsList_web :
#        hots.append(hot['id'])
#    for will in willsList_web :
#        wills.append(will['id'])
#    print hots
#    print wills
#    cinemaList = []
#    for cinema in cinemaList:
#        print 1
#    hotmovies = __hotsList_web(''.join(['web_hotslist_', '290']), 60*60, '290')
#    for hotmmm in hotmovies:
#        del_cacheBykey(''.join(['web_hotinfo_home_',hotmmm['id'],'_','290']))
    del_cacheBykey('web_hotinfo_home_511_290')
    del_cacheBykey('web_moviemessage_511_290')
#    print __citylistByPinyin('', '')
#    movie_name_mode = '敢死队2（4D)'
#    mode = re.search(r'[（(][\s\S]*[)）]',movie_name_mode).group()
#    print 'mode:%s'% mode
#    movie_name = movie_name_mode.replace(mode, '')#re.sub(mode,'',movie_name_mode)
#    print movie_name
    #    ss = 'sfsdfsd'
#    clientuser_id = ss.split('-')[-1]
#    print clientuser_id
#    rediscache = redis.StrictRedis(host='10.200.92.237', port=6379, db=2)
#    movie = Movie.objects.get(id=207)
#    rediscache.set('testobj', movie)
#    redisMovie = rediscache.get('testobj')
#    print redisMovie
#    cinemaInMovieInCityList = __cinemaListInMovieAndCity('207', '290')[0]
#    print_DB_SQL()
#    cityname = u'北京市'
#    cityname = cityname[:cityname.rfind(u'市')]
#    cityname = cityname[:cityname.rfind(u'自治')]
#    print cityname
#    cityname = cityname.replace('市', '', -1)
#    print cityname
#    print cityname[::-1].replace(u'市', '', 1)[::-1]
    #    cinema = Cinema.objects.get(id='2083')
    #    desc = cinema.description
    #    j = json.loads(desc)
    #    print json.dumps(j, sort_keys=True)
    #    list = Area.objects.all().values_list('cityid', flat=True).distinct()
    #    print list
    #    city_id = '290'
    #    __cinemasOfCity(city_id)
    #    print len(__moviesList(city_id))
    #    cinemas = __cinemasOrderByDistance('', '', '', '290')
    #    cinema = cinemas[0]
    #    print cinema.id
    #    filmsessions = cinema.filmsession_set.order_by('showtime').all()
    #    for filmsession in filmsessions:
    #        print filmsession.movie.title, filmsession.showtime
    #    city_id = '290'
    #    movie_id = '2'
    #    __activitiesInCityAndMovieByDB(city_id, movie_id)
    #    prefix = random24Str(5)
    #    clientuser = ClientUser(prefix=prefix)
    #    clientuser.save()
    #    print '-'.join([prefix, str(clientuser.id)])
    #    url = 'http://img1.mtime.com/pi/d/2009/44/20091029183020.70225474.jpg'
    #    if url.find('_') > -1:
    #        suff = url.split('.')[-1]
    #        perfix = url[0: url.rfind('_')]
    #        print ''.join([perfix, '.', suff])
    #    else:
    #        print url
    #    cities = __citiesHasActivity().all()
    #    for city in cities:
    #        print city.cityid,city.cityname
    #    print_DB_SQL()
#    cinema = Cinema.objects.get(id='2061')
#    clon = cinema.longitude
#    clat = cinema.latitude
#    lon = '116.396010'
#    lat = '40.002834'
#    #    print float('116.396000')
#    #    print pow(sin((float(lat)-float(clat))*pi/360),2)
#    d = (6378137*2*asin(sqrt(pow(sin((float(lat)-float(clat))*pi/360),2)+cos(float(lat)*pi/180)*cos(float(clat)*pi/180)*pow(sin((float(lon)-float(clon))*pi/360),2))))/1000
#    print d
    #    __distanceByCinema(cinema, '116.396000', '40.002830')