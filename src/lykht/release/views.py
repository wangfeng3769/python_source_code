# -*- coding:utf8 -*-

import re
import os
from django.contrib import messages
from django.http import HttpResponse
from django.http import HttpResponseRedirect
from django.shortcuts import render_to_response
from django.template import RequestContext
from django.contrib.auth.decorators import login_required
from backends.models import User_Cinema
from backends.models import Filmsession_hall
from backends.models import Cinema
from backends.models import Activity_Cinema
import datetime


#@login_required
def index(request):
    day_delta = request.REQUEST.get('delta','0')
    day_int = int(day_delta)
#    cinema  = User_Cinema.objects.get(user=request.user).cinema
    cinema = Cinema.objects.get(id=1)
    today = datetime.datetime.today()
    filmsession0 = Filmsession_hall.objects.filter(cinema=cinema,date=today)
    filmsession1 = Filmsession_hall.objects.filter(cinema=cinema,date=today+datetime.timedelta(days=1))
    print(today+datetime.timedelta(days=1))
    filmsession2 = Filmsession_hall.objects.filter(cinema=cinema,date=today+datetime.timedelta(days=2))
    filmsession3 = Filmsession_hall.objects.filter(cinema=cinema,date=today+datetime.timedelta(days=3))
    filmsession4 = Filmsession_hall.objects.filter(cinema=cinema,date=today+datetime.timedelta(days=4))
    filmsession5 = Filmsession_hall.objects.filter(cinema=cinema,date=today+datetime.timedelta(days=5))
    filmsession6 = Filmsession_hall.objects.filter(cinema=cinema,date=today+datetime.timedelta(days=6))
    movie_list = list(set([one.movie for one in filmsession0]))
    movie_name_showtimes = []
    for movie in movie_list:
        showtimes = []
        for one in Filmsession_hall.objects.filter(movie=movie,date=today+datetime.timedelta(days=day_int)):
            showtimes.append(one.showtime)
        movie_name_showtimes.append({'movie_name': movie.name,'showtimes':showtimes })
    activitys = Activity_Cinema.objects.filter(cinema = cinema)

    return render_to_response('release/index.html',locals(),context_instance= RequestContext(request))