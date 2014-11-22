DEBUG = True
TEMPLATE_DEBUG = DEBUG
DATABASES = {
    'default': {
        'ENGINE': 'django.db.backends.mysql',
        'NAME': 'leyingke',
        'USER': 'root',
        'PASSWORD': '3769',
        'HOST': '127.0.0.1',
        'PORT': '3306',
        'OPTIONS': {
            'init_command': 'SET storage_engine=INNODB',
            },
        },
#    'leyingke': {
#        'ENGINE': 'django.db.backends.mysql',
#        'NAME': 'leyingke',
#        'USER': 'root',
#        'PASSWORD': 'lyw1606mysql',
#        'HOST': '10.200.92.237',
#        'PORT': '',
#        'OPTIONS': {
#            #'init_command': 'SET storage_engine=INNODB',
#        },
#        },
    }
CACHES = {
    'default': {
        'BACKEND': 'redis_cache.RedisCache',
        'LOCATION': '10.200.92.237:6379',
        'OPTIONS': {
            'DB': 2,
            'PARSER_CLASS': 'redis.connection.HiredisParser'
        },
        },
    }
REDIS_HOST = '10.200.92.237'
REDIS_PORT = 6379
#MEDIA_ROOT = '/home/www/media'
#MEDIA_ROOT = './media/'
PYAPNS_CONFIG = {
    'HOST': 'http://10.200.92.238:7077/',
    }

BROKER_URL = 'redis://10.200.92.237:6379/2'
