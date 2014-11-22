# -*- coding: utf-8 -*-
from django.db import models
import os

class City(models.Model):
    cityid = models.CharField('城市id', max_length=100, unique=True)
    cityname = models.CharField('城市名称', max_length=100)
    center_longitude = models.DecimalField('中心点经度坐标', max_digits=12, decimal_places=6)
    center_latitude = models.DecimalField('中心点纬度坐标', max_digits=12, decimal_places=6)

    def __unicode__(self):
        return self.cityname
    class Meta:
        db_table = u'city'
        verbose_name = "城市"
        verbose_name_plural = "城市"

MOVIE_STATUS_CHOICE = (
    ('1', '已上线'),
    ('0', '已下线'),
    )

class Movie(models.Model):
    title = models.CharField('影片名称', max_length=100)
    nickname = models.CharField('别名', max_length = 100)
    directors = models.CharField('导演们', max_length=200)
    actors = models.CharField('演员们', max_length=200)
    mins = models.CharField('片长', max_length=100)
    pubdate = models.DateTimeField('影片发布日期')
    age = models.CharField('影片年代',max_length=100)
    score = models.CharField('评分', max_length=20)
    plots = models.TextField('剧情')
    poster_image_url = models.CharField('海报图片地址', max_length=500,blank =True)
    outid = models.CharField('外链id', max_length=32, unique=True)
    index = models.IntegerField('排序', max_length=5, default=0)
    createtime = models.DateTimeField('创建时间', auto_now_add=True)
    status = models.CharField('状态', max_length=50, choices=MOVIE_STATUS_CHOICE)
    filmtype = models.CharField('影片类型', max_length=100,blank =True)
    certification = models.CharField('影片分级', max_length=200,blank =True)
    language = models.CharField('影片语言', max_length=100,blank =True)
    area = models.CharField('地区', max_length=50,blank =True)

    def __unicode__(self):
        return self.title
    class Meta:
        db_table = u'movie'
        verbose_name = "影片"
        verbose_name_plural = "影片"

class Movie_Trailers(models.Model):
    image_url = models.CharField('预告片图片地址', max_length=500)
    video_url = models.CharField('预告片视频地址', max_length=500)
    movie = models.ForeignKey(Movie, to_field='outid')
    class Meta:
        db_table = u'movie_trailers'

def get_trailer_path(instance,name):
    return '%d/trailer/%s'% (instance.parent.id,name)

class Movie_Bar_Trailers(models.Model):
    parent = models.ForeignKey(Movie)
    image_url = models.ImageField('预告片图片',upload_to=get_trailer_path)
    video_url = models.CharField('预告片视频地址',max_length = 500)
    class Meta:
        verbose_name = u"添加预告片相关信息"



class Movie_Stills(models.Model):
    image_url = models.CharField('剧照图片地址', max_length=500)
    source = models.CharField('来源', max_length=45, default='lyk')
    movie = models.ForeignKey(Movie, to_field='outid')
    class Meta:
        db_table = u'movie_stills'
        verbose_name = u"剧照"

def get_still_path(instance,name):
    return '%d/stills/%s'% (instance.parent.id,name)

class Movie_Bar_Stills(models.Model):
    parent = models.ForeignKey(Movie) #todo movie
    source = models.CharField('来源',max_length=45,default='lyk') # todo rename source
    img = models.ImageField('图片',upload_to=get_still_path)#os.path.abspath(os.path.dirname(__file__)))
    class Meta:
        verbose_name = u"添加剧照图片"



class Movie_Posters(models.Model):
    movie = models.ForeignKey(Movie, to_field='outid')

    class Meta:
        db_table = u'movie_posters'
        verbose_name = u"影片添加海报"

def get_poster_path(instance,name):
    return '%d/posters/%s'% (instance.parent.id,name)

POSTER_VERSION_CHOICES = (
    ('V', '竖版图片'),
    ('H', '横版图片'),
    )

