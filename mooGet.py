#!/usr/bin/python

# mootwit
# steve mookie kong
# licensed under gplv3
# http://www.gnu.org/licenses/gpl-3.0.html

import json
import MySQLdb as mdb
import codecs
import pprint
import urllib2
import oauth2 as oauth

# database stuff populate as necessary
dbuser='DBUSER'
dbpass='DBPASS'
dbhost='localhost'
dbname='mootwit'

# twitter username
user = 'TWITTER_USER'

# twitter stuff
CONSUMER_KEY = "KEY"
CONSUMER_SECRET = "SECRET"
ACCESS_KEY = "ACCESS"
ACCESS_SECRET = "SECRET"

consumer = oauth.Consumer(key=CONSUMER_KEY, secret=CONSUMER_SECRET)
access_token = oauth.Token(key=ACCESS_KEY, secret=ACCESS_SECRET)
client = oauth.Client(consumer, access_token)

# number of entries to process at once
count = 200 
# hard limit on the number of entries from a user by twitter
hardlimit = 3200
# max rounds that can happen
maxrounds = hardlimit / count

# for converting twitter's months to numbers
month_dict = {'Jan':'01','Feb':'02','Mar':'03','Apr':'04','May':'05','Jun':'06','Jul':'07','Aug':'08','Sep':'09','Oct':'10','Nov':'11','Dec':'12'}
def to_dict(name):
   return month_dict[name]

# the fields i want
fields = ['id','created_at','text','entities']

# bootstrap or start from a last processed tweet id
bootcon = mdb.connect(dbhost,dbuser,dbpass,dbname)
bootcon.query("SELECT count(id) from mootwit")
result = bootcon.use_result()
bootcheck = result.fetch_row()[0][0]

if bootcheck == 0:
	sinceid = ''
else:
	sincecon = mdb.connect(dbhost,dbuser,dbpass,dbname)
	sincecon.query("SELECT id from mootwit order by id DESC limit 1")
	result = sincecon.use_result()
	sinceid = "&since_id=%s" % str(result.fetch_row()[0][0])

# the base url
urlbase = 'https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=' + user + '&count=' + str(count) + '&include_entities=true&trim_user=true' + sinceid

con = mdb.connect(dbhost,dbuser,dbpass,dbname)

for page in range(1,maxrounds + 1):
	url = urlbase + '&page=' + str(page)
	response, data = client.request(url)
	twitjson = json.loads(data)
	if not twitjson:
		break
	for entity in twitjson:
		for field in fields:
			if field == 'id':
				tweetid = entity['id']
			if field == 'text':
				intext = entity['text']
				tweettext = intext.encode('ascii','ignore')
			if field == 'created_at':
				datesplit = entity[field].rsplit()
				year = datesplit[5]
				mon = to_dict(datesplit[1])
				day = datesplit[2]
				time = datesplit[3]
				mdatetime = year + '-' + mon + '-' + day + ' ' + time
			if field == 'entities':
				if entity[field]['urls']:
					for urlnum in entity[field]['urls']:
			                        try:
							req = urllib2.urlopen(urlnum['expanded_url'])
						except urllib2.HTTPError, e:
							realurl = urlnum['expanded_url']
						except urllib2.URLError, e:
							realurl = urlnum['expanded_url']
						else:
							realurl = req.url
						urlcon = mdb.connect(dbhost,dbuser,dbpass,dbname)
						urlcur = urlcon.cursor()
						urlsql =  u"INSERT into moourls (tweetid,url,short) VALUES (%s,\"%s\",\"%s\")" % (tweetid,realurl,urlnum['url'])
						urlcur.execute(urlsql)
						urlcon.commit()
		cur = con.cursor()
		sql = u"INSERT into mootwit (id,text,created_at) VALUES (%s,\"%s\",\"%s\")" % (tweetid,mdb.escape_string(tweettext),mdatetime)
		cur.execute(sql)
		con.commit()
