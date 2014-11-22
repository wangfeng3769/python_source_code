#class Data(object):
#    def __init__(self, *args):
#        self._data = list(args)
#
#    def __iter__(self):
#        return Iter(self)
#
#
#class Iter(object):
#    def __init__(self, data):
#        self._index = 0
#        self._data = data._data
#
#    def next(self):
#        if self._index >= len(self._data): raise StopIteration()
#        d = self._data[self._index]
#        self._index += 1
#        return d
#
#d = Data(1, 2, 3)
#for x in d:
#    print x
#def coroutine():
#    print "coroutine start..."
#    result = None
#    while True:
#        s = yield result
#        result = s.split(",")
#c = coroutine()
#c.send(1)
##c.send(None)
class User(object):
    def __new__(self, *args, **kwargs):
        print "__new__", self, args, kwargs
        return object.__new__(self)
    def __init__(self, name, age):
        print "__init__", name, age

    def __del__(self):
        print "__del__"

u=User('Tom',50)
string = 'alt'
print '-------'
print string.endswith('a')