#!/usr/bin/python

import urllib
import json
import MySQLdb as mdb
import codecs

# database stuff
dbuser=''
dbpass=''
dbhost='localhost'
dbname='mootwit'

# twitter username
user = 'ultramookie'
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
fields = ['id','created_at','text']

con = mdb.connect(dbhost,dbuser,dbpass,dbname)

# the base url
urlbase = 'https://api.twitter.com/1/statuses/user_timeline.json?screen_name=' + user + '&count=' + str(count) + '&trim_user=true'

for page in range(1,maxrounds + 1):
	url = urlbase + '&page=' + str(page)
	twitreturn = urllib.urlopen(url)
	twitjson = json.loads(twitreturn.read())
	if not twitjson:
		break
	for entity in twitjson:
		for field in fields:
			if field == 'id':
				tweetid = entity['id']
			if field == 'text':
				intext = entity['text']
				tweettext = intext.encode('ascii','ignore')
				print tweettext
			if field == 'created_at':
				datesplit = entity[field].rsplit()
				year = datesplit[5]
				mon = to_dict(datesplit[1])
				day = datesplit[2]
				time = datesplit[3]
				mdatetime = year + '-' + mon + '-' + day + ' ' + time
	
		cur = con.cursor()

		sql = u"INSERT into mootwit (id,text,created_at) VALUES (%s,\"%s\",\"%s\")" % (tweetid,mdb.escape_string(tweettext),mdatetime)

		cur.execute(sql)
