#coding:utf-8
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
def get_mins(show_date,begin_time,end_time):
    # print 'show_date',show_date,type(show_date),begin_time,type(begin_time),end_time,type(end_time)
    start = datetime.datetime.strptime(show_date+' '+begin_time,'%Y-%m-%d %H:%M')
    end = datetime.datetime.strptime(show_date+' '+end_time,'%Y-%m-%d %H:%M')
    # print start ,end
    if end<start:
        end += datetime.timedelta(days=1)
    mins = (end-start).seconds/60
    return str(mins)
def get_hour_mins_format(mark_time):
    if type(mark_time) == type(0.1):
        begin_time = xlrd.xldate_as_tuple(mark_time,0)
        # marked_time = datetime.datetime(begin_time[0],begin_time[1],begin_time[2],begin_time[3],begin_time[4],begin_time[5]).strftime('%H:%M')
        return '%s:%s'%(begin_time[3],begin_time[4])
    else:
        return str(mark_time)

wb = xlrd.open_workbook(u'E:\北京传奇时代（鼎新以厅为基准导出）.xls')

hall_num_seat = ""
for s in wb.sheets():
    print s.name
    cinema_name = s.cell(0,4).value
    show_date = s.cell(7,3).value
    for row in range(s.nrows):
        # for col in range(s.ncols):
        #     print row ,col,s.cell(row,col).value
        #     print s.cell(row,col).value

        if s.cell(row,1).value:
            hall_num_seat = s.cell(row,1).value
        try:
            hall_num_seat_list = hall_num_seat.split(' ')
            hall_num = hall_num_seat_list[0]
            seat_num = re.search('[0-9]*',hall_num_seat_list[1]).group()
            show_date = get_show_date(show_date)
            movie_name_mode = s.cell(row,5).value
            begin_time = get_hour_mins_format(s.cell(row,2).value)
            end_time = get_hour_mins_format(s.cell(row,4).value)
            price = s.cell(row,7).value
            mins = get_mins(show_date,begin_time,end_time)
            try:
                mode = re.search(u'[（\(][\s\S]*[\)）]',unicode(movie_name_mode)).group()
                movie_name =unicode(movie_name_mode).replace(mode,'')
                mode = mode[1:-1]
            except Exception,e:
                mode = ''
                movie_name =movie_name_mode
            if show_date and hall_num and seat_num and show_date and begin_time and end_time and price and mins :
                content = dict(movie_name=movie_name,show_date=show_date,hall_num=hall_num,begin_time=begin_time,end_time=end_time,mins=mins,mode=mode,price=price)
                print content
        except Exception,e:
            # print 2, e
            pass