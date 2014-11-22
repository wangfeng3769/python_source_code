# -*- coding: utf-8 -*-
from django.db import models

class Movie(models.Model):
    title = models.CharField('影片名称', max_length=100)
    directors = models.CharField('导演们', max_length=200)
    actors = models.CharField('演员们', max_length=200)
    mins = models.CharField('片长', max_length=100)
    pubdate = models.DateTimeField('影片发布日期')
    score = models.CharField('评分', max_length=20)
    plots = models.TextField('剧情')
    poster_image_url = models.CharField('海报图片地址', max_length=500)
    outid = models.CharField('外链id', max_length=32, unique=True)
    index = models.IntegerField('排序', max_length=5, default=0)
    createtime = models.DateTimeField('创建时间', auto_now_add=True)
    filmtype = models.CharField('影片类型', max_length=100)
    certification = models.CharField('影片分级', max_length=200)
    language = models.CharField('影片语言', max_length=100)
    area = models.CharField('地区', max_length=50)
    class Meta:
        db_table = u'movie'

class Movie_Trailers(models.Model):
    image_url = models.CharField('预告片图片地址', max_length=500)
    video_url = models.CharField('预告片视频地址', max_length=500)
    movie = models.ForeignKey(Movie, to_field='outid')
    class Meta:
        db_table = u'movie_trailers'

class Movie_Stills(models.Model):
    image_url = models.CharField('剧照图片地址', max_length=500)
    source = models.CharField('来源', max_length=45, default='mtime')
    movie = models.ForeignKey(Movie, to_field='outid')
    class Meta:
        db_table = u'movie_stills'

LYK_MOVIE_POSTER_TYPE_CHOICES = (
    (0, '竖版'),
    (1, '横版'),
    )

class Movie_Posters(models.Model):
    image_url = models.CharField('海报图片地址', max_length=500)
    type = models.IntegerField('海报图片横竖类型', choices=LYK_MOVIE_POSTER_TYPE_CHOICES, default=0)
    movie = models.ForeignKey(Movie, to_field='outid')
    class Meta:
        db_table = u'movie_posters'

class Movie_Cinecisms(models.Model):
    username = models.CharField('评论人名称', max_length=500)
    region = models.CharField('评论发布地', max_length=500)
    pubtime = models.DateTimeField('评论发布时间', auto_now_add=True)
    content = models.CharField('评论内容', max_length=500)
    source = models.CharField('评论内容', max_length=45)
    usersid = models.CharField('评论人sid', max_length=45)
    movie = models.ForeignKey(Movie, to_field='outid')
    class Meta:
        db_table = u'movie_cinecisms'

LYK_MEDIA_SCORESOURCE_CHOICES = (
    (0, '网友'),
    (1, '专业观影团'),
    )

class MediaChannel(models.Model):
    medianame = models.CharField('评分媒体名称', max_length=50)
    scoresource = models.IntegerField('评分来源群体', choices=LYK_MEDIA_SCORESOURCE_CHOICES, default=0)
    mediaicon = models.ImageField('评分媒体iconurl', upload_to='icon/%Y/%m/%d', blank=True, null=True, max_length=500)
    class Meta:
        db_table = u'mediachannel'

class Movie_Score(models.Model):
    score = models.CharField('分数', max_length=20)
    mediachannel = models.ForeignKey(MediaChannel)
    movie = models.ForeignKey(Movie)
    class Meta:
        db_table = u'movie_score'

class Movie_News_List(models.Model):
    title = models.CharField('题目', max_length=100)
    url = models.CharField('外链接地址', max_length=500)
    source = models.CharField('来源', max_length=100, null=True)
    pubtime = models.DateTimeField('发布时间', max_length=100, null=True)
    img_url = models.CharField('图片地址', max_length=100, null=True)
    content = models.TextField('内容', null=True)
    index = models.IntegerField('排序', max_length=5, default=0)
    movie = models.ForeignKey(Movie)
    class Meta:
        db_table = u'movie_news_list'

