import sqlite3
import sys
import datetime

conn = None
c = None

def getuserid( twitter_user ) :
    try:
        c.execute('select userid from users where twitter_user=?', (twitter_user,))
        l = c.fetchall()
        if len(l) != 1:
            if len(l) == 0:
                c.execute('''insert into users (twitter_user) values (?)''', (twitter_user,))
                conn.commit()
                c.execute('select userid from users where twitter_user=?', (twitter_user,))
                l = c.fetchall()
                return l[0]
            else:
                print "Too many rows for {0}".format(twitter_user)
                return -1
        else:
            return l[0]
    except:
        print "Unexpected error:", sys.exc_info()[0]
        print "Exception on user insert"
        return -1

def getnetid( net ) :
    try:
        c.execute('select netid from netids where twitter_list=?', (net,))
        l = c.fetchall()
        if len(l) != 1:
            if len(l) == 0:
                c.execute('''insert into netids (twitter_list) values (?)''', (net,))
                conn.commit()
                c.execute('select netid from netids where twitter_list=?', (net,))
                l = c.fetchall()
                return l[0]
            else:
                print "Too many rows for {0}".format(net)
                return -1
        else:
            return l[0]
    except:
        print "Unexpected error:", sys.exc_info()[0]
        print "Exception on net insert"
        return -1

def setupdb() :
    global conn
    global c

    conn = sqlite3.connect('trustdb.db')

    c = conn.cursor()

    print "Checking DB setup"
    try:
        c.execute('''create table if not exists observations (suserid integer, tuserid integer, netid integer, observation datetime)''')
        conn.commit()
    except:
        print "Exception on create observations"

    try:
        c.execute('''create table if not exists users (userid integer primary key, twitter_user text unique)''')
        conn.commit()
    except:
        print "Exception on create users"

    try:
        c.execute('''create table if not exists netids (netid integer primary key, twitter_list text unique)''')
        conn.commit()
    except:
        print "Exception on create netids"

    return 0

def makeobservation ( suser, tuser, net, obstime ) :

    suserid = getuserid(suser)
    tuserid = getuserid(tuser)
    netid = getnetid(net)

    try:
        c.execute("insert into observations values (?, ?, ?, ?)", (suserid[0],tuserid[0],netid[0],obstime))
        conn.commit()
    except:
        print "Unexpected error:", sys.exc_info()[0]
        print "Exception on observation insert"

def getlatestnet ( net ) :
    try:
        links = ""
        netid = getnetid(net)[0]
        c.execute("select distinct suserid, tuserid from observations where netid=?",(netid,))
        rows = c.fetchall()
        for row in rows:
            c.execute("select suserid, tuserid, max(observation) from observations where netid=? and suserid=? and tuserid=?",(netid,row[0],row[1],))
            link = c.fetchall()
            links += "    \"{0}\" -> \"{1}\";\n".format(getusername(link[0][0]),getusername(link[0][1]))
        return links
    except:
        print "Unexpected error:", sys.exc_info()[0]
        print "Exception pulling observations"

def getnetparticipants ( net ) :
    try:
        people = ""
        netid = getnetid(net)[0]
        c.execute("select suserid from observations where netid=? union select tuserid from observations where netid=?",(netid,netid,))
        rows = c.fetchall()
        for row in rows:
            people += "    \"{0}\" [label=\"{0}\" URL=\"javascript:void(window.open(\\\"http://twitter.com/#!/{0}\\\"))\"];\n".format(getusername(row[0]))
        return people
    except:
        print "Unexpected error:", sys.exc_info()[0]
        print "Exception pulling observations"

def getusername( id ) :
    try:
        c.execute("select twitter_user from users where userid=?",(id,))
        return c.fetchall()[0][0]
    except:
        print "Unexpected error:", sys.exc_info()[0]
        print "Exception pulling user name"

def rendergraph( name ) :
    graph = ""
    people = getnetparticipants (name)
    links = getlatestnet (name)
    graph += "digraph G {\n"
    graph += people
    graph += links
    graph += "}\n"

    return graph

