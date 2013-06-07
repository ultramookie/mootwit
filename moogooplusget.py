#!/usr/bin/python

# mootwit
# steve mookie kong
# licensed under gplv3
# http://www.gnu.org/licenses/gpl-3.0.html

import urllib
import json
import MySQLdb as mdb
import codecs
import pprint
import time

# database stuff populate as necessary
dbuser=''
dbpass=''
dbhost='localhost'
dbname='mootwit'

# google+ userid. this is embedded in the url when you view your own profile.
# example: https://plus.google.com/u/0/110018105057768386585/posts
# "110018105057768386585" is my userid.
user = ''

# google api key. https://developers.google.com/console/help/#generatingdevkeys
key = ''

# bootstrap or start from a last processed tweet id
bootcon = mdb.connect(dbhost,dbuser,dbpass,dbname)
bootcon.query("SELECT count(id) from mootwit where gid is NOT NULL")
result = bootcon.use_result()
bootcheck = result.fetch_row()[0][0]

if bootcheck == 0:
	sinceid = 0
else:
	sincecon = mdb.connect(dbhost,dbuser,dbpass,dbname)
	sincecon.query("SELECT id from mootwit where gid is NOT NULL order by id DESC limit 1")
	result = sincecon.use_result()
	sinceid = result.fetch_row()[0][0] + 1

# the base url
urlbase = 'https://www.googleapis.com/plus/v1/people/' + user + '/activities/public?key=' + key + '&fields=items(id,published,verb,object(content,attachments(url)))&prettyPrint=false&maxResults=100'

con = mdb.connect(dbhost,dbuser,dbpass,dbname)

url = urlbase

gplusreturn = urllib.urlopen(url)

gplusjson = json.loads(gplusreturn.read())

gplusvalues = gplusjson.values()
gplusitems = gplusvalues[0]

for item in gplusitems:
	if item['verb'] == 'share' or item['verb'] == 'post':
		gid = item['id']
		dt = time.strptime(item['published'],"%Y-%m-%dT%H:%M:%S.%fZ")
		udt = time.mktime(dt)
		unixts = int(udt)
		intext = item['object']['content']
                actualtext = intext.encode('ascii','ignore')
                if item['verb'] == 'share':
                        actualtext = "<b>Shared</b>: " + actualtext
		if unixts < sinceid:
			break
		if len(item['object']) > 1:
			numattach = len(item['object']['attachments'])
			for anum in range(0,numattach):
				url = item['object']['attachments'][anum]['url']
				urlcon = mdb.connect(dbhost,dbuser,dbpass,dbname)
				urlcur = urlcon.cursor()
				urlsql =  u"INSERT into moourls (tweetid,url,short) VALUES (%s,\"%s\",\"%s\")" % (unixts,url,url)
				urlcur.execute(urlsql)
		cur = con.cursor()
		sql = u"INSERT into mootwit (id,text,created_at,gid) VALUES (%s,\"%s\",FROM_UNIXTIME(%s),\"%s\")" % (unixts,mdb.escape_string(actualtext),unixts,gid)
		cur.execute(sql)
