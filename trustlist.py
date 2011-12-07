        
# SETTINGS
        
import tweepy
import settings
import argparse
import useful

import netdb
import datetime
import sys # E

auth = tweepy.OAuthHandler(settings.CONSUMER_KEY, settings.CONSUMER_SECRET)
auth.set_access_token(settings.ACCESS_KEY, settings.ACCESS_SECRET)
api = tweepy.API(auth)

parser = argparse.ArgumentParser(description='Get seed and list information')
parser.add_argument('-s', '--seed', dest='seed_user', default=settings.seed_user)
parser.add_argument('-l', '--list', dest='list_name', default=settings.list_name)
parser.add_argument('-d', '--dot', dest='dot_file_name')
parser.add_argument('-w', action='store_true',default=False) # generates Phil's web format
parser.add_argument('-n', '--net', dest='net_file_name')

args = parser.parse_args()

       
# BUILD TRUSTNET

trust_list = []
new_list = []
dotfile = None
netfile = None
cloudfile = None

if args.w :
    names = (args.seed_user,args.list_name)
    args.dot_file_name = "%s.%s.dot" % names
    args.net_file_name = "%s.%s.net" % names
    args.cloud_file_name = "%s.%s" % names

# E

print args.seed_user
print args.list_name
print args.dot_file_name
print args.net_file_name
print args.cloud_file_name



def get_list(seed_user, list_name) :
    try :
        users = api.list_members(seed_user,list_name)
        # Fix for differences between Phil's and Eli's list_members function. (A tweepy issue?)
        # Please remove when we've resolved this
        if not (users[0].__class__.__name__ == "User") :
            users = users[0]
        # End of Fix
    except Exception, e : 
        #print e,e.__class__
        users = []
    return users    


def buildList(seed_user, list_name):

    users = get_list(seed_user,list_name)
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
            candidates = get_list(user,list_name)

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




# Phil's Alternative Crawler
# An alternative recursive crawler (not using build_list and crawl_deeper) that builds trust-lists into a SetDict (ie. dictionary of sets)
# One set is created for each layer of depth / distance from the root user
# The SetDict has a pp (pretty print) which can output data suitable for another program to format (eg. into a web-page)
# This also updates the .dot file

# This is an experiment, it's quite compact, and closer to the way I tend to write code these days. 
# See if it's the style you'd like to use

visited = useful.SetDict()

def recurse(depth, user_name, list_name) :
    """ The recursive step, crawls the tree and fills the "visited" SetDict.
    Breadth-first search. (So that we place people as high as they deserve in the depth tree)"""
    people = [p.screen_name.lower() for p in get_list(user_name,list_name)]
    queue = []
    for p in people :
        if dotfile : dotfile.write('    "%s" -> "%s"\n' % (user_name,p))
        netdb.makeobservation(user_name, p, list_name, datetime.datetime.now())
        if not visited.contains(p) :
            visited.insert(depth,p)
            queue.append(p)
    for p in queue :        
        recurse(depth+1,p,list_name)

def build(user,list_name) :
    """ Call this to start the crawler"""
    visited.insert(0,user)
    recurse(1,user,list_name)

# End of Phil's alternative



if __name__ == '__main__' :

    if args.cloud_file_name != None :
        cloudfile = open(args.cloud_file_name, 'w+')
        
    if args.dot_file_name != None:
        dotfile = open(args.dot_file_name, 'w+')

    if dotfile : dotfile.write("digraph G {\n")
    
    if (not args.w) :
        # Use James / Eli's original code
        netdb.setupdb()
        print buildList(args.seed_user, args.list_name)
        if args.net_file_name != None:
            graph = netdb.rendergraph(args.list_name)
            netfile = open(args.net_file_name, 'w+')
            netfile.write(graph)

    else :
        # my alternative (used in current web-based test)

        netdb.setupdb()
        
        visited = useful.SetDict() # a dictionary of sets. We're going to store one set for each "depth" (distance from the root)
        build(args.seed_user,args.list_name)

        if dotfile : dotfile.write("}\n")

        if args.net_file_name != None:
            graph = netdb.rendergraph(args.list_name)
            netfile = open(args.net_file_name, 'w+')
            netfile.write(graph)
        
        visited.pp(cloudfile)


