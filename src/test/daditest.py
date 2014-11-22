#coding=utf-8
import xlrd
import re
import datetime
wb = xlrd.open_workbook(u'D:/大地基于厅导出.xls')
def judge_head(s):
    hall = s.cell(0,0).value
    movie_name = s.cell(0,1).value
    movie_class = s.cell(0,2).value
    movie_length = s.cell(0,3).value
    begin_time = s.cell(0,4).value
    end_time = s.cell(0,5).value
    if (hall==u'厅名') and (movie_name==u'影片名') and (movie_class==u'影片级别') and (movie_length==u'片长（分钟）')and(begin_time==u'开场时间') and(end_time==u'散场时间'):
        return True
    else:
        return False
show_date = datetime.datetime.today().strftime("%Y-%m-%d")
hall_num_last = ''
for s in wb.sheets():
    if not judge_head(s):
        raise Exception
    for row in range(s.nrows):
        movie_name_mode = begin_time = end_time = mins = None
        if row==0:
            continue
        if s.cell(row,0).value:
            hall_num_last = s.cell(row,0).value
        try:
            print hall_num_last
            hall_num = hall_num_last
            movie_name_mode = s.cell(row,1).value
            begin_time = s.cell(row,4).value
            end_time = s.cell(row,5).value
            mins = s.cell(row,3).value
            try:
                mode = re.search(u'[（\(][\s\S]*[\)）]',unicode(movie_name_mode)).group()
                movie_name =unicode(movie_name_mode).replace(mode,'')
                mode = mode[1:-1]
            except Exception,e:
                mode = ''
                movie_name =movie_name_mode
            print movie_name,mode
            begin_time = xlrd.xldate_as_tuple(begin_time, 0)
            end_time = xlrd.xldate_as_tuple(end_time, 0)
            begin_time1 ='%s:%s' % begin_time[3:5]
            end_time1 = '%s:%s' % end_time[3:5]
            if movie_name_mode and begin_time and end_time and mins:
                content = dict(movie_name=movie_name,show_date=show_date,hall_num=hall_num,begin_time=begin_time1,end_time=end_time1,mins=mins,mode=mode)
        except Exception,e:
            print e
