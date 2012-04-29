#!/usr/bin/python

import urllib
import json

month_dict = {"Jan":1,"Feb":2,"Mar":3,"Apr":4, "May":5, "Jun":6, "Jul":7,"Aug":8,"Sep":9,"Oct":10,"Nov":11,"Dec":12}

def to_dict(name):
   return month_dict[name]

fields = ['id','created_at','text']
user = 'ultramookie'
count = '20'

url = 'https://api.twitter.com/1/statuses/user_timeline.json?screen_name=' + user + '&count=' + count + '&trim_user=true'
twitreturn = urllib.urlopen(url)
twitjson = json.loads(twitreturn.read())
for entity in twitjson:
	for field in fields:
		if field == 'id':
			tweetid = entity['id']
		if field == 'text':
			tweettext = entity['text']
		if field == 'created_at':
			datesplit = entity[field].rsplit()
			year = datesplit[5]
			mon = to_dict(datesplit[1])
			day = datesplit[2]
			time = datesplit[3]

	print 'id: ' + str(tweetid) + '\ntext: ' + tweettext + '\ndate: ' + str(mon) + ' ' + day + ' ' + year + ' ' + time + '\n'
