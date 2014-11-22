def wrap(fun):
    print fun.__doc__
    return fun


@wrap
def test(**args):
    print args
def mydecorator(function):
    def _mydecorator(*args, **kw):
        # do some stuff before the real
        # function gets called
        res = function(*args, **kw)
        # do some stuff after
        return res
    return _mydecorator

def mydecorator(function):
    def _mydecorator(request):
        res=function(request)
        return res
    return _mydecorator(request)

