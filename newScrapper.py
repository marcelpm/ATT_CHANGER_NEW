#!/usr/bin/env python
from cStringIO import StringIO

from HTMLParser import HTMLParser
from htmldom import htmldom
import os


def getNodeText(rootDOM) :

	if rootDOM is None:
		return None

	isGood= 1;

	currentWords = []

	text = rootDOM.text().split('\n')
	wordCount = 0

	for word in text:
		if word != None and word != '':
		 	currentWords.append(word)
		 	wordCount += 1

	index = 0;
	children = rootDOM.children()
	while isGood == 1:

		child = children.eq(index);
		if child is None:
			isGood = -1
			break
		
		text = child.text().split('\n');
		wordCount = 0;
		if word != None and word != '':
			 currentWords.remove(word)

		index += 1

	return currentWords;


def parseToInt(stringIn):
	addVal = 0;
	number = 0;
	val = 0;
	for char in stringIn:

		try:
			addVal = int(char)
			val = val*10;
			val = val + addVal
		except ValueError:
			pass
	return val




root_dir = "contact";

sub_dirs = os.listdir(root_dir);



newFileName = 'tempCsv2'

def getFilename(filename, extension):

	number = 1
	while os.path.exists(filename+'.'+extension):
		filename += str(number)
	return filename;


newFile = getFilename(newFileName, 'csv')+'.csv'

fp = open(newFile, "w+")

fp.write("email,name, jobTitle, calledToBar,companyName,streetAddress,city,province,postalCode,AreasOfPractice\n")	

for current_dir in sub_dirs:

	current_file = open("contact/"+current_dir+"/"+os.listdir("contact/"+current_dir)[0], "r")
	
	file_text = current_file.read()

	dom = htmldom.HtmlDom().createDom(file_text)
	
	current_file.close()
	


	name=dom.find("div.listingdetail_individualname > h1 > span").text()

	jobTitle = dom.find("span[itemprop=JobTitle]").text()

	calledToBar_e = dom.find(".listingdetail_individualmaininfo")



	calledToBar = parseToInt(calledToBar_e.text());

	companyName = dom.find("a[title=companylink]").text()

	streetAddress = dom.find("span[itemprop=streetAddress] > span > div").text()

	city = dom.find("span[itemprop=addressLocality]").text()
	province = dom.find("span[itemprop=addressRegion]").text()
	postalCode = dom.find("span[itemprop=postalCode]").text()

	phoneNumber = dom.find("span[itemprop=telephone]").text()
	faxNumber = dom.find("span[itemprop=faxNumber]").text()
	email = dom.find("span[itemprop=email] > a").text()

	
	table = dom.find("table")


	mainBlock = dom.find("div#main_block")


	table = mainBlock.find("table")


	rows = table.find("tr")
	
	AreasOfPractice = ''

	while rows.html() != '':
		col = rows.find("td").eq(0)
		if col and "Areas of Practice" in col.text():
			AreasOfPractice = rows.find("td").eq(1).text()
			break;
		rows = rows.next()


	if AreasOfPractice is None:
		AreasOfPractice = ''

	varList = [email, name, jobTitle, calledToBar, companyName,streetAddress, city, province, postalCode, AreasOfPractice]

	def processVars(theList):
		newList = []

		for i in theList:
			if i is None:
				newList.append('')
			else:
				newList.append(str(i).replace(',', ''))
		return newList


	varList = processVars(varList);

	fp.write("\""+email+"\"")


	for i in varList[1:]:
		if varList[1] == 'Sam Hall':
			print i
		fp.write(",\""+i+"\"")
	fp.write("\n")
	


fp.close()