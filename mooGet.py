#!/usr/bin/python

import urllib
import json

month_dict = {'Jan':'01','Feb':'02','Mar':'03','Apr':'04','May':'05','Jun':'06','Jul':'07','Aug':'08','Sep':'09','Oct':'10','Nov':'11','Dec':'12'}

def to_dict(name):
   return month_dict[name]

fields = ['id','created_at','text']
user = 'ultramookie'
count = '200'

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
			mdatetime = year + '-' + mon + '-' + day + ' ' + time

	print 'id: ' + str(tweetid) + '\ntext: ' + tweettext + '\ndate: ' + mdatetime + '\n'
