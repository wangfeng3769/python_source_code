# -*- coding: utf-8 -*-
import json
import datetime
from django.core.serializers.json import DjangoJSONEncoder
from django.http import HttpResponseBadRequest, HttpResponse, HttpResponseRedirect
from django.shortcuts import render_to_response
from django.views.decorators.http import last_modified, require_POST
from lykweb.controller import __hotsList_web, __movieInfoByWebHome, __movieByDB, __cinemaByDB, __imageUrl2SizeByLYK, __timeformat3, __willsList_web, __getCityId, __hasFeatureOfCinema, __HotMovieInfoByWebHome, __lastFilmsessionInMovieByDB, __timeformat2, __mostSessionsInMovieByDB, __filmsession_hall_today_ByDB, __filmsession_today_ByDB, __cinemaImages, __cinemaLogo, __cinemaImageUrl2SizeByLYK, __districtListByDB, __cinemasListInCity, __filmsessionsInCity, __strNameCut, __sortCinemaListBySessioncount, __movielistInCinema, __movie_Horizon_Poster, __citylistByPinyin, __cinemaHasActivitylistInMovie, __cinemaInMovieByFeature, __cinemaListInMovieAndCity, __mostSessionsInMovieByCinemaList, __cinemaInMovieByFeatureByCinemaList, __cinemaInMovieBySeatByCinemaList, __cinemaListInMovieAndCityToday, __timeformat5, __cinemaListInMovieAndCityTomorrow, __filmsessionlistByMovieAndCinemaList, __jsonFilmsession_Contrast, __jsonCinemaMapInfo, __userLogin, __userRegister, __activityList, __activitiesInCityByMovie, __activitiesInCityByCinema, __getClientVersion, __movieInfoByWeb, __cinemaWebByDB, __filmsession_today_web_ByDB, __moviecount_by_cinema_web_ByDB, __MovieMessageByWeb, __jsonFilmsessions_movie_cinemas_Contrast, __timeformat, __favor2DB, __clientuserJson, __favorCinemaListJson, __userActivityListJson, __activityListByWeb, __districtListWebByDB, __cinemaListByWeb, __cinemaListHome2ByWeb, __cinemasCountInCity, __userUpdate, __isFavorCinemaByClientUser, __cinemasSearch, __hotCinemaListByWebHome, __userMovieLife, __activityByWeb, __sendPhoneNumCode, __phonebind, __cinemaFilmsessions, __activitiesInCity, __getHomePage, getLastSession, getMovieMessageSessionsCountLast, print_DB_SQL
from lykweb.demo_data import movie_FeaturesOfCinema, getMoviePosterUrl
from lykweb_data.models import ClientUser
from webtool import atom_att
from lykweb_data.cache import lastmodified, del_cacheBykey


# 网站首页
#@last_modified(lastmodified)
def home_info(request):
    pagenum = request.GET.get('page', None)
    cityJson = __getCityId('', 60*60, request)
    city_id = cityJson['cityid']
    city_name =cityJson['cityname']
    hotmovies = __hotsList_web(''.join(['web_hotslist_', city_id]), 60*60, city_id)
    page = __getHomePage(hotmovies, pagenum, 25)
    hotlist = [__HotMovieInfoByWebHome(''.join(['web_hotinfo_home_', movie['id'], '_', city_id]), 60*60, movie['id'], city_id) for movie in hotmovies][page['start']:page['end']]
    intHotMoviePageNum = 0
    for hotMoviePageNum in hotlist:
        hotMoviePageNum['pagenum'] = page['pagenumlist'][intHotMoviePageNum]
        intHotMoviePageNum += 1
        getLastSession(hotMoviePageNum)
    hotlistcount = len(hotlist)
    hotcount = len(hotmovies)
    hotlastcount = [i+len(hotlist)+1+page['start'] for i in range(25 - len(hotlist))]
    willmovies = __willsList_web(''.join(['web_willlist_',city_id]), 60*60, city_id)
    willlist = [__movieInfoByWebHome(''.join(['web_willinfo_home_', movie['id'], '_', city_id]), 60*60, movie['id'], city_id) for movie in willmovies][:23]
