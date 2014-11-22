DEBUG = True
TEMPLATE_DEBUG = DEBUG
DATABASES = {
    'default': {
        'ENGINE': 'django.db.backends.mysql',
        'NAME': 'leyingke',
        'USER': 'root',
        'PASSWORD': 'lyw1606mysql',
        'HOST': '10.200.92.237',
        'PORT': '',
        'OPTIONS': {
            'init_command': 'SET storage_engine=INNODB',
            },
        },
    'leyingkeweb': {
        'ENGINE': 'django.db.backends.mysql',
        'NAME': 'leyingke',
        'USER': 'root',
        'PASSWORD': 'lyw1606mysql',
        'HOST': '10.200.92.237',
        'PORT': '',
        'OPTIONS': {
            #'init_command': 'SET storage_engine=INNODB',
        },
        },
    }
CACHES = {
    'default': {
        'BACKEND': 'redis_cache.RedisCache',
        'LOCATION': '10.200.92.237:6379',
        'TIMEOUT': 3600,
        'OPTIONS': {
            'DB': 4,
            'MAX_ENTRIES': 10000,
            },
        },
    }
REDIS_HOST = '10.200.92.237'
REDIS_PORT = 6379
