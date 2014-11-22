#coding=utf8
#coding:utf-8
import xlrd
import re
wb = xlrd.open_workbook(u'D:/了迎客资料/淘宝售票渠道.xls')




for s in wb.sheets():
    print s.name
    for row in range(s.nrows):
        if s.name =='Table1' and row==0:
            continue
        city_pinyin=s.cell(row,0).value
        taobao_cinema_id = s.cell(row,1).value
        taobao_cinema_name = s.cell(row,2).value
        letv_id = s.cell(row,3).value
        letv_cinema_name = s.cell(row,4).value
        if taobao_cinema_id and letv_id:
            print int(taobao_cinema_id),int(float(str(letv_id)))