class Movie_Bar_Posters(models.Model):
    parent = models.ForeignKey(Movie)
    source = models.CharField('来源',max_length=45,default='lyk')
    type = models.CharField('海报版式',max_length=20,choices=POSTER_VERSION_CHOICES)
    img = models.ImageField('海报图片',upload_to=get_poster_path)#os.path.abspath(os.path.dirname(__file__)))
    class Meta:
        verbose_name = u"添加海报图片"


class Movie_Bar1_Posters(models.Model):
    parent = models.ForeignKey(Movie_Posters)
    picurl = models.CharField('海报URL',max_length=300)
    type = models.CharField('海报版式',max_length=20,choices=POSTER_VERSION_CHOICES)
    class Meta:
        verbose_name = u"添加图片"


class Movie_Cinecisms(models.Model):
    username = models.CharField('评论人名称', max_length=500)
    region = models.CharField('评论发布地', max_length=500)
    pubtime = models.DateTimeField('评论发布时间', auto_now_add=True)
    content = models.CharField('评论内容', max_length=500)
    movie = models.ForeignKey(Movie, to_field='outid')
    class Meta:
        db_table = u'movie_cinecisms'

class City(models.Model):
        cityid = models.CharField('城市id', max_length=100, unique=True)
        cityname = models.CharField('城市名称', max_length=100)
        center_longitude = models.DecimalField('中心点经度坐标', max_digits=12, decimal_places=6)
        center_latitude = models.DecimalField('中心点纬度坐标', max_digits=12, decimal_places=6)

        def __unicode__(self):
            return self.cityname
        class Meta:
            db_table = u'city'
            verbose_name = "城市"
            verbose_name_plural = "城市"

LYK_CINEMA_ONSALE_CHOICES = (
    (0, '不可售票'),
    (1, '可售票'),
    )

class Cinema(models.Model):
    name = models.CharField('影院名称', max_length=200)
    score = models.CharField('影院打分', max_length=20,blank=True)
    address = models.CharField('影院地址', max_length=400,blank=True)
    longitude = models.DecimalField('经度坐标', max_digits=12, decimal_places=6)
    latitude = models.DecimalField('纬度坐标', max_digits=12, decimal_places=6)
    telephone = models.CharField('联系电话', max_length=100,blank=True)
    description = models.TextField('影院描述',blank=True)
    roadline = models.CharField('乘车路线', max_length=400,blank=True)
    outid = models.CharField('外链id', max_length=32, unique=True)
    locationstr = models.CharField('省市区拼音', max_length=200)
    onsale = models.IntegerField('是否可以售票', choices=LYK_CINEMA_ONSALE_CHOICES, default=0)
    index = models.IntegerField('排序', max_length=5, default=0,blank=True)
    longitude_baidu = models.DecimalField('百度经度坐标', max_digits=12, decimal_places=6,blank=True)
    latitude_baidu = models.DecimalField('百度纬度坐标', max_digits=12, decimal_places=6,blank=True)
    businesshours = models.CharField('营业时间', max_length=50,blank=True)
    weburl = models.CharField('官网地址', max_length=400,blank=True)
    weibourl = models.CharField('官方微博',max_length=400,blank=True)
    introduction = models.TextField('影院介绍',blank=True)
    city = models.ForeignKey(City, to_field='cityid')

    def __unicode__(self):
        return self.name
    class Meta:
        db_table = u'cinema'
        verbose_name = "影院"
        verbose_name_plural = "影院"

class Cinema_Images(models.Model):
    image_url = models.CharField('影院图片地址', max_length=500)
    cinema = models.ForeignKey(Cinema, to_field='outid')
    class Meta:
        db_table = u'cinema_images'

