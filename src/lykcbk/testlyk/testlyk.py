# -*- coding: utf-8 -*-
from datetime import datetime, date
import json
import requests

# 影片详情
import time

def test_movie_info():
    j = {
        "mac": "E8:9A:8F:CB:B9:4F",
        "device_name": "iphone",
        "device_version": "ios4.3",
        "device_model": "iphone4",
        "screen_size": "640*960",
        "client_version": "0.9"
    }
    url = 'http://127.0.0.1:8000/v1/mobile/movie/info/2'
    data = {'atominfo': json.dumps(j)}
#    response = requests.post(url, data, headers={'If-Modified-Since':str(datetime.now().strftime('%a, %d %b %Y %X GMT'))})
    last = ''
    response = None
    for i in range(10):
        if not last:
            response = requests.get(url, headers={'If-Modified-Since':str(datetime.now().strftime('%a, %d %b %Y %X GMT'))})
            last = response.headers['Last-Modified']
            print response.status_code
            print response.content
        else:
            response = requests.get(url, headers={'If-Modified-Since':last})
            if response.status_code == 200:
                last = response.headers['Last-Modified']
            print response.status_code
            print response.content
        time.sleep(10)
#    response = requests.get(url)


if __name__ == '__main__':
    print 1
#    test_movie_info()
#    now = datetime.now()
#    print now
#    print date(*now[0,3])

#    from PIL import Image, ImageOps
#    from qrencode import Encoder
#    cre = Encoder()
#    img = cre.encode("xudong,Fighting!", { 'width': 300 })
#    img.save('out.png')


#    try:
#    from elaphe import barcode
    from EpsImagePlugin import EpsImageFile
#    img = barcode('qrcode','Hello Barcode Writer In Pure PostScript.',options=dict(version=9, eclevel='M'),margin=10, data_mode='8bits')
#    print img.show()
#    except Exception:
#        pass

    url = 'http://api.leyingkeweb.com/lykweb/v1/mobile/favor/list/0?lon=116.326010&lat=40.002534'
    # 'http://api.leyingkeweb.com/lykweb/v1/mobile/favor/update/0/1/2061'
    response = requests.get(url, headers={'User-Agent': json.dumps({'sid': 'qimib-1'})})
    print response.content
