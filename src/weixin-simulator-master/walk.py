
#  -*-  coding: utf-8  -*-

import time
import random
import urllib2
import hashlib
import xml.etree.cElementTree as ET

import Tkinter as tk
import ScrolledText as st


settings = {
    # `ToUserName` & `FromUserName` will be placed in the XML data posted to
    # the given URL.
    "ToUserName": "gh_bea8cf2a04fd",
    "FromUserName": "oLXjgjiWeAS1gfe4ECchYewwoyTc",

    # URL of your Wexin handler.
    "url": "http://127.0.0.1:8000/weixin",

    # These will be displayed in GUI.
    "mp_display_name": "APP",
    "me_display_name": "ME",

    # The token you submitted to Weixin MP. Used to generate signature.
    "token": ""
}


template = '''
<xml>
    <ToUserName><![CDATA[%(to)s]]></ToUserName>
    <FromUserName><![CDATA[%(from)s]]></FromUserName>
    <CreateTime>%(time)d</CreateTime>
    <MsgType><![CDATA[text]]></MsgType>
    <Content><![CDATA[%(content)s]]></Content>
    <MsgId>$(id)s</MsgId>
</xml>
'''


def post(url, data):
    request = urllib2.Request(url, data)
    request.add_header("Content-Type", "text/xml")
    response = urllib2.urlopen(request)
    return response.read()


def send():
    s = e.get()
    print s
    if s:
        t.insert(tk.END, settings["me_display_name"]+"\n", "send_name")
        t.insert(tk.END, s+"\n", "send_content")

        msg = {
            "to": settings["ToUserName"],
            "from": settings["FromUserName"],
            "time": time.time(),
            "content": s,
            "id": str(random.random())[-10:],
            }

        qs = "?signature=%s&timestamp=%s&nonce=%s" % \
             mix(int(msg["time"]), msg["id"])
        receive(msg["time"], post(settings["url"], template % msg))


def receive(start, response):
    if time.time() - start > 4.95:
        return

    et = ET.fromstring(response)
    print "Received:\n%s\n" % response

    c = unicode(et.find("Content").text)

    t.insert(tk.END, settings["mp_display_name"]+"\n", "receive_name")
    t.insert(tk.END, c+"\n", "receive_content")


def mix(time, salt):
    timestamp = str(time)
    nonce = str(time + int(salt[-6:]))

    l = [timestamp, nonce, settings["token"]]
    l.sort()
    signature = hashlib.sha1("".join(l)).hexdigest()

    return (signature, timestamp, nonce)


def follow():

    qs = "?signature=%s&timestamp=%s&nonce=%s" % \
         mix(int(msg["time"]), msg["id"])
    receive(msg["time"], post(settings["url"]+qs, template % msg))

def post(url, data):
    request = urllib2.Request(url, data)
    print dir(request)
    print request.host
    print request.type
    print request.get_full_url()
    print request.get_method()
    request.add_header("Content-Type", "text/xml")
    response = urllib2.urlopen(request)
    return response.read()
# data = xml
msg = {
        "to": settings["ToUserName"],
        "from": settings["FromUserName"],
        "time": time.time(),
        "content": "Hello2BizUser",
        "id": str(random.random())[-10:],
        }
# post('http://127.0.0.1:8000/weixin',template % msg)

content =      """<item>
                <Title><![CDATA[%s]]></Title>
                <Description><![CDATA[%s]]></Description>
                <PicUrl><![CDATA[%s]]></PicUrl>
                <Url><![CDATA[%s]]></Url>
                </item>""" %(1,2,3,4)
print content