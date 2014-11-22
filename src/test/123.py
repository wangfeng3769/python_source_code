#coding=utf-8
import xlrd
import re
import datetime
def get_show_date(show_date):
    if type(show_date) == type(0.1):
        __s_date = datetime.date(1899, 12, 31).toordinal()-1
        show_date = datetime.date.fromordinal(int(show_date)+__s_date)
        return str(show_date)
    else:
        return str(show_date)
def judge_head(s):
    if s.cell(0,0).value == u'日期'and s.cell(0,1).value==u'影厅' and s.cell(0,2).value == u'场次' and\
        s.cell(0,3).value == u'影片名称' and s.cell(0,4).value == u'国别' and s.cell(0,5).value == u'开始时间' and\
        s.cell(0,6).value == u'片长' and s.cell(0,7).value == u'票价':
        pass
    else:
        raise Exception


wb = xlrd.open_workbook(u'D:\火烈鸟排片表.xls')
for s in wb.sheets():
    for row in range(s.nrows):
        if row==0:
            if s.cell(0,0).value == u'日期'and s.cell(0,1).value==u'影厅' and s.cell(0,2).value == u'场次' and \
                    s.cell(0,3).value == u'影片名称' and s.cell(0,4).value == u'国别' and s.cell(0,5).value == u'开始时间' and \
                    s.cell(0,6).value == u'片长' and s.cell(0,7).value == u'票价':
                pass
            else:
                raise Exception
        try:
            show_date = s.cell(row,0).value
            print type(show_date)
            show_date = get_show_date(show_date)
            print show_date
            hall_num = s.cell(row,1).value
            movie_name_mode = s.cell(row,3).value
            begin_time = s.cell(row,5).value
            # end_time = s.cell(row,4).value
            print type(int(s.cell(row,6).value))
            price = '%d'% s.cell(row,6).value
            mins = s.cell(row,7).value
            begin_time = xlrd.xldate_as_tuple(begin_time,0)
            begin_time = '%s:%s'%begin_time[3:5]
            deatail_time = show_date+' '+begin_time
            end_time =(datetime.datetime.strptime(deatail_time, "%Y-%m-%d %H:%M")+datetime.timedelta(seconds=mins*60)).strftime("%H:%M")
            mins = "%d"%s.cell(row,7).value
            try:
                print'------------------'
                sequence = re.split(u'[（\(\)） ]',movie_name_mode)
                movie_name = sequence[0]
                print movie_name
            except Exception,e:
                print e
            try:
                sequence = re.split(u'[（\(\)） ]',movie_name_mode)
                movie_name = sequence[0]
                mode = re.search(u'[（\(][\s\S]*[\)）]{0,100}',unicode(movie_name_mode)).group()
                mode = re.split(u'[（\(\)）]',mode)[1]
                # movie_name =unicode(movie_name_mode).replace(mode,'')
                # mode = mode[1:-1]
            except Exception,e:
                mode = ''
                movie_name =movie_name_mode
            print mode
            if show_date:
                # content = dict(movie_name=movie_name,show_date=str(show_date),hall_num=hall_num,begin_time=begin_time,end_time=end_time,mins=mins,mode=mode,price=price)
                # print content
                # contents.append(content)
                pass
        except Exception,e:
            print e
