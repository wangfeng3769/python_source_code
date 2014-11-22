#coding=utf-8
import xlrd
import re
import datetime
wb = xlrd.open_workbook(u'D:/河北黑龙江河南湖北.xlsx')
for s in wb.sheets():
    print s.name
    cinema_name = s.cell(0,4).value
    for row in range(s.nrows):
        if row==0:
            continue
        # for col in range(s.ncols):
        #     print row,col,':', s.cell(row,col).value
        letv_cinema_id = s.cell(row,2).value.strip()
        zzb_id = s.cell(row,5).value.strip()
        zzb_cinema_name = s.cell(row,4).value.strip()
        try:
            cinema_zzb = Cinema_ZZB.objects.get(zzb_id = zzb_id)
        except Exception:
            cinema_zzb = Cinema_ZZB()
        if not cinema_zzb:
            if zzb_id:
                cinema_zzb.zzb_id = zzb_id
            if zzb_cinema_name:
                cinema_zzb.zzb_cinema_name=zzb_cinema_name
        if  letv_cinema_id:
            cinema_zzb.cinema_id=letv_cinema_id
            cinema_zzb.save()
        try:
            cinema = Cinema.objects.get(id=int(letv_cinema_id))
            if Cinema_ZZB.objects.get(zzb_id=zzb_id):
                zzb_circuit_name = Cinema_ZZB.objects.get(zzb_id=zzb_id).zzb_circuit_name
                circuit_id = Circuit.objects.get(zzb_circuit_name__contains=zzb_circuit_name).id
                cinema.circuit_id = circuit_id
                cinema.save()
        except Exception,e:
            print e

def info_import():
    zzb_cinema_list = Cinema_ZZB.objects.all()
    for zzb_cinema in zzb_cinema_list:
        if Circuit.objects.get(name__contains=zzb_cinema.zzb_circuit_name):
        else:
            Circuit.objects.create(name =zzb_cinema.zzb_circuit_name)

