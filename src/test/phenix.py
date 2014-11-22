#coding=utf-8
import xlrd
import re
import datetime
def get_show_date(show_date):
    if type(show_date) == type(0.1):
        __s_date = datetime.date(1899, 12, 31).toordinal()-1

wb = xlrd.open_workbook(u'D:\排片表\火凤凰\湘潭左岸国际影城-11.22排片-火凤凰系统.xls')
for s in wb.sheets():
    print s.name
    cinema_name = s.cell(0,4).value
    #    show_day = s.cell(0,16).value
    for row in range(s.nrows):
        for col in range(s.ncols):
            print row,col,s.cell(row,col).value
        try:
            show_date = s.cell(row,0).value.strip()
            hall_num = s.cell(row,1).value
            movie_name_mode = s.cell(row,2).value
            begin_time = s.cell(row,3).value
            end_time = s.cell(row,4).value
            price = s.cell(row,5).value
            mins = s.cell(row,6).value
            try:
                mode = re.search(u'[（\(][\s\S]*[\)）]',unicode(movie_name_mode)).group()
                movie_name =unicode(movie_name_mode).replace(mode,'')
                mode = mode[1:-1]
            except Exception,e:
                mode = ''
                movie_name =movie_name_mode
            print movie_name,mode
            if show_date:
                content = dict(movie_name=movie_name,show_date=show_date,hall_num=hall_num,begin_time=begin_time,end_time=end_time,mins=mins,mode=mode,price=price)
        except Exception,e:
            print e

