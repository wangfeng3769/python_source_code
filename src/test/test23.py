#coding=utf-8
class F(object):
    __slots__ = ['ice','cream']
print dir(F)
g =F()
g.ice =1

from threading import Thread
class myclass(Thread):
    def __init__(self):
        pass

from threading import Thread
class MyThread(Thread):
    def __init__(self):
        print 'sss'
        pass
MyThread()

class Base(object):
    def __secret(self):
        print"don't tell"
    def public(self):
        self.__secret()
Base()._Base__secret()
Base.public()