class Filmsession(models.Model):
    showtime = models.DateTimeField('放映时间', null=True)
    price = models.IntegerField('现价', null=True)
    price_ori = models.IntegerField('原价', null=True)
    language_version = models.CharField('语言版本', max_length=20, null=True)
    screening_mode = models.CharField('屏幕类型', max_length=20, null=True)
    date = models.DateField('排场日期-用于抓取排重')
    movie = models.ForeignKey(Movie, to_field='outid')
    cinema = models.ForeignKey(Cinema, to_field='outid')
    hall_num = models.CharField('影厅号', max_length=100, null=True)
    city = models.ForeignKey(City, to_field='cityid')
    class Meta:
        db_table = u'filmsession'
        verbose_name = '影片排场'

class Filmsession_hall(models.Model):
    showtime = models.DateTimeField('放映时间', null=True)
    price = models.IntegerField('现价', null=True)
    price_ori = models.IntegerField('原价', null=True)
    language_version = models.CharField('语言版本', max_length=20, null=True)
    screening_mode = models.CharField('屏幕模式', max_length=20, null=True)
    date = models.DateField('排场日期-用于抓取排重')
    movie = models.ForeignKey(Movie, to_field='outid')
    cinema = models.ForeignKey(Cinema, to_field='outid')
    hall_num = models.CharField('厅号', max_length=100, null=True)
    city = models.ForeignKey(City, to_field='cityid')
    endtime = models.DateTimeField('结束时间',null=True)
    class Meta:
        db_table = u'filmsession_hall'
        verbose_name = '影片排场'

class Movie_Will_City(models.Model):
    movie = models.ForeignKey(Movie, to_field='outid')
    city = models.ForeignKey(City, to_field='cityid')
    class Meta:
        db_table = u'movie_will_city'

class Area(models.Model):
    cityid = models.CharField('城市id', max_length=100)
    cityname = models.CharField('城市名称', max_length=100)
    districtid = models.CharField('城区id', max_length=100, unique=True)
    districtname = models.CharField('城区名称', max_length=100)
    class Meta:
        db_table = u'area'

class DistrictCinema(models.Model):
    district = models.ForeignKey(Area, to_field='districtid')
    cinema = models.OneToOneField(Cinema, to_field='outid')
    class Meta:
        db_table = u'districtcinema'

class ClientUser(models.Model):
    username = models.CharField('用户名', max_length=100, null=True)
    age = models.CharField('年龄', max_length=100, null=True)
    gender = models.CharField('性别', max_length=100, null=True)
    password = models.CharField('密码', max_length=100, null=True)
    portrait_imgurl = models.CharField('头像地址', max_length=400, null=True)
    prefix = models.CharField('随机前缀', max_length=6)
    class Meta:
        db_table = u'clientuser'

class UserPhoneNumber(models.Model):
    phonenumber = models.CharField('手机号', max_length=100)
    clientuser = models.ForeignKey(ClientUser)
    class Meta:
        db_table = u'userphonenumber'

class ClientDevice(models.Model):
        mac = models.CharField('MAC地址', max_length=100)
        device_name = models.CharField('设备名', max_length=50)
        device_version = models.CharField('设备版本', max_length=50)
        device_model = models.CharField('设备型号', max_length=50)
        screen_size = models.CharField('屏幕尺寸', max_length=15)
        client_version =  models.CharField('客户端版本', max_length=15)
        clientuser = models.ForeignKey(ClientUser)
        class Meta:
            db_table = u'clientdevice'

class Condition4Cinema(models.Model):
    key = models.CharField('URL中key值', max_length=5)
    name = models.CharField('条件名称', max_length=20)
    city = models.ForeignKey(City, to_field='cityid')

    def __unicode__(self):
        return self.name
    class Meta:
        db_table = u'condition4cinema'
        verbose_name = "影院筛选条件"
        verbose_name_plural = "影院筛选条件"

LYK_ACTIVITY_OP_CHOICES = (
    (0, '打开URL'),
    (1, '进入影片页'),
    (2, '进入影院页'),
    (3, '进入排场页'),
    (4, '进入活动列表页'),
    )

LYK_ACTIVITY_ICON_CHOICES = (
    (0, '首映'),
    (1, '抢票'),
    (2, '团购'),
    (3, 'APP推荐'),
    )