class City(models.Model):
    cityid = models.CharField('城市id', max_length=100, unique=True)
    cityname = models.CharField('城市名称', max_length=100)
    center_longitude = models.DecimalField('中心点经度坐标', max_digits=12, decimal_places=6)
    center_latitude = models.DecimalField('中心点纬度坐标', max_digits=12, decimal_places=6)
    pinyin = models.CharField('城市拼音', max_length=100)
    index = models.IntegerField('排序', max_length=5, default=0)
    class Meta:
        db_table = u'city'

LYK_CINEMA_ONSALE_CHOICES = (
    (0, '不可售票'),
    (1, '可售票'),
    )

class Cinema(models.Model):
    name = models.CharField('影院名称', max_length=200)
    score = models.CharField('影院打分', max_length=20)
    address = models.CharField('影院地址', max_length=400)
    longitude = models.DecimalField('经度坐标', max_digits=12, decimal_places=6)
    latitude = models.DecimalField('纬度坐标', max_digits=12, decimal_places=6)
    telephone = models.CharField('联系电话', max_length=100)
    description = models.TextField('影院描述')
    roadline = models.CharField('乘车路线', max_length=400)
    outid = models.CharField('外链id', max_length=32, unique=True)
    locationstr = models.CharField('省市区拼音', max_length=200)
    onsale = models.IntegerField('是否可以售票', choices=LYK_CINEMA_ONSALE_CHOICES, default=0)
    index = models.IntegerField('排序', max_length=5, default=0)
    longitude_baidu = models.DecimalField('百度经度坐标', max_digits=12, decimal_places=6)
    latitude_baidu = models.DecimalField('百度纬度坐标', max_digits=12, decimal_places=6)
    businesshours = models.CharField('营业时间', max_length=50)
    weburl = models.CharField('官网地址', max_length=400, default=None)
    weibourl = models.CharField('官方微博',max_length=400,default=None)
    introduction = models.TextField('影院介绍')
    city = models.ForeignKey(City, to_field='cityid')
    class Meta:
        db_table = u'cinema'

class CinemaFeatureType(models.Model):
    type =  models.CharField('影院特色类型', max_length=100)
    class Meta:
        db_table = u'cinemafeaturetype'

class Cinema_Features(models.Model):
    cinema = models.ForeignKey(Cinema)
    cinemafeaturetype = models.ForeignKey(CinemaFeatureType)
    content = models.CharField('内容',max_length=600)
    class Meta:
        db_table = u'cinema_features'

class Cinema_Images(models.Model):
    image_url = models.CharField('影院图片地址', max_length=500)
    islogo = models.IntegerField('是否为logo', default=0)
    cinema = models.ForeignKey(Cinema, to_field='outid')
    class Meta:
        db_table = u'cinema_images'

class Filmsession(models.Model):
    showtime = models.DateTimeField('放映时间', null=True)
    price = models.CharField('现价', max_length=10, null=True, default='')
    price_ori = models.CharField('原价', max_length=10, null=True, default='')
    language_version = models.CharField('语言版本', max_length=20, null=True)
    screening_mode = models.CharField('屏幕模式', max_length=20, null=True)
    date = models.DateField('排场日期-用于抓取排重')
    movie = models.ForeignKey(Movie, to_field='outid')
    cinema = models.ForeignKey(Cinema, to_field='outid')
    hall_num = models.CharField('厅号', max_length=100, null=True)
    city = models.ForeignKey(City, to_field='cityid')
    class Meta:
        db_table = u'filmsession'

