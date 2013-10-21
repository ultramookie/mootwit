#!/usr/bin/python

# mooappnetget
# steve mookie kong
# licensed under gplv3
# http://www.gnu.org/licenses/gpl-3.0.html

import time
import re
import urllib2
import feedparser
import MySQLdb as mdb
import codecs
from BeautifulSoup import BeautifulSoup

# database stuff populate as necessary
dbuser=''
dbpass=''
dbhost='localhost'
dbname='mootwit'

# RSS Feed for App.net user
rss = ""

# bootstrap or start from a last processed tweet id
bootcon = mdb.connect(dbhost,dbuser,dbpass,dbname)
bootcon.query("SELECT count(id) from mootwit where anid is NOT NULL")
result = bootcon.use_result()
bootcheck = result.fetch_row()[0][0]

if bootcheck == 0:
        sinceid = 0
else:
        sincecon = mdb.connect(dbhost,dbuser,dbpass,dbname)
        sincecon.query("SELECT id from mootwit where anid is NOT NULL order by id DESC limit 1")
        result = sincecon.use_result()
        sinceid = result.fetch_row()[0][0] + 1

con = mdb.connect(dbhost,dbuser,dbpass,dbname)

feed = feedparser.parse(rss)

for item in feed.entries:
	realtext = ""
	link = item.link
	text = str(item.description).replace("photos.app.net","http://photos.app.net")
	body = ' '.join(BeautifulSoup(text).findAll(text=True))
	words = body.split(" ")
	for word in words:
		if re.match("^(http|https)://",word):
			try:
				req = urllib2.urlopen(word)
			except urllib2.HTTPError, e:
				word = word
			except urllib2.URLError, e:
				word = word
			else:
				word = req.url
		realtext = realtext + " " + word
	actualtext = realtext.encode('ascii','ignore').strip()
	dt = time.strptime(item.published, "%a, %d %b %Y %H:%M:%S -0000")
	udt = time.mktime(dt)
	unixts = int(udt)
        anid = item.link.split('/')[-1]
	if unixts < sinceid:
		break
	cur = con.cursor()
	sql = u"INSERT into mootwit (id,text,created_at,anid) VALUES (%s,\"%s\",FROM_UNIXTIME(%s),\"%s\")" % (unixts,mdb.escape_string(actualtext),unixts,anid)
	cur.execute(sql)	