LYK_ACTIVITY_STATUS_CHOICES = (
    (0, '下线'),
    (1, '即将上线'),
    (2, '上线'),
    )

LYK_ACTIVITY_PINTYPE_CHOICES = (
    (0, '黑色无图标'),
    (1, '蓝色有图标'),
    (2, '红色有图标'),
    )
class ActivityType(models.Model):
    name = models.CharField('活动类型名称',max_length=200,unique=True)
    introduction = models.TextField('活动类型说明')
    def __unicode__(self):
        return self.name
    class Meta:
        verbose_name = "活动类型"


class Activity(models.Model):
    title = models.CharField('活动标题', max_length=100)
    img_url = models.ImageField('活动图片',upload_to = 'activity/')
    introduction = models.TextField('活动广告语')
    description = models.TextField('活动详情')
    starttime = models.DateTimeField('开始时间')
    endtime = models.DateTimeField('结束时间')
    optype = models.IntegerField('操作类型', choices=LYK_ACTIVITY_OP_CHOICES)
    data = models.CharField('操作数据', max_length=500)
    activitytype = models.ForeignKey(ActivityType,verbose_name = '活动类型')
    iconname = models.CharField('活动icon名称', max_length=4)
    iconurl = models.CharField('活动iconURL', max_length=400)
    iconcolor = models.CharField('活动icon色值RGB', max_length=20)
    status = models.IntegerField('活动状态', choices=LYK_ACTIVITY_STATUS_CHOICES)
    url = models.CharField('活动URL', max_length=400)
    outid = models.CharField('外链id', max_length=45)
    index = models.IntegerField('排序', max_length=5, default=0)
    citys = models.ManyToManyField(City,db_table=u'activitys_citys')
    signup_starttime = models.DateTimeField('报名开始时间',blank=True)
    signup_endtime = models.DateTimeField('报名结束时间',blank=True)

    def __unicode__(self):
        return self.title
    class Meta:
        db_table = u'activity'
        verbose_name = "活动"
        verbose_name_plural = "活动"

class Activity_Movie(models.Model):
    activity = models.ForeignKey(Activity)
    movie = models.ForeignKey(Movie)
    optype = models.IntegerField('操作类型', choices=LYK_ACTIVITY_OP_CHOICES)

    def __unicode__(self):
        return ''.join([self.activity.title, '-', self.movie.title])
    class Meta:
        db_table = u'activitys_movies'
        verbose_name = "对应影片的活动"
        verbose_name_plural = "对应影片的活动"

class Activity_Cinema(models.Model):
    activity = models.ForeignKey(Activity)
    cinema = models.ForeignKey(Cinema)
    optype = models.IntegerField('操作类型', choices=LYK_ACTIVITY_OP_CHOICES)
    pintype = models.IntegerField('展现类型', choices=LYK_ACTIVITY_PINTYPE_CHOICES)

    def __unicode__(self):
        return ''.join([self.activity.title, '-', self.cinema.name])
    class Meta:
        db_table = u'activitys_cinemas'
        verbose_name = "对应影院的活动"
        verbose_name_plural = "对应影院的活动"

class Application(models.Model):
    title = models.CharField('应用名称', max_length=100)
    image_url = models.CharField('应用icon', max_length=400)
    introduction = models.CharField('应用简介', max_length=400)
    download_url = models.CharField('应用下载地址', max_length=400)
    createtime = models.DateTimeField('创建时间')
    movies = models.ManyToManyField(Movie,db_table=u'applications_movies')
    cinemas = models.ManyToManyField(Cinema,db_table=u'applications_cinemas')
    citys = models.ManyToManyField(City,db_table=u'applications_citys')
    class Meta:
        db_table = u'application'

LYK_ORDER_PAY_STATUS_CHOICES = (
    (0, '未支付'),
    (1, '已支付'),
    )

