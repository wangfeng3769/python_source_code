# coding:utf-8
from register1.models import User
from django.http import HttpResponseRedirect
from django.shortcuts import render_to_response
from django.template.response import TemplateResponse
from django.contrib.auth.forms import AuthenticationForm as authentication_form

def register(request,current_app = 'register',template_name = 'registration/register.html'):
    username = request.REQUEST.get('username', '')
    password = request.REQUEST.get('password', '')
    if username and password:
        try:
            if User.objects.get(username = username):
                return HttpResponseRedirect('/../register/?ret_info = %s&' % u"你的用户名已被注册")

        except Exception,e:
            user = User.objects.create_user(username=username,password=password)
            user.is_staff = True
            user.save()
            return HttpResponseRedirect('/')
    else:
        context = {
            'form':authentication_form(request)
        }
        return TemplateResponse(request, template_name, context,
            current_app=current_app)
        return TemplateResponse('registration/register.html',{})



