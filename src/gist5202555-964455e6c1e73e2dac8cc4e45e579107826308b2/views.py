# coding:utf-8
from django.http import HttpResponse

import xml.etree.ElementTree as ET
import hashlib

TOKEN = "hahaha"

def wechat(request):
    if request.method == 'GET':
        response = HttpResponse(checkSignature(request), content_type="text/plain")
        return response
    elif request.method == 'POST':
        response = HttpResponse(responseMsg(request), content_type="application/xml")
        return response

def checkSignature(request):
    global TOKEN
    signature = request.GET.get("signature", None)
    timestamp = request.GET.get("timestamp", None)
    nonce = request.GET.get("nonce", None)
    echoStr = request.GET.get("echostr",None)

    token = TOKEN
    tmpList = [token,timestamp,nonce]
    tmpList.sort()
    tmpstr = "%s%s%s" % tuple(tmpList)
    tmpstr = hashlib.sha1(tmpstr).hexdigest()
    if tmpstr == signature:
       return echoStr

def responseMsg(request):
    xml = ET.fromstring(request.raw_post_data)
    content = xml.find("Content").text
    fromUserName = xml.find("ToUserName").text
    toUserName = xml.find("FromUserName").text
    postTime = str(int(time.time()))
    
    reply = """<xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[text]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            <FuncFlag>0</FuncFlag>
                    </xml>"""
    return HttpResponse(reply % (toUserName, fromUserName, postTime, "hello"))