#    hotcinemas = __hotCinemaListByWebHome(''.join(['web_hotcinemalistbyhome_',city_id]), 60*60, city_id)[0:4]
#    if not hotcinemas:
    cinemaList = __cinemaListByWeb(''.join(['web_hotcinemalist_',city_id]), 60*60, city_id)[0:8]
    cinemaList = __cinemaListHome2ByWeb(cinemaList)
    citylist = __citylistByPinyin('web_citypinyinlist', 60*60)
    activitylist = __activityListByWeb(''.join(['web_activitylist_',city_id]), 60*60, city_id)
    activitycount = len(activitylist)
    cinemacount = __cinemasCountInCity(''.join(['web_cinemascount_',city_id]), 60*60, city_id)['cinemacount']
    clientuser = request.session.get('clientuser', None)
    print_DB_SQL()
    return render_to_response('index.html', locals())

# 影院列表
#@last_modified(lastmodified)
def cinema_list(request):
    cityJson = __getCityId('', 60*60, request)
    city_id = cityJson['cityid']
    city_name =cityJson['cityname']
    cinemalist = __cinemasListInCity(''.join(['web_cinemalist_',city_id]), 60*60, city_id)
    hotmovies = __hotsList_web(''.join(['web_hotslist_', city_id]), 60*60, city_id)
    hotlist = [__movieInfoByWeb(''.join(['web_hotinfo_', movie['id'], '_', city_id]), 60*60, movie['id'], city_id) for movie in hotmovies]
    hotcount = len(hotlist)
    willmovies = __willsList_web(''.join(['web_willlist_',city_id]), 60*60, city_id)
    willlist = [__movieInfoByWeb(''.join(['web_willinfo_', movie['id'], '_', city_id]), 60*60, movie['id'], city_id) for movie in willmovies]
    hotcinemas = __cinemaListByWeb(''.join(['web_hotcinemalist_',city_id]), 60*60, city_id)[0:8]
    filmsessioncount = __filmsessionsInCity(''.join(['web_filmsessioncount_',city_id]), 60*60, city_id)['filmsessioncount']
    districts = __districtListWebByDB(''.join(['web_districtlist_',city_id]), 60*60, city_id)
    citylist = __citylistByPinyin('web_citypinyinlist', 60*60)
    activitylist = __activityListByWeb(''.join(['web_activitylist_',city_id]), 60*60, city_id)
    activitycount = len(activitylist)
    cinemacount = __cinemasCountInCity(''.join(['web_cinemascount_',city_id]), 60*60, city_id)['cinemacount']
    clientuser = request.session.get('clientuser', None)
    return render_to_response('CinemaList.html', locals())

# 影院详细页
#@last_modified(lastmodified)
def cinema_info(request, cinema_id):
    cityJson = __getCityId('', 60*60, request)
    city_id = cityJson['cityid']
    city_name =cityJson['cityname']
    cinema = __cinemaWebByDB(''.join(['web_cinemainfo_', cinema_id]), 60*60, cinema_id)

    features = __hasFeatureOfCinema(''.join(['web_features_', cinema_id]), 60*60, cinema_id)
    featurescount = len(features['typelist'])
    filmsessions_today = __filmsession_today_web_ByDB(''.join(['web_filmsessions_today_', cinema_id]), 60*60, cinema_id)

    moviecount = __moviecount_by_cinema_web_ByDB(filmsessions_today)
    hotmovies = __hotsList_web(''.join(['web_hotslist_', city_id]), 60*60, city_id)
    hotlist = [__movieInfoByWeb(''.join(['web_hotinfo_', movie['id'], '_', city_id]), 60*60, movie['id'], city_id) for movie in hotmovies]
    hotcount = len(hotlist)
    willmovies = __willsList_web(''.join(['web_willlist_',city_id]), 60*60, city_id)
    willlist = [__movieInfoByWeb(''.join(['web_willinfo_', movie['id'], '_', city_id]), 60*60, movie['id'], city_id) for movie in willmovies]
    hotcinemas = __cinemaListByWeb(''.join(['web_hotcinemalist_',city_id]), 60*60, city_id)[0:8]
    citylist = __citylistByPinyin('web_citypinyinlist', 60*60)
    activitylist = __activityListByWeb(''.join(['web_activitylist_',city_id]), 60*60, city_id)
    activitycount = len(activitylist)
    cinemacount = __cinemasCountInCity(''.join(['web_cinemascount_',city_id]), 60*60, city_id)['cinemacount']
    clientuser = request.session.get('clientuser', None)
    isfavor = __isFavorCinemaByClientUser(cinema_id, clientuser)
    return render_to_response('CinemaMessage.html', locals())

