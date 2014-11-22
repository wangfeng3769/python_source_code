# -*- coding: utf-8 -*-
from datetime import datetime,timedelta
from django.contrib import admin
from backends.models import Cinema, Movie, Activity, Activity_Cinema, Activity_Movie, Condition4Cinema,Movie_Trailers,Movie_Stills,City,Filmsession,Movie_Posters
from backends.models import Movie_Posters,Movie_Bar_Posters,Movie_Stills, Movie_Bar_Stills,Movie_Bar1_Posters
from backends.models import Activity_Bar_Manager
from backends.models import CinemaFeatureType,Cinema_Features
from backends.models import Cinema_bar_Activity,CinemaActivity
from backends.models import UploadFilmSessionLog
from backends.models import AppManager
from backends.models import AppPicture
from backends.models import ActivityType
import lykht.settings as path
from backends.models import Movie_Bar_Trailers
from backends.models import Filmsession_hall
from backends.models import FilmsessionFile
from django.contrib.admin.actions import delete_selected
import os
from backends.models import Movie_News_List


def display(modeladmin,request,queryset):
    queryset.update(status='1')
display.short_description = "上线"
def hide(modeladmin,request,queryset):
    queryset.update(status='0')
hide.short_description = "下线"

class CinemaAdmin(admin.ModelAdmin):
    list_display = ('name', 'address', 'telephone', 'index','score','onsale')
    list_filter = ('city__cityname',)
    list_editable = ('address','telephone', 'index',)
    list_per_page = 200

class PosterInline(admin.StackedInline):
    model = Movie_Bar_Posters

class StillInline(admin.StackedInline):
    model = Movie_Bar_Stills

class TrailerInline(admin.StackedInline):
    model = Movie_Bar_Trailers

class MovieAdmin(admin.ModelAdmin):
    list_display = ('show_id','title','show_status', 'show_pubdate', 'show_trailers', 'show_stills','show_score','index','show_motion','show_status1')
    list_editable = ('index',)
    list_display_links = ['show_motion',]
    actions = [hide,display]
    search_fields = ['movie__id','movie__pubdate',]
    list_filter = [ 'filmtype','area','status','pubdate','age']
    inlines = [PosterInline,StillInline,TrailerInline]
#    filter_horizontal = ['outid']
    list_per_page = 50



    def show_status1(self,obj):
        if obj.status == '0':
            return '已下线'
        elif obj.status == '1':
            return '已上线'
    show_status1.short_description = '状态显示'

    def show_motion(self,obj):
        return '修改'
    show_motion.short_description = '操作'

    def show_id(self,obj):
        return obj.id
    show_id.short_description = 'ID'

    def show_pubdate(self,obj):
        year_month = str(obj.pubdate)[:7].split('-')
        return year_month[0] + '年'+  year_month[1]+ '月'
    show_pubdate.short_description = '上映时间'

    def show_status(self,obj):
        try:
            then1 = Filmsession.objects.get(movie__id=obj.outid).showtime
        except:
            then1 = None
        try:
            then2 = Filmsession_hall.objects.get(movie__id=obj.outid).showtime
        except:
            then2 =None
        if then1:
            then = then1
        elif then2:
            then = then2
        else :
            return '---'
        now=datetime.now()
#        then = obj.pubdate
        delta = timedelta(days=0)
        if then - now == delta:
                return '正在热映'
        elif then - now > delta:
                return '即将上映'
        return '----'
    show_status.short_description = '状态'

    def show_trailers(self,obj):
#        out_id = obj.outid
#        trailers_count = Movie_Trailers.objects.filter(movie_id = out_id).count()
        trailers_count = Movie_Bar_Trailers.objects.filter(parent__id = obj.id).count()
        return trailers_count
    show_trailers.short_description = '预告片（数量）'

    def show_stills(self,obj):
#        out_id = obj.outid
#        stills_count = Movie_Stills.objects.filter(movie_id = out_id).count()
        stills_count = Movie_Bar_Stills.objects.filter(parent__id = obj.id).count()
        return stills_count
    show_stills.short_description = '剧照（数量）'

    def show_score(self,obj):
        return obj.score
    show_score.short_description = '综合评分'

def offline(modeladmin, request, queryset):
    queryset.update(status='0')
offline.short_description = "下线"

