#coding:utf-8

from register1.models import User
from django.http import HttpResponseRedirect
from django.shortcuts import render_to_response
from django.template.response import TemplateResponse
from django.contrib.auth.forms import AuthenticationForm as authentication_form
from django.template import RequestContext
import xlrd,re
from backends.models import Filmsession,Movie,Cinema,Filmsession_hall
from backends.models import  FilmsessionFile
import datetime
from backends.models import City,Cinema
from backends.models import UploadFilmSessionLog

def new(request):
    cityid = request.REQUEST.get('cityid', None)
    city_list = City.objects.all()
    cinema_list = Cinema.objects.filter(city_id=cityid)
    mes_info = {'city_list':city_list,'cinema_list':cinema_list}
    return render_to_response('upload/new.html',mes_info,context_instance= RequestContext(request))

def create(request):
    file_obj = request.FILES.get("file",None)
    platform = request.REQUEST.get('platform',None)
    cinema_id = request.REQUEST.get('cinema_id',None)
    bk = xlrd.open_workbook(file_contents=file_obj.read(),encoding_override='utf-8')
    judge = ''
    for s in bk.sheets():
        print s.name
        cinema_name = s.cell(0,4).value
        show_day = s.cell(0,16).value
        for row in range(s.nrows):
            try:
                show_date1 = s.cell(row,1).value
                if re.search('\d{4}-\d{1,2}-\d{1,2}',show_date1):
                    show_date = show_date1
                hall1 = s.cell(row,5).value
                if re.search(u'厅',hall1):
                    hall =hall1
                movie_name_mode = s.cell(row,7).value
                begin_time1 = s.cell(row,10).value
                if re.search('\d{1,2}:\d{1,2}',begin_time1):
                    begin_time = begin_time1
                end_time1 = s.cell(row,13).value
                if re.search('\d{1,2}:\d{1,2}',end_time1):
                    end_time = end_time1
                price1 = s.cell(row,15).value
                if re.search('\d{1,100}',price1):
                    price = price1
                mins1 = s.cell(row,17).value
                if re.search('\d{1,100}',mins1):
                    mins = mins1
            except:
                pass
            try:
                if show_date and hall and movie_name_mode and begin_time and end_time and price and mins:
                    print show_date
                    print hall
                    print  movie_name_mode
                    print begin_time
                    print end_time
                    print price
                    print mins
                    try:
                        mode = re.search(u'[\（\(][\s\S]*[\)\）]',movie_name_mode).group()
                        print 'mode:%s'% mode
                        movie_name = movie_name_mode.replace(mode,'')
                        mode = mode[1:-1]
                    except Exception,e:
                        print e
                        mode =''
                        movie_name =movie_name_mode

                    try:
                        movie = Movie.objects.get(title = movie_name)
                        print movie
                    except Exception,e:
                        print e
                        movie = Movie.objects.get(title__contains = movie_name)
                    try:
                        cinema = Cinema.objects.get(id = cinema_id)
                    except:
                        cinema = None
                    if show_date != judge:
                        UploadFilmSessionLog.objects.filter(
                            identify = show_date+cinema_id,
#                            upload_time = datetime.datetime.now(),
                            ticket_platform = {'1':'火凤凰','2':'顶新','3':'满天星'}.get(platform,''),
                            show_time = show_date).delete()
                        UploadFilmSessionLog.objects.create(
                            identify = show_date+cinema_id,
                            upload_time = datetime.datetime.now(),
                            ticket_platform = {'1':'火凤凰','2':'顶新','3':'满天星'}.get(platform,''),
                            show_time = show_date)
                        judge = show_date


                    if cinema and movie:
                        FilmsessionFile.objects.filter(
                            language_version = movie.language,
                            screening_mode = mode,
                            showdate = show_date,
                            hall_num=hall,
                            start = begin_time,
                            end = end_time,
                            movie = movie,
                            cinema = cinema,
                            mins = mins,
                            city = cinema.city,
                            price =price,
                            date= datetime.datetime.strptime(show_date, "%Y-%m-%d")).delete()
                        FilmsessionFile.objects.create(
                                        identify = show_date+cinema_id,
                                        language_version = movie.language,
                                        screening_mode = mode,
                                        showdate = show_date,
                                        hall_num=hall,
                                        start = begin_time,
                                        end = end_time,
                                        movie = movie,
                                        cinema = cinema,
                                        mins = mins,
                                        city = cinema.city,
                                        price =price,
                                        date= datetime.datetime.strptime(show_date, "%Y-%m-%d"))

            except Exception,e:
                print  e
                print '100000'
    return render_to_response('upload/move.html',{'all_list': FilmsessionFile.objects.all()},context_instance= RequestContext(request))

def move(request):
    all_list = FilmsessionFile.objects.all()
    for one in all_list :
        showtime = datetime.datetime.strptime(one.showdate+' '+one.start, "%d/%m/%y %H:%M")
        endtime = datetime.datetime.strptime(one.showdate+' '+one.end, "%d/%m/%y %H:%M")
        if endtime<showtime:
            endtime = endtime + datetime.timedelta(days = 1)
        Filmsession_hall.objects.create(
            price =one.price,
            language_verison = one.language_version,
            screening_mod = one.screening_mod,
            date = one.date,
            movie = one.movie,
            cinema = one.cinema,
            hall_num = one.hall_num,
            city = one.city,
            showtime =showtime,
            endtime =endtime
        )
    all_list.delete()

