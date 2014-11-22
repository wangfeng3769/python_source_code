#coding:utf-8
import xlrd
import re
wb = xlrd.open_workbook(u'E:\火凤凰1.15.16.xls')
for s in wb.sheets():
    print s.name
    cinema_name = s.cell(0,4).value
    show_day = s.cell(0,16).value
    for row in range(s.nrows):
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
        try:
            if show_date and hall and movie_name_mode and begin_time and end_time and price and mins:
                try:
                    mode = re.search(u'[\（\(][\s\S]*[\)\）]',movie_name_mode.decode('utf-8')).group()
                    print 'mode:%s'% mode
                    movie_name = movie_name_mode.decode('utf-8').replace(mode,'')
                    mode = mode[1:-1]
                    print'-----------------------'
                    print len(mode)
                except Exception,e:
                    print e
                    mode ='==============='
                    movie_name =movie_name_mode
                print show_date;print hall;print movie_name;print mode;print begin_time; print end_time;print price;print mins;
        except Exception,e:
            print  e
            print '100000'




