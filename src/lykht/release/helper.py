# -*- coding:utf8 -*-
from backends.models import Filmsession_hall,Activity_Cinema,Cinema
import datetime
def index(day_delta,):
    head_info = []
    day_int = int(day_delta)
    #    cinema  = User_Cinema.objects.get(user=request.user).cinema
    cinema = Cinema.objects.get(id=1)
    today = datetime.datetime.today()
    for i in range(7):
        date= date=today+datetime.timedelta(days=i)
        filmsession = Filmsession_hall.objects.filter(cinema=cinema,date=date)
        head_info.append({'date':date,'count':filmsession.count()})

    date= date=today+datetime.timedelta(days=day_delta)
    filmsessions = Filmsession_hall.objects.filter(cinema=cinema,date=date)
    movie_list = list(set([one.movie for one in filmsessions]))
    movie_name_showtimes = []
    for movie in movie_list:
        showtimes = []
        for one in Filmsession_hall.objects.filter(movie=movie,date=today+datetime.timedelta(days=day_int,cinema =cinema)):
            showtimes.append(one.showtime)
        movie_name_showtimes.append({'movie_name': movie.name,'showtimes':showtimes })
    activitys = Activity_Cinema.objects.filter(cinema = cinema).order_by('-activity__starttime')
    return head_info ,movie_name_showtimes,activitys

