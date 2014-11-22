#coding=utf-8
__author__ = 'Administrator'
import xml.etree.ElementTree as ET

xml = """ <xml>
 <ToUserName><![CDATA[toUser]]></ToUserName>
 <FromUserName><![CDATA[fromUser]]></FromUserName>
 <CreateTime>1348831860</CreateTime>
 <MsgType><![CDATA[text]]></MsgType>
 <Content><![CDATA[this is a test]]></Content>
 <MsgId>1234567890123456</MsgId>
 </xml>
"""
root = ET.fromstring(xml)
print dict([(child.tag,child.text) for child in root])
