        
# SETTINGS
        
import tweepy
import settings
import argparse
import netdb
import datetime

auth = tweepy.OAuthHandler(settings.CONSUMER_KEY, settings.CONSUMER_SECRET)
auth.set_access_token(settings.ACCESS_KEY, settings.ACCESS_SECRET)
api = tweepy.API(auth)

parser = argparse.ArgumentParser(description='Get seed and list information')
parser.add_argument('-s', '--seed', dest='seed_user', default=settings.seed_user)
parser.add_argument('-l', '--list', dest='list_name', default=settings.list_name)
parser.add_argument('-d', '--dot', dest='dot_file_name')
parser.add_argument('-n', '--net', dest='net_file_name')
args = parser.parse_args()
        
# BUILD TRUSTNET

trust_list = []
new_list = []

dotfile = None
netfile = None

if args.dot_file_name != None:
    dotfile = open(args.dot_file_name, 'w+')
    dotfile.write("digraph G {\n")

def buildList(seed_user, list_name):
    users = api.list_members(seed_user,list_name)

    # Fix for differences between Phil's and Eli's list_members function. (A tweepy issue?)
    # Please remove when we've resolved this
    if not (users[0].__class__.__name__ == "User") :
        users = users[0]
    # End of Fix

    for user in users:
        trust_list.append(user.screen_name.lower())

    # crawl deeper    
    
    new_list = crawlDeeper(trust_list, list_name)
    while len(new_list) > 0 : new_list = crawlDeeper(new_list, list_name)
        
    # update database

    if args.dot_file_name != None:
        dotfile.write("}\n")
    
    return trust_list
        
        
# CRAWL DEEPER (only call from buildList())

def crawlDeeper(list, list_name):
    new_list[:] = []
    for user in list:
        print 'checking %s' % user
        user = user.lower()
        try:
            candidates = api.list_members(user,list_name)

            # Fix for differences between Phil's and Eli's list_members function. (A tweepy issue?)
            # Please remove when we've resolved this
            print candidates
            if not (candidates[0].__class__.__name__ == "User") :
                candidates = candidates[0]
            # End of Fix


            for candidate in candidates:
                print '--checking candidate %s isn\'t already in trust list' % candidate.screen_name
#makeobservation('interstar','1mentat', 'tne-github', datetime.datetime.now())
                try:
                    netdb.makeobservation(user, candidate.screen_name.lower(), list_name, datetime.datetime.now())
                except:
                    print "Unexpected error:", sys.exc_info()[0]
                    print "netdb observation failed"
                if args.dot_file_name != None:
                    dotfile.write("    \"{0}\" -> \"{1}\"\n".format(user, candidate.screen_name.lower()))
                try:
                    trust_list.index(candidate.screen_name.lower())
                except:
                    print '--adding user %s to trust list' % candidate.screen_name.lower()
                    trust_list.append(candidate.screen_name.lower())
                    new_list.append(candidate.screen_name.lower())
        except:
            continue
    return new_list
    
netdb.setupdb()
print buildList(args.seed_user, args.list_name)

if args.net_file_name != None:
    graph = netdb.rendergraph(args.list_name)
    netfile = open(args.net_file_name, 'w+')
    netfile.write(graph)