# 用于15分钟失效状态标识
LYK_ORDER_STATUS_CHOICES = (
    (0, '有效'),
    (1, '失效'), # 订单生成超时， 同一用户生成第二个订单时，前面的未支付并有效的订单改成失效
    )

class TicketOrder(models.Model):
    createtime = models.DateTimeField('创建时间')
    updatetime = models.DateTimeField('更新时间')
    ticketcount = models.IntegerField('购票数量')
    totalprice = models.CharField('总购票价格', max_length=20)
    seatinfos = models.CharField('座位号', max_length=400)
    phonenum = models.CharField('接收取票码手机号', max_length=20)
    cdkey = models.CharField('兑换码', max_length=100)
    pay_status = models.IntegerField('订单支付状态', choices=LYK_ORDER_PAY_STATUS_CHOICES)
    status = models.IntegerField('订单状态', choices=LYK_ORDER_STATUS_CHOICES)
    filmsession = models.ForeignKey(Filmsession_hall)
    clientuser = models.ForeignKey(ClientUser)
    class Meta:
        db_table = u'ticketorder'

class BookSeatChannel(models.Model):
    cname = models.CharField('渠道名称', max_length=100, default=u'乐影客')
    ckey = models.CharField('渠道KEY', max_length=100, default='leyingke')
    cinemas = models.ManyToManyField(Cinema, db_table='channels_cinemas')
    filmsessions = models.ManyToManyField(Filmsession_hall, db_table='channels_filmsessions')
    class Meta:
        db_table = u'bookseatchannel'

LYK_FAVORITE_TYPE_CHOICES = (
    (0, '影院'),
    (1, '影片'),
    (2, '活动'),
    )

LYK_FAVORITE_STATUS_CHOICES = (
    (1, '加关注'),
    (-1, '取消关注'),
    )

class Favorite(models.Model):
    ftype = models.IntegerField('关注类型', choices=LYK_FAVORITE_TYPE_CHOICES)
    data = models.CharField('数据ID', max_length=100)
    status = models.IntegerField('关注状态', choices=LYK_FAVORITE_STATUS_CHOICES)
    createtime = models.DateTimeField('创建时间', auto_now_add=True)
    updatetime = models.DateTimeField('更新时间', auto_now=True)
    clientuser = models.ForeignKey(ClientUser)
    class Meta:
        db_table = u'favorite'

class UserActivity(models.Model):
    clientuser = models.ForeignKey(ClientUser)
    activity = models.ForeignKey(Activity)
    cdkey = models.CharField('兑换码', max_length=100)
    class Meta:
        db_table = u'useractivity'

def get_manager_path(instance,name):
    return 'activity/%d/%s'% (instance.parent.id,name)


class Activity_Bar_Manager(models.Model): #
    parent = models.ForeignKey(ActivityType)
    title = models.CharField('图片名',max_length=30)
    img = models.ImageField('图片',upload_to=get_manager_path)#os.path.abspath(os.path.dirname(__file__)))
    class Meta:
        verbose_name = u"添加图片"


class CinemaFeatureType(models.Model):
    type =  models.CharField('影院特色类型', max_length=100)
    def __unicode__(self):
        return self.type
    class Meta:
        db_table = u'cinemafeaturetype'
        verbose_name = "特色服务"

class Cinema_Features(models.Model):
    cinema = models.ForeignKey(Cinema)
    cinemafeaturetype = models.ForeignKey(CinemaFeatureType)
    content = models.CharField('内容',max_length=600)
    class Meta:
        db_table = u'cinema_features'
        verbose_name = "影院特色服务"


class CinemaActivity(models.Model):
    cinema = models.ForeignKey(Cinema)
    address = models.TextField('地址')
    roadline = models.TextField('乘车路线')
    telephone = models.CharField('联系电话',max_length=400)
    weburl = models.CharField('官网地址',max_length=100)
    weibourl = models.CharField('微博地址',max_length=100)
    description = models.TextField('影院描述')
    class Meta:
        verbose_name = '添加影院活动'