class ActivityAdmin(admin.ModelAdmin):
    list_display = ('id','activity_name', 'activity_status','cinema_relevance','movie_relevance','show_times','start_and_end','index','show_motion', 'expire',)
    list_editable = ('index',)
    list_display_links = ['show_motion',]
    actions = [offline,]
    list_per_page = 50

    def expire(self, obj):
        curtime = datetime.now()
        if  curtime < obj.starttime:
            return u'未开始'
        elif obj.starttime < curtime < obj.endtime:
            return u'正在进行'
        elif obj.endtime < curtime:
            return u'已过期'
        return u'不知道'
    expire.short_description = "是否过期"

    def activity_name(self,obj):
        return obj.title
    activity_name.short_description ='活动名称'

    def activity_status(self,obj):
        if obj.status == 0:
            return '下线'
        elif obj.status == 1:
            return '即将上线'
        elif obj.status == 2:
            return '上线'
        else :
            return '出错了，请检查设置'
    activity_status.short_description = '活动状态'

    def cinema_relevance(self,obj):
        return str(obj.cinemas.count())+'家'
    cinema_relevance.short_description = '关联影院'

    def movie_relevance(self,obj):
        movie_list = obj.movies.all()
        return '%s|'* len(movie_list) % tuple([i.title for i in movie_list])
    movie_relevance.short_description = '关联影片'

    def show_times(self,obj):
         return '待定'
    show_times.short_description = '场次数量'

    def start_and_end(self,obj):
        return str(obj.starttime)[:10].replace('-','.') + '-'+str(obj.endtime)[:10].replace('-','.')
    start_and_end.short_description = '开始/结束时间'

    def show_motion(self,obj):
        return '修改'
    show_motion.short_description = '操作'

class Activity_CinemaAdmin(admin.ModelAdmin):
    list_display = ('activity', 'cinema', 'optype', 'pintype',)
    list_editable = ('optype', 'pintype',)
    list_per_page = 50

class Activity_MovieAdmin(admin.ModelAdmin):
    list_display = ('activity', 'movie', 'optype',)
    list_editable = ('optype',)
    list_per_page = 50

class Condition4CinemaAdmin(admin.ModelAdmin):
    list_display = ('ccindex', 'name', 'key', 'city')
    list_editable = ('name', 'key', 'city')
    list_filter = ('city__cityname',)
    list_per_page = 50

    def ccindex(self, obj):
        return obj.id
    ccindex.short_description = u'序号'

class FilmAdmin(admin.ModelAdmin):
    list_display = ( 'movie',)
    list_filter = ('city__cityname',)
class CityAdmin(admin.ModelAdmin):
    list_display = ('cityname','center_longitude','center_latitude')

class Movie_PosterAdmin(admin.ModelAdmin):
    list_display = ('image_url','movie')



class PosterInline1(admin.StackedInline):
    model = Movie_Bar1_Posters

class PosterAdmin(admin.ModelAdmin):
    list_display = ('movie',)
    inlines = (PosterInline1,PosterInline)
    save_as = True

class StillInline(admin.StackedInline):
    model = Movie_Bar_Stills
class StillAdmin(admin.ModelAdmin):
    list_display = ('movie',)
    inlines = (StillInline,)

class ActivityInline(admin.StackedInline):
    model = Activity_Bar_Manager
class ActivityManagerAdmin(admin.ModelAdmin):
    list_display = ('id','name', 'introduction','show_motion')
#    list_editable = ('show_motion',)
    list_display_links = ['show_motion',]
    actions = [offline,]
    inlines = (ActivityInline,)
    list_per_page = 50
    def show_motion(self,obj):
        return '修改'
    show_motion.short_description = '操作'

class CinemaFeatureTypeAdmin(admin.ModelAdmin):
    list_display = ('id', 'type')
admin.site.register(CinemaFeatureType,CinemaFeatureTypeAdmin)

class Cinema_FeaturesAdmin(admin.ModelAdmin):
    list_display = ('id','cinema','cinemafeaturetype','content')
admin.site.register(Cinema_Features,Cinema_FeaturesAdmin)

class CinemaActivityInline(admin.StackedInline):
    model = Cinema_bar_Activity
class CinemaActivityAdmin(admin.ModelAdmin):
    list_display = ('cinema','address', 'roadline','telephone','weburl','weibourl','description','show_motion')
    #    list_editable = ('show_motion',)
    list_display_links = ['show_motion',]
    inlines = (CinemaActivityInline,)
    list_per_page = 50
    def show_motion(self,obj):
        return '修改'
    show_motion.short_description = '操作'

def submit(modeladmin, request, queryset):
    queryset.update(status='已提交')
submit.short_description = "提交"

class UploadFilmSessionLogAdmin(admin.ModelAdmin):
    list_display = ('id','upload_time','ticket_platform','show_time','status')
    actions=[submit,]
    list_per_page = 50
def move(modeladmin, request, queryset):
    dates = list(set([one.date for one in queryset]))
    print dates
    Filmsession_hall.objects.filter(date__in = dates,cinema = queryset[0].cinema).delete()
    for one in queryset :
        showtime = datetime.strptime(one.showdate+' '+one.start, "%Y-%m-%d %H:%M")
        endtime = datetime.strptime(one.showdate+' '+one.end, "%Y-%m-%d %H:%M")
        if endtime<showtime:
            endtime = endtime + timedelta(days = 1)
        Filmsession_hall.objects.create(
            price =one.price,
            language_version = one.language_version,
            screening_mode = one.screening_mode,
            date = one.date,
            movie = one.movie,
            cinema = one.cinema,
            hall_num = one.hall_num,
            city = one.city,
            showtime =showtime,
            endtime =endtime)

    return delete_selected(modeladmin, request, queryset)
