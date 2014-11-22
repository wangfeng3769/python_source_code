from django.conf.urls.defaults import patterns, include, url
# Uncomment the next two lines to enable the admin:
#from django.contrib import admin
import settings

#admin.autodiscover()
urlpatterns = patterns('',
    (r'^media/(?P<path>.*)$', 'django.views.static.serve',{'document_root':settings.MEDIA_ROOT}),
    # Examples:
    # url(r'^$', 'lykbk.views.home', name='home'),
    # url(r'^lykbk/', include('lykbk.foo.urls')),

    # Uncomment the admin/doc line below to enable admin documentation:
#    url(r'^admin/doc/', include('django.contrib.admindocs.urls')),

    # Uncomment the next line to enable the admin:

    #    url(r'^admin/login/$', 'django.contrib.auth.views.login'),
    url(r'^$', 'django.contrib.auth.views.login'),
    url(r'^accounts/login/$', 'django.contrib.auth.views.login'),
    url(r'^accounts/profile/$','release.views.index'),
    url(r'^accounts/logout/$','django.contrib.auth.views.logout_then_login'),
    url(r'^accounts/password_change/$','accounts.views.password_change'),
    url(r'^accounts/password_change_done/$','accounts.views.password_change_done')
#    url(r'^login/', include(admin.site.urls)),
#    #    url(r'', include('cms.urls')),
#    url(r'^register/', include('register1.urls')),
#    url(r'^upload/',include('upload.urls'))
#    url(r'^register/', 'register.views.register')

)
