from django.conf.urls import patterns, include, url

# Uncomment the next two lines to enable the admin:
# from django.contrib import admin
# admin.autodiscover()

urlpatterns = patterns('',
    # Examples:
    # url(r'^$', 'leyingkeweb.views.home', name='home'),
    # url(r'^leyingkeweb/', include('leyingkeweb.foo.urls')),
    url(r'^home/info$', 'lykweb.views.home_info', ),
    url(r'^cinema/list$', 'lykweb.views.cinema_list', ),
    url(r'^cinema/info/(.+)$', 'lykweb.views.cinema_info', ),
    url(r'^movie/info/(.+)$', 'lykweb.views.movie_info', ),
    url(r'^header$', 'lykweb.views.header', ),
    url(r'^activity/list$', 'lykweb.views.activity_list', ),
    url(r'^activity/info/(.+)$', 'lykweb.views.activity_info', ),
    url(r'^filmsession/contrast.json$', 'lykweb.views.filmsession_contrast', ),
    url(r'^cinema/mapinfo.json$', 'lykweb.views.cinema_mapinfo', ),
    url(r'^user/login$', 'lykweb.views.user_login', ),
    url(r'^user/logout$', 'lykweb.views.user_logout', ),
    url(r'^user/register$', 'lykweb.views.user_register', ),
    url(r'^user/registerinit$', 'lykweb.views.user_registerinit', ),
    url(r'^user/favorupdate.json$', 'lykweb.views.user_favorupdate', ),
    url(r'^user/info$', 'lykweb.views.user_info', ),
    url(r'^user/update$', 'lykweb.views.user_update', ),
    url(r'^user/phonenumvaild$', 'lykweb.views.user_phonenumvaild', ),
    url(r'^user/phonebind$', 'lykweb.views.user_phonebind', ),
    url(r'^client/download$', 'lykweb.views.client_download', ),
    url(r'^redirect$', 'lykweb.views.redirect', ),
    url(r'^cinema/filmsessions$', 'lykweb.views.cinema_filmsessions_ajax', ),




    url(r'^home/demo$', 'lykweb.views.home_demo', ),
    url(r'^movie/demo/(.+)$', 'lykweb.views.movie_demo', ),

    # Uncomment the admin/doc line below to enable admin documentation:
    # url(r'^admin/doc/', include('django.contrib.admindocs.urls')),

    # Uncomment the next line to enable the admin:
    # url(r'^admin/', include(admin.site.urls)),
)
