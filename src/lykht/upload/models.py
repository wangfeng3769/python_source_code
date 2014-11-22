#coding:utf-8
from django.db import models
from backends.models import Cinema,Movie,City
import os
#class FilmsessionFile(models.Model): # todo 组id
#        showtime = models.DateTimeField('放映时间', null=True)
#        price = models.IntegerField('现价', null=True)
#        price_ori = models.IntegerField('原价', null=True)
#        language_version = models.CharField('语言版本', max_length=20, null=True)
#        screening_mode = models.CharField('屏幕类型', max_length=20, null=True)
#        date = models.DateField('排场日期-用于抓取排重')
#        movie = models.ForeignKey(Movie, to_field='outid')
#        cinema = models.ForeignKey(Cinema, to_field='outid')
#        hall_num = models.CharField('影厅号', max_length=100, null=True)
#        city = models.ForeignKey(City, to_field='cityid')
#        showdate = models.CharField('放映日期',max_length=100)
#        start = models.CharField('放映时间',max_length=100)
#        end = models.CharField('结束时间',max_length=100)
#        mins = models.CharField('片长',max_length=50)
#        class Meta:
#            db_table = u'filmsessionfile'
#            verbose_name = '影片排场备份'
#            verbose_name_plural ='影片排场备份'

