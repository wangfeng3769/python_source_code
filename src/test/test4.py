#coding=utf-8
#coding:utf-8
import xlrd
import re
wb = xlrd.open_workbook(u'E:\满天星_天津_佳映天悦_1.19.xls')
for s in wb.sheets():
    print s.name
    cinema_name = s.cell(0,4).value
    show_day = s.cell(0,16).value
    for row in range(s.nrows):
        show_date = hall = begin_time = end_time = price = movie_name_mode =None
        show_date1 = s.cell(row,1).value
        if re.search('\d{4}-\d{1,2}-\d{1,2}',show_date1):
            show_date = show_date1
        hall1 = s.cell(row,5).value
        if re.search(u'厅',hall1):
            hall =hall1
        movie_name_mode = s.cell(row,7).value.encode('utf8')
        begin_time1 = s.cell(row,10).value
        if re.search('\d{1,2}:\d{1,2}',begin_time1):
            begin_time = begin_time1
        end_time1 = s.cell(row,13).value
        if re.search('\d{1,2}:\d{1,2}',end_time1):
            end_time = end_time1
        price1 = s.cell(row,15).value
        if re.search('\d{1,100}',price1):
            price = price1
        mins1 = s.cell(row,17).value
        if re.search('\d{1,100}',mins1):
            mins = mins1

        if show_date and hall and begin_time and end_time and price and movie_name_mode:
            try:
                print'================================'
                mode = re.search(u'[（\(][\s\S]*[\)）]',unicode(movie_name_mode,'utf-8')).group()
                print 'mode:%s'% mode
                movie_name =unicode(movie_name_mode,'utf-8').replace(mode,'')
                mode = mode[1:-1]
                print'-----------------------'
                print len(mode)
            except Exception,e:
                print e
                mode = ''
                movie_name =movie_name_mode
            try:
                print '+++++++++++++'
                floor = re.search(u'[（\(][\s\S]*[\)）]',unicode(hall)).group()
                print floor
                hall_num=unicode(hall).replace(floor,'')
            except Exception,e:
                print e
                hall_num=hall
                pass
            print show_date, movie_name, mode, begin_time, end_time,price, mins,hall_num,mode




