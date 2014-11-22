#coding=utf-8
class MyIter(object):
    def __init__(self,step):
        self.step = step
    def next(self):
        print "return the next element"
        if self.step ==0:
            raise StopIteration
        self.step-=1
        return self.step
    def __iter__(self):
        print 'retrun the iterator its self'
        return self

for i in MyIter(4):
    print i
    print i

