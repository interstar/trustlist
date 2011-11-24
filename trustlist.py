        
# SETTINGS
        
import tweepy
import settings
import argparse

auth = tweepy.OAuthHandler(settings.CONSUMER_KEY, settings.CONSUMER_SECRET)
auth.set_access_token(settings.ACCESS_KEY, settings.ACCESS_SECRET)
api = tweepy.API(auth)

parser = argparse.ArgumentParser(description='Get seed and list information')
parser.add_argument('-s', '--seed', dest='seed_user', default=settings.seed_user)
parser.add_argument('-l', '--list', dest='list_name', default=settings.list_name)
args = parser.parse_args()
        
# BUILD TRUSTNET

trust_list = []
new_list = []

def get_list(seed_user, list_name) :
    users = api.list_members(seed_user,list_name)
    # Fix for differences between Phil's and Eli's list_members function. (A tweepy issue?)
    # Please remove when we've resolved this
    if not (users[0].__class__.__name__ == "User") :
        users = users[0]
    # End of Fix
    return users    

def buildList(seed_user, list_name):

    users = get_list(seed_user,list_name)
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
            candidates = get_list(user,list_name)

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
    

print buildList(args.seed_user, args.list_name)
        