# 影片详细页
#@last_modified(lastmodified)
def movie_info(request, movie_id):
    cityJson = __getCityId('', 60*60, request)
    city_id = cityJson['cityid']
    city_name =cityJson['cityname']
    movie = __MovieMessageByWeb(''.join(['web_moviemessage_', movie_id, '_', city_id]), 60*60, movie_id, city_id)
    getMovieMessageSessionsCountLast(movie)
    getLastSession(movie)

    hotmovies = __hotsList_web(''.join(['web_hotslist_', city_id]), 60*60, city_id)
    hotlist = [__movieInfoByWeb(''.join(['web_hotinfo_', hotmovie['id'], '_', city_id]), 60*60, hotmovie['id'], city_id) for hotmovie in hotmovies]
    hotcount = len(hotlist)
    willmovies = __willsList_web(''.join(['web_willlist_',city_id]), 60*60, city_id)
    willlist = [__movieInfoByWeb(''.join(['web_willinfo_', willmovie['id'], '_', city_id]), 60*60, willmovie['id'], city_id) for willmovie in willmovies]
    hotcinemas = __cinemaListByWeb(''.join(['web_hotcinemalist_',city_id]), 60*60, city_id)[0:8]
    # 城区列表
    districts = __districtListWebByDB(''.join(['web_districtlist_',city_id]), 60*60, city_id)
    citylist = __citylistByPinyin('web_citypinyinlist', 60*60)

    # 今天的影院
    cinemalistByToday = __cinemaListInMovieAndCityToday(''.join(['web_cinemalistByToday_', movie_id, '_', city_id]), 60*60, movie_id, city_id)
    # 明天的影院
    cinemalistByTomorrow = __cinemaListInMovieAndCityTomorrow(''.join(['web_cinemalistByTomorrow_', movie_id, '_', city_id]), 60*60, movie_id, city_id)
    today = __timeformat5(datetime.date.today())
    oneday = datetime.timedelta(days=1)
    tomorrow = __timeformat5(datetime.date.today() + oneday)
    todaystr = __timeformat3(datetime.date.today())
    tomorrowstr = __timeformat3(datetime.date.today() + oneday)
    activitylist = __activityListByWeb(''.join(['web_activitylist_',city_id]), 60*60, city_id)
    activitycount = len(activitylist)
    cinemacount = __cinemasCountInCity(''.join(['web_cinemascount_',city_id]), 60*60, city_id)['cinemacount']
    clientuser = request.session.get('clientuser', None)
    return render_to_response('MovieMessage.html', locals())

# header
#@last_modified(lastmodified)
def header(request):
    cityJson = __getCityId('', 60*60, request)
    city_id = cityJson['cityid']
    city_name =cityJson['cityname']
    hotmovies = __hotsList_web(''.join(['web_hotslist_', city_id]), 60*60, city_id)
    hotcount = len(hotmovies)
    clientuser = request.session.get('clientuser', None)
    return render_to_response('header.html', locals())

