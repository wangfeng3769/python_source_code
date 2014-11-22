#coding = utf-8
class API(object):
    def _print_values(self, obj):
        def _print_value(key):
            print 'key:',key
            if key.startswith('_'):
                return ''
            value = getattr(obj, key)
            print 'value:', value
            if not hasattr(value, 'im_func'):
                doc = type(value).__name__
                print'doc:', doc
            else:
                if value.__doc__ is None:
                    doc = 'no docstring'
                else:
                    doc = value.__doc__
            return '%s : %s' % (key, doc)
        res = [_print_value(el) for el in dir(obj)]
        print res
        return '\n'.join([el for el in res if el != ''])
    def __get__(self, instance, klass):
        print instance,klass
        if instance is not None:
            return self._print_values(instance)
        else:
            return self._print_values(klass)

class MyClass(object):
    __doc__ = API()
    def __init__(self):
        self.a = 2
    def meth(self):
        """my method"""
        return 1

#print MyClass.__doc__
print'+++++++'
instance = MyClass()
print '\\\\\\\\\\\\\\\\\\'
print dir(instance)
print instance.__doc__
print '-----------------------'

