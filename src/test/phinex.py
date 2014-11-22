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

wb = xlrd.open_workbook(u'E:\珠海中影FACE影城凤凰佳影按片导出（火凤凰3）.xls')
for s in wb.sheets():
    for row in range(s.nrows):
    #            for col in range(s.ncols):
    #                print row,col,s.cell(row,col).value
        try:
            show_date = s.cell(row,0).value
            print type(show_date)
            get_show_date(show_date)
            hall_num = s.cell(row,1).value
            movie_name_mode = s.cell(row,2).value
            begin_time = s.cell(row,3).value
            end_time = s.cell(row,4).value
            price = s.cell(row,5).value
            mins = s.cell(row,6).value
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
                content = dict(movie_name=movie_name,show_date=str(show_date),hall_num=hall_num,begin_time=begin_time,end_time=end_time,mins=mins,mode=mode,price=price)
                # print content
                # contents.append(content)
        except Exception,e:
            print e
