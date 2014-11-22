class myIterator(object):
    def __init__(self,step):
        self.step = step
    def next(self):
        print 'next'
        if self.step == 0:
            raise StopIteration
        self.step-=1
        return self.step
    def __iter__(self):
        print 'iter'
        return self


for i in myIterator(5):
    print i