class Filmsession_hall(models.Model):
    showtime = models.DateTimeField('放映时间', null=True)
    price = models.CharField('现价', max_length=10, null=True, default='')
    price_ori = models.CharField('原价', max_length=10, null=True, default='')
    language_version = models.CharField('语言版本', max_length=20, null=True)
    screening_mode = models.CharField('屏幕模式', max_length=20, null=True)
    date = models.DateField('排场日期-用于抓取排重')
    movie = models.ForeignKey(Movie, to_field='outid')
    cinema = models.ForeignKey(Cinema, to_field='outid')
    hall_num = models.CharField('厅号', max_length=100, null=True)
    city = models.ForeignKey(City, to_field='cityid')
    class Meta:
        db_table = u'filmsession_hall'

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
    portrait_imgurl = models.ImageField('头像地址', upload_to='portraits/%Y/%m/%d', blank=True, null=True, max_length=400)
    prefix = models.CharField('随机前缀', max_length=6)
    email = models.CharField('Email', max_length=100, null=True)
    source = models.CharField('来源平台', max_length=45, default='mobile')
    class Meta:
        db_table = u'clientuser'

LYK_CLIENT_PLATFORM = (
    (0, 'ios'),
    (1, 'android'),
    )

class ClientVersion(models.Model):
    title = models.CharField('版本更新名称', max_length=20)
    content = models.CharField('版本更新内容', max_length=400)
    size = models.CharField('版本文件大小', max_length=15)
    client_version = models.CharField('版本号', max_length=12)
    platform = models.IntegerField('客户端平台')
    dl_url = models.FileField('下载地址', upload_to='client/dl', blank=True, null=True, max_length=500)
    status = models.IntegerField('状态', default=0)
    createtime = models.DateTimeField('创建时间', auto_now_add=True)
    class Meta:
        db_table = u'clientversion'

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
    class Meta:
        db_table = u'condition4cinema'

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

class Activity(models.Model):
    title = models.CharField('活动标题', max_length=100)
    image_url = models.CharField('活动图片', max_length=400)
    introduction = models.CharField('活动广告语', max_length=400)
    description = models.TextField('活动详情')
    starttime = models.DateTimeField('开始时间')
    endtime = models.DateTimeField('结束时间')
    optype = models.IntegerField('操作类型', choices=LYK_ACTIVITY_OP_CHOICES)
    data = models.CharField('操作数据', max_length=500)
    icontype = models.IntegerField('活动类型', choices=LYK_ACTIVITY_ICON_CHOICES)
    iconname = models.CharField('活动icon名称', max_length=4)
    iconurl = models.CharField('活动iconURL', max_length=400)
    iconcolor = models.CharField('活动icon色值RGB', max_length=20)
    status = models.IntegerField('活动状态', choices=LYK_ACTIVITY_STATUS_CHOICES)
    url = models.CharField('活动URL', max_length=400)
    outid = models.CharField('外链id', max_length=45)
    index = models.IntegerField('排序', max_length=5, default=0)
    signup_starttime = models.DateTimeField('报名开始时间')
    signup_endtime = models.DateTimeField('报名结束时间')
    citys = models.ManyToManyField(City,db_table=u'activitys_citys')
    class Meta:
        db_table = u'activity'

class Activity_Movie(models.Model):
    activity = models.ForeignKey(Activity)
    movie = models.ForeignKey(Movie)
    optype = models.IntegerField('操作类型', choices=LYK_ACTIVITY_OP_CHOICES)
    class Meta:
        db_table = u'activitys_movies'

class Activity_Cinema(models.Model):
    activity = models.ForeignKey(Activity)
    cinema = models.ForeignKey(Cinema)
    optype = models.IntegerField('操作类型', choices=LYK_ACTIVITY_OP_CHOICES)
    pintype = models.IntegerField('展现类型', choices=LYK_ACTIVITY_PINTYPE_CHOICES)
    class Meta:
        db_table = u'activitys_cinemas'

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
    createtime = models.DateTimeField('创建时间', auto_now_add=True)
    updatetime = models.DateTimeField('更新时间', auto_now=True)
    class Meta:
        db_table = u'useractivity'

class IP_City(models.Model):
    ip = models.CharField('IP地址', max_length=32)
    cityid = models.CharField('城市id', max_length=100)
    class Meta:
        db_table = u'ip_city'