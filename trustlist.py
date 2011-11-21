# import tweepy
# import auth
# import MySQLdb
# from datetime import datetime
# from dateutil.relativedelta import *
# from settings import *
# import sys
# import warnings
# import logging, logging.handlers
# import string

        
# SETTINGS
        
import tweepy

CONSUMER_KEY = ''
CONSUMER_SECRET = ''

ACCESS_KEY = ''
ACCESS_SECRET = ''

auth = tweepy.OAuthHandler(CONSUMER_KEY, CONSUMER_SECRET)
auth.set_access_token(ACCESS_KEY, ACCESS_SECRET)
api = tweepy.API(auth)

# seed_user = 'webisteme'
# seed_list =    'punkmoney-trusted'    
        
# BUILD TRUSTNET

trust_list = []
new_list = []

def buildList(seed_user, list_name):

    users = api.list_members(seed_user,list_name)[0]
    
    for user in users:
        trust_list.append(user.screen_name.lower())

    # crawl deeper    
    
    new_list = crawlDeeper(trust_list, list_name)
    while len(new_list) > 0 : new_list = crawlDeeper(new_list, list_name)
        
    # update database
    
    return trust_list
        
        
# CRAWL DEEPER (only call from buildList())

def crawlDeeper(list, list_name):
    new_list[:] = []
    for user in list:
        print 'checking %s' % user
        user = user.lower()
        try:
            candidates = api.list_members(user,list_name)[0]
            for candidate in candidates:
                print '--checking candidate %s isn\'t already in trust list' % candidate.screen_name
                try:
                    trust_list.index(candidate.screen_name.lower())
                except:
                    print '--adding user %s to trust list' % candidate.screen_name.lower()
                    trust_list.append(candidate.screen_name.lower())
                    new_list.append(candidate.screen_name.lower())
        except:
            continue
    return new_list
    

print buildList('webisteme', 'punkmoney-trusted')
        