def activity_list(request):
    cityJson = __getCityId('', 60*60, request)
    city_id = cityJson['cityid']
    city_name =cityJson['cityname']
    hotmovies = __hotsList_web(''.join(['web_hotslist_', city_id]), 60*60, city_id)
    hotcount = len(hotmovies)
    cinemacount = __cinemasCountInCity(''.join(['web_cinemascount_',city_id]), 60*60, city_id)['cinemacount']
    activitylistByMovie = __activitiesInCityByMovie(''.join(['web_activitylistByMovie_',city_id]), 60*60, city_id)
    activitylistByCinema = __activitiesInCityByCinema(''.join(['web_activitylistByCinema_',city_id]), 60*60, city_id)
#    activitylistByCity = __activitiesInCity(''.join(['web_activitylistByCity_',city_id]), 60*60, city_id)
    citylist = __citylistByPinyin('web_citypinyinlist', 60*60)
    clientuser = request.session.get('clientuser', None)
    return render_to_response('activitylist.html', locals())

def activity_info(request, act_id):
    cityJson = __getCityId('', 60*60, request)
    city_id = cityJson['cityid']
    city_name =cityJson['cityname']
    activityinfo = __activityByWeb(''.join(['web_activityinfo_', act_id]), 60*60, act_id)
    hotmovies = __hotsList_web(''.join(['web_hotslist_', city_id]), 60*60, city_id)
    hotcount = len(hotmovies)
    cinemacount = __cinemasCountInCity(''.join(['web_cinemascount_',city_id]), 60*60, city_id)['cinemacount']
    activitylist = __activityListByWeb(''.join(['web_activitylist_',city_id]), 60*60, city_id)
    activitycount = len(activitylist)
    citylist = __citylistByPinyin('web_citypinyinlist', 60*60)
    clientuser = request.session.get('clientuser', None)
    return render_to_response('atv_inner.html', locals())

def filmsession_contrast(request):
    movie_id = request.GET.get('movie_id', None)
    cinema_ids = request.GET.get('cinema_ids', None)
    pdate = request.GET.get('date', None)
    if movie_id and cinema_ids and pdate:
        cinema_id_list = cinema_ids.split(',')
        jsonResult = __jsonFilmsessions_movie_cinemas_Contrast(''.join(['web_filmsession_contrast_', movie_id, cinema_ids, pdate]), 60*60, movie_id, cinema_id_list, pdate)
        return HttpResponse(json.dumps(jsonResult, cls=DjangoJSONEncoder, sort_keys=True), mimetype='application/json')
    return HttpResponse(json.dumps([]), mimetype='application/json')

def cinema_search_ajax(request):
    city_id = request.GET.get('city_id', None)
    keyword = request.GET.get('keyword', None)
    feature_ids = request.GET.get('feature_ids', None)
    jsonResult = __cinemasSearch(''.join(['web_cinemalist_', city_id, keyword, feature_ids]), 60*60, city_id, keyword, feature_ids)
    return HttpResponse(json.dumps(jsonResult, cls=DjangoJSONEncoder, sort_keys=True), mimetype='application/json')

def cinema_filmsessions_ajax(request):
    cinema_id = request.GET.get('id', None)
    keyword = request.GET.get('keyword', None)
    if cinema_id and keyword:
        jsonResult = __cinemaFilmsessions(''.join(['web_cinema_filmsessions_', cinema_id, keyword]), 60*60, cinema_id, keyword)
        return HttpResponse(json.dumps(jsonResult, cls=DjangoJSONEncoder, sort_keys=True), mimetype='application/json')
    return HttpResponseBadRequest(json.dumps({}), mimetype='application/json')

# 根据影院id获取地图信息
def cinema_mapinfo(request):
    cinema_id = request.GET.get('id', None)
    if cinema_id:
        jsonResult = __jsonCinemaMapInfo(''.join(['web_cinema_mapinfo_', cinema_id]), 60*60, cinema_id)
        return HttpResponse(json.dumps(jsonResult, cls=DjangoJSONEncoder, sort_keys=True), mimetype='application/json')
    return HttpResponseBadRequest(json.dumps({}), mimetype='application/json')

