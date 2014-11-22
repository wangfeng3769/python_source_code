#coding=utf-8
import xlrd
import re
import datetime
wb = xlrd.open_workbook(u'D:\排片表\ｖｉｓｔａ\重庆沃美影城11.23片表.xls')
for s in wb.sheets():
    print s.name
    cinema_name = s.cell(0,4).value
    show_day = s.cell(0,16).value
    for row in range(s.nrows):
#        for col in range(s.ncols):
#            print row,col,s.cell(row,col).value
        try:
            __s_date = datetime.date(1899, 12, 31).toordinal()-1
            movie_name_mode = s.cell(row,1).value
            cinema_name = s.cell(row,3).value
            hall_num = s.cell(row,4).value
            business_date = s.cell(row,5).value
            begin_time = s.cell(row,6).value
            r_begin_time = s.cell(row,7).value   # 真实放映时间
            r_r_begin_time = s.cell(row,8).value # 正片放映时间
            r_int = int(r_r_begin_time)
            show_date = datetime.date.fromordinal(r_int+__s_date)
            print show_date
            end_time = s.cell(row,9).value       # 结束时间
            seat_num = int(s.cell(row,14).value) #
            version = s.cell(row,16).value
            price1 = s.cell(row,19).value
            print price1
            price = re.search(r'\d{1,3}',price1).group()
            try:
                mode = re.search(u'[（\(][\s\S]*[\)）]',unicode(movie_name_mode)).group()
                movie_name =unicode(movie_name_mode).replace(mode,'')
                mode = mode[1:-1]
            except Exception,e:
                mode = ''
                movie_name =movie_name_mode
            print movie_name,mode
            begin_time = xlrd.xldate_as_tuple(r_r_begin_time,0)
            end_time = xlrd.xldate_as_tuple(end_time,0)
            print datetime.datetime(begin_time[0],begin_time[1],begin_time[2],begin_time[3],begin_time[4],begin_time[5])
            print datetime.datetime(end_time[0],end_time[1],end_time[2],end_time[3],end_time[4],end_time[5])
        except Exception,e:
            print e

