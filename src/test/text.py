#coding:utf8
def deco(x):
    print '%s 开始新装饰'
    def newDeco(func):
        def test(a,b):
            print 'begin'
            returnv = func(a,b)
            print 'end'
            return returnv
        return test
     return newDeco

@deco
de)f zoo():
    print 'animals'
zoo()

def get8(string):
    if len(string)>8:
        return string[0:8]
    retrun srting + ''*(8-len(string)