def user_login(request):
    email = request.POST.get('email', None)
    pwd = request.POST.get('password', None)
    if email and pwd:
        __userLogin(request, email, pwd)
    return HttpResponseRedirect(request.META.get('HTTP_REFERER', '/'))

def user_logout(request):
    del(request.session['clientuser'])
    return HttpResponseRedirect(request.META.get('HTTP_REFERER', '/'))

def user_registerinit(request):
    cityJson = __getCityId('', 60*60, request)
    city_id = cityJson['cityid']
    city_name =cityJson['cityname']
    hotmovies = __hotsList_web(''.join(['web_hotslist_', city_id]), 60*60, city_id)
    hotcount = len(hotmovies)
    citylist = __citylistByPinyin('web_citypinyinlist', 60*60)
    request.session['register_from'] = request.META.get('HTTP_REFERER', '/')
    return render_to_response('Register.html', locals())

def user_register(request):
    email = request.POST.get('email', None)
    pwd = request.POST.get('password', None)
    if email and pwd:
        __userRegister(request, email, pwd)
    return HttpResponseRedirect(request.session['register_from'])

def user_favorupdate(request):
    clientuser = request.session.get('clientuser', None)
    cinema_id = request.GET.get('id', None)
    ftype = request.GET.get('ftype', None)
    op = request.GET.get('op', None)
    loveid = request.GET.get('loveid', None)
    if not clientuser:
        return HttpResponse(json.dumps({'result': 'error01'}, cls=DjangoJSONEncoder, sort_keys=True), mimetype='application/json')
    if cinema_id and ftype and op:
        try:
            sid = str(clientuser.id).split('-')[-1]
            __favor2DB(sid, ftype, op, cinema_id)
            # 清缓存
            if ftype == '0':
                del_cacheBykey(''.join(['web_favorcinemalist_', str(clientuser.id)]))
                del_cacheBykey(''.join(['web_isfavorcinema_', cinema_id, str(clientuser.id)]))
            return HttpResponse(json.dumps({'result': 'success', 'loveid': loveid}), mimetype='application/json')
        except Exception:
            pass
    return HttpResponse(json.dumps({'result': 'error02'}), mimetype='application/json')

def user_info(request):
    cityJson = __getCityId('', 60*60, request)
    city_id = cityJson['cityid']
    city_name =cityJson['cityname']
    hotmovies = __hotsList_web(''.join(['web_hotslist_', city_id]), 60*60, city_id)
    hotcount = len(hotmovies)
    citylist = __citylistByPinyin('web_citypinyinlist', 60*60)
    clientuser = request.session.get('clientuser', None)
    if not clientuser:
        return home_info(request)
    clientuserJson = __clientuserJson(''.join(['web_clientuser_', str(clientuser.id)]), 60*60, clientuser)
    favorcinemalist = __favorCinemaListJson(''.join(['web_favorcinemalist_', str(clientuser.id)]), 60*60, str(clientuser.id))
    favorcinemacount = len(favorcinemalist)
    useractivitylist = __userActivityListJson(''.join(['web_useractivitylist_', str(clientuser.id)]), 60*60, str(clientuser.id))
    activitycount = len(useractivitylist)
    usermovielife = __userMovieLife(favorcinemalist, useractivitylist)
    cinemacount = __cinemasCountInCity(''.join(['web_cinemascount_',city_id]), 60*60, city_id)['cinemacount']
    return render_to_response('UserCenter.html', locals())

def user_update(request):
    username = request.POST.get('username', None)
    valid = username or 'portrait' in request.FILES
    clientuser = request.session.get('clientuser', None)
    if not clientuser or not valid:
        return HttpResponseRedirect(request.META.get('HTTP_REFERER', '/'))

    userJson = __userUpdate(request, str(clientuser.id), username, '', '')
    del_cacheBykey(''.join(['web_clientuser_', str(clientuser.id)]))

    return HttpResponseRedirect(request.META.get('HTTP_REFERER', '/'))