move.short_description = "移动"
class FilmsessionAdmin(admin.ModelAdmin):
    list_display = ('id','show_time','show_cinema','hall_num','show_name','language_version','screening_mode','start','end','show_mins','price','show_motion')
    list_display_links = ['show_motion',]
    list_filter = ['cinema__name','showdate']
    actions = [move,]

    def show_motion(self,obj):
        return u'修改'
    show_motion.short_description = u'操作'
    def show_mins(self,obj):
        return obj.movie.mins
    show_mins.short_description = u'片长'
    def show_cinema(self,obj):
        return obj.cinema.name
    show_cinema.short_description = u'影院名称'
    def show_name(self,obj):
        return obj.movie.title
    show_name.short_description = u'影片名称'
    def show_time(self,obj):
        return obj.date
    show_time.short_description = '放映日期'
admin.site.register(FilmsessionFile,FilmsessionAdmin)

def line_down(modeladmin, request, queryset):
    queryset.update(status='下线')
line_down.short_description = "下线"

class AppManagerAdmin(admin.ModelAdmin):
    list_display = ('id','name','status','movie_relevance','platform','filesize','downloadurl','index','show_motion')
    list_editable = ['index',]
    actions = [line_down,]
    list_display_links = ['show_motion',]
    def movie_relevance(self,obj):
        movie_list = obj.movies.all()
        return '%s|'* len(movie_list) % tuple([i.title for i in movie_list])
    movie_relevance.short_description = '关联影片'
    def filesize(self,obj):
        file_path = os.path.join(path.MEDIA_ROOT, str(obj.attachment)).replace('\\', '/')
        filesize = os.path.getsize(unicode(file_path,'utf8'))
        if filesize > 1024*1024:
            filesize = '%.2fM' % (float(filesize) / 1024 / 1024)
        elif filesize > 1024:
            filesize = '%.2fK' % (float(filesize) / 1024)
        else:
            filesize = '%sB' % filesize
    filesize.short_description = '文件大小'

    def downloadurl(self,obj):
        return"""<a href="%s">%s</a>""" % (obj.attachment.url,str(obj.attachment).split("/")[-1])
    downloadurl.allow_tags = True
    downloadurl.short_description = '下载地址'
    list_per_page = 50
    def show_motion(self,obj):
        return '修改'
    show_motion.short_description = '操作'
admin.site.register(AppManager,AppManagerAdmin)

class AppPictureAdmin(admin.ModelAdmin):
    list_display = ('id','poster','client','web')
    def poster(self,obj):
        return """<img src="%s">"""% obj.poster_pic.url
    poster.short_description = '海报图片'
    poster.allow_tags = True
    def client(self,obj):
        return """<img src="%s">"""% obj.client_pic.url
    client.short_description = '客户端图片'
    client.allow_tags = True
    def web(self,obj):
        return """<img src="%s">"""% obj.web_pic.url
    web.short_description = '网站图标'
    web.allow_tags = True
admin.site.register(AppPicture,AppPictureAdmin)

class ActivityTypeAdmin(admin.ModelAdmin):
    list_display = ('id','name','introduction','show_motion')
    list_display_links = ['show_motion',]
    def show_motion(self,obj):
        return '修改'
    show_motion.short_description = '操作'
class Filmsession_hallAdmin(admin.ModelAdmin):
    list_display = ('id','show_name','showtime','endtime','show_cinema','hall_num','language_version','screening_mode','show_mins','price','show_motion')
    list_display_links = ['show_motion',]
    actions = [move,]
    list_filter = ('cinema__name',)

    def show_motion(self,obj):
        return u'修改'
    show_motion.short_description = u'操作'
    def show_mins(self,obj):
        return obj.movie.mins
    show_mins.short_description = u'片长'
    def show_cinema(self,obj):
        return obj.cinema.name
    show_cinema.short_description = u'影院名称'
    def show_name(self,obj):
        return obj.movie.title
    show_name.short_description = u'影片名称'
class Movie_News_ListAdmin(admin.ModelAdmin):
    list_display = ('title', 'movie', 'source', 'index')
    list_editable = ('index',)
    list_per_page = 50
    search_fields = ('movie__title',)
    list_filter = ('movie__title',)
admin.site.register(Movie_News_List,Movie_News_ListAdmin)
#admin.site.register(ActivityType,ActivityTypeAdmin)
admin.site.register(UploadFilmSessionLog,UploadFilmSessionLogAdmin)
admin.site.register(CinemaActivity,CinemaActivityAdmin) #影院增加活动
admin.site.register(ActivityType,ActivityManagerAdmin)
admin.site.register(Movie_Stills,StillAdmin)
admin.site.register(Movie_Posters,PosterAdmin)

admin.site.register(Movie_Posters,Movie_PosterAdmin)
admin.site.register(City,CityAdmin)
admin.site.register(Filmsession_hall,Filmsession_hallAdmin) #Filmsession_hall
admin.site.register(Cinema, CinemaAdmin)
admin.site.register(Movie, MovieAdmin)
admin.site.register(Activity, ActivityAdmin)
admin.site.register(Activity_Movie, Activity_MovieAdmin)
admin.site.register(Activity_Cinema, Activity_CinemaAdmin)
admin.site.register(Condition4Cinema, Condition4CinemaAdmin)