def get_cinema_path(instance,name):
    return 'cinema/%d/'% instance.parent.id

class Cinema_bar_Activity(models.Model):
    parent = models.ForeignKey(CinemaActivity)
    title = models.CharField('图片名',max_length=30)
    img = models.ImageField('图片',upload_to=get_cinema_path)#os.path.abspath(os.path.dirname(__file__)))
    class Meta:
        verbose_name = u"添加图片"

class UploadFilmSessionLog(models.Model):
    identify = models.CharField('标识',max_length = 100)
    upload_time = models.DateTimeField('创建时间', auto_now_add=True)
    ticket_platform = models.CharField('票务平台', max_length=100)
    show_time = models.CharField('排片日期',max_length=100)
    status = models.CharField('状态',max_length=100,default='未提交')
    class Meta:
        verbose_name = u"排场信息上传log表"

def get_path(instance,name):
        return 'upload/%d/%s'%(instance.id,name)
class AppManager(models.Model):
    name = models.CharField('名称', max_length=100)
    advertisement = models.TextField('广告语',null=True)
    introduction = models.TextField('介绍',null=True)
    attachment = models.FileField('下载包文件地址',upload_to = 'app/upload')
    movies = models.ManyToManyField(Movie)
    cinemas = models.ManyToManyField(Cinema)
    platform = models.CharField('支持平台',max_length=100)
    status = models.CharField('状态',max_length=100,default= '上线')
    index = models.IntegerField('排序',max_length=10,default=0)
    def __unicode__(self):
        return self.name
    class Meta:
        verbose_name =u'App管理'
        db_table = u'appmanager'

class AppPicture(models.Model):
    appmanager = models.ForeignKey(AppManager)
    poster_pic = models.ImageField('海报图片',upload_to = 'app/poster/')
    client_pic = models.ImageField('客户端图标',upload_to = 'app/client/')
    web_pic = models.ImageField('网站图标',upload_to = 'app/web/')
    class Meta:
        verbose_name = u'App图片信息'

class FilmsessionFile(models.Model): # todo 组id
    identify = models.CharField('标识',max_length = 100)
    showtime = models.DateTimeField('放映时间', null=True)
    price = models.IntegerField('现价', null=True)
    price_ori = models.IntegerField('原价', null=True)
    language_version = models.CharField('语言版本', max_length=20, null=True)
    screening_mode = models.CharField('屏幕类型', max_length=20, null=True)
    date = models.DateField('排场日期-用于抓取排重')
    movie = models.ForeignKey(Movie, to_field='outid')
    cinema = models.ForeignKey(Cinema, to_field='outid')
    hall_num = models.CharField('影厅号', max_length=100, null=True)
    city = models.ForeignKey(City, to_field='cityid')
    showdate = models.CharField('放映日期',max_length=100)
    start = models.CharField('放映时间',max_length=100)
    end = models.CharField('结束时间',max_length=100)
    mins = models.CharField('片长',max_length=50)
    class Meta:
        db_table = u'filmsessionfile'
        verbose_name = '影片排场备份'
        verbose_name_plural ='影片排场备份'

class Movie_News_List(models.Model):
    title = models.CharField('题目', max_length=100)
    url = models.CharField('外链接地址', max_length=500)
    source = models.CharField('来源', max_length=100, blank=True)
    pubtime = models.DateTimeField('发布时间', max_length=100, blank=True)
    img_url = models.CharField('图片地址', max_length=100, blank=True)
    content = models.TextField('内容', blank=True)
    index = models.IntegerField('排序', max_length=5, default=0)
    movie = models.ForeignKey(Movie)

    def __unicode__(self):
        return self.title
    class Meta:
        db_table = u'movie_news_list'
        verbose_name = "电影报道"
        verbose_name_plural = "电影报道"
from django.contrib.auth.models import User
class  User_Cinema(models.Model):
    user = models.ForeignKey(User,verbose_name='用户')
    cinema = models.ForeignKey(Cinema,verbose_name='影院')