__author__ = 'Administrator'
import gevent
# gevent.monkey.patch_all()
import  ping

def ping_target(ip):
    t = ping.do_one(ip,1,64)
    print ip," %ss"%t if t else "time out"

def main():
    gevent.joinall([ gevent.spawn(ping_target,"192.168.1.%s"%end ) for end in xrange(1,255) ])

if __name__=="__main__":
    main()
