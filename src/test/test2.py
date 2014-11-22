#coding:utf-8
import xlrd
import re
import datetime
import chardet
from xlrd import open_workbook
wb = open_workbook(u'E:\满天星.xls')
for s in wb.sheets():

    if s.cell(0,0).value:
        date1 = re.search(u'\d{4}.*\d{1,2}.*\d{1,2}',s.cell(0,0).value).group()
        date2= date1.replace(u'年','-').replace(u'月','-')
    for row in range(s.nrows):
        if s.cell(row,0).value:
            movie_name_mode = s.cell(row,0).value
            try:
                mode = re.search(u'[（\(][\s\S]*[\)）]',unicode(movie_name_mode)).group()
                print 'mode:%s'% mode
                print "++++++++++++++++++++++++++++++++++++++"
                movie_name = unicode(movie_name_mode).replace(mode,'')
                mode = mode[1:-1]
                print mode
            except Exception,e:
                print e
                mode =''
                movie_name =movie_name_mode
            print movie_name,mode
        if s.cell(row,10):
            price_ori = re.search(r'\d*',str(s.cell(row,10).value).strip())
            if price_ori:
                price = price_ori.group()
                print price
        for ncol in  range(s.ncols):
            if row ==0:
                hall_num=date_tuple=begin_time=None
                continue
            try:
                if s.cell(row,ncol).value:
                    if re.search(U'厅',s.cell(row,ncol).value):
                        hall_num =s.cell(row,ncol).value
                        date_tuple = xlrd.xldate_as_tuple(s.cell(row+1,ncol).value, 0)
                        begin_time ='%s:%s'%(date_tuple[3],date_tuple[4])
#                        print hall_num,date_tuple,begin_time,price,movie_name,mode,date2

                else:
                    hall_num = None
                    begin_time =None
            except:
                hall_num = None
                begin_time =None
            print'-----------------------------------------------'
            if date2 and movie_name and hall_num and date_tuple and begin_time:
                print date2 ,movie_name ,mode , hall_num , date_tuple ,begin_time,price

            print'------------------------------------------------'