def user_phonebind(request):
    phonenum = request.GET.get('phonenum', None)
    code = request.GET.get('code', None)
    clientuser = request.session.get('clientuser', None)
    if not clientuser or not phonenum or not code:
        return HttpResponse(json.dumps({'result': 'error01'}, cls=DjangoJSONEncoder, sort_keys=True), mimetype='application/json')

    if __phonebind(str(clientuser.id), phonenum, code):
        del_cacheBykey(''.join(['web_clientuser_', str(clientuser.id)]))

    return HttpResponse(json.dumps({'result': 'success'}, cls=DjangoJSONEncoder, sort_keys=True), mimetype='application/json')

# 获取手机验证码
#@require_POST
def user_phonenumvaild(request):
    phonenum = request.GET.get('phonenum', None)

    userJson = {'result': 'error01'}
    if not phonenum:
        return HttpResponseBadRequest(json.dumps(userJson), mimetype='application/json')

    userJson = __sendPhoneNumCode(request, phonenum)

    return HttpResponse(userJson, mimetype='application/json')

def client_download(request):
    cityJson = __getCityId('', 60*60, request)
    city_id = cityJson['cityid']
    city_name =cityJson['cityname']
    hotmovies = __hotsList_web(''.join(['web_hotslist_', city_id]), 60*60, city_id)
    hotcount = len(hotmovies)
    citylist = __citylistByPinyin('web_citypinyinlist', 60*60)
    androidurl = __getClientVersion('web_downloadurl_android', 60*60, 'android')['url']
    androidurl = '%s%s' % ('http://115.182.92.238/', androidurl)
    cinemacount = __cinemasCountInCity(''.join(['web_cinemascount_',city_id]), 60*60, city_id)['cinemacount']
    clientuser = request.session.get('clientuser', None)
    return render_to_response('SoftwareLoading.html', locals())

def redirect(request):
    desurl = request.GET.get('url', None)
    if not desurl:
        desurl = '/'
    return HttpResponseRedirect(desurl)



# =============================================================================================================

# 网站首页-demo
#@last_modified(lastmodified)
def home_demo(request):
    pass
#    city_id = __getCityId('web_cityid', 60*60, request)
#    hotmovie_ids = ['97','23','13','62','166','214','213','215','62']
#    hotlist = [__movieInfoByWebHome(movie_id, city_id) for movie_id in hotmovie_ids]
#    hotcount = len(hotlist)
#    cinema_ids = ['2067','2074','2126','2132',]
#    cinemalist = [__cinemaByDB(cinema_id) for cinema_id in cinema_ids]
#    return render_to_response('demo_index.html', locals())

# 影片详细页-demo
#@last_modified(lastmodified)
def movie_demo(request, movie_id):
    pass
#    city_id = __getCityId('web_cityid', 60*60, request)
#    movie = __movieByDB(movie_id)
##    movie.poster_image_url = __imageUrl2SizeByLYK(movie.id, 'poster', movie.poster_image_url, 's')
#    movie.poster_image_url = getMoviePosterUrl(movie_id)
#    movie_trailers = movie.movie_trailers_set.all()[0:9]
#    for trailer in movie_trailers:
#        trailer.image_url = __imageUrl2SizeByLYK(movie.id, 'trailers', trailer.image_url, '')
#    movie_stills = movie.movie_stills_set.all()[0:16]
#    for still in movie_stills:
#        still.image_url = __imageUrl2SizeByLYK(movie.id, 'stills', still.image_url, 's')
#    movie.pubdate = __timeformat3(movie.pubdate)
#    movie_featureOfCinema = movie_FeaturesOfCinema(movie_id)
#    movie_ids = ['97','23','13','166','62','214','213','215']
#    hotlist = [__movieInfoByWebHome(movie_id, city_id) for movie_id in movie_ids]
#    hotcount = len(hotlist)
#    cinema_ids = ['2067','2074','2126','2132','2137','2140','2155','2164']
#    cinemalist = [__cinemaByDB(cinema_id) for cinema_id in cinema_ids]
#    return render_to_response('demo_message.html', locals())