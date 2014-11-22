#coding=utf-8
import xlrd
import re
import datetime
wb = xlrd.open_workbook(u'D:/排片表/火凤凰/23日拍片-火凤凰.xls')
for s in wb.sheets():
    print s.name
#    cinema_name = s.cell(0,4).value
#    show_day = s.cell(0,16).value
    for row in range(s.nrows):
        for col in range(s.ncols):
            print row,col,s.cell(row,col).value
        try:
            movie_name_mode = s.cell(row,1).value
            cinema_name = s.cell(row,3).value
            hall_num = s.cell(row,5).value
            begin_time = s.cell(row,7).value
            end_time = s.cell(row,9).value
            show_date = s.cell(row,2).value
#            version = s.cell(row,16).value
            price1 = s.cell(row,13).value
            try:
                mode = re.search(u'[（\(][\s\S]*[\)）]',unicode(movie_name_mode)).group()
                movie_name =unicode(movie_name_mode).replace(mode,'')
                mode = mode[1:-1]
            except Exception,e:
                mode = ''
                movie_name =movie_name_mode
            print movie_name,mode

        except Exception,e:
            print e

