#!/usr/bin/env python

from HTMLParser import HTMLParser
from htmldom import htmldom
import os

root_dir = "contact";

sub_dirs = os.listdir(root_dir);

for current_dir in sub_dirs:

	current_file = open("contact/"+current_dir+"/"+os.listdir("contact/"+current_dir)[0], "r")
	
	file_text = current_file.read();

	dom = htmldom.HtmlDom().createDom(file_text)
	
	current_file.close()
	
	name=dom.find("div.listingdetail_individualname > h1 > span").text()

	individual_main_info = dom.find("div.listingdetail_individualmaininfo")

	job_title = individual_main_info.find("div > i > span").text()

	#print(job_title)
	#need to parse out the (BC) at end
	called_to_bar_string = individual_main_info.text()

	call_to_bar_int_string = []
	for t in called_to_bar_string.split():
		try:
			call_to_bar_int_string.append(int(t))
		except ValueError:
			pass

	company_item = dom.find("div.company-item")

	company_name = company_item.find("div.listingdetail_companyname > h2 > span > a").text()

	contact_info = company_item.find("div.listingdetail_contactinfo")

	street_address_div = contact_info.find("div > span")

	street_address = street_address_div.eq(0).text

	city = contact_info.find("div > div > span[itemprop=addressLocality]").text()

	province = contact_info.find("div > div > span[itemprop=addressRegion]").text()

	postal_code = contact_info.find("div > div > span[itemprop=postalCode]").text()

	print(street_address)
	#phone_prop = company_item.find("div > span[itemprop=telephone]")
	
	#print(type(phone_prop))
	#phoneNumber =  phone_prop.text()

	#fax_number = company_item.find("div > span[itemprop=faxNumer]").text()

	#email = company_item.find("div > span[itemprop=email]").text()

	data_table = dom.find("table.listingdetail_enchanceddata")
	number = 1

	rows = data_table.find("tr")

	# print(data_table)
	# for table_row in rows:
	# 	print('hi');
	# 	first_col_text = table_row.find("td").first().text()
		
	# 	number = number + 1 
	# 	if number < 4:
	# 		print(table_row.text())

	# 	if "Areas of Practice" in first_col_text:
	# 		areas_of_practise = table_row.find("td").second().text()