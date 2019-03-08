#!/usr/bin/env python3

import requests, html2text, sys, os
from bs4 import BeautifulSoup
import xml.etree.cElementTree as ET

from io import BytesIO
from datetime import date

"""
    TournamentTypeSwitcher
"""
def tournamentTypeSwitcher(argument):
    switcher = {
        "nbv"   : "NBV",
        "privat": "FUN",
        "fun"   : "FUN",
    }
    return switcher.get(argument.lower(), "OTHER")

"""
    START
"""

url = 'https://nbv-online.de/index.php/termine-oben#erwachsenenturniere'.rstrip()
headers = {
    'User-Agent': 'My User Agent 1.0',
    'From': 'youremail@domain.com'  # This is another valid field
}
response = requests.get(url, headers=headers)

if response.status_code != requests.codes.ok:
    print("404: File not found");
    sys.exit(1)
else:
    root = ET.Element("root")
    soup = BeautifulSoup(response.content, "html.parser")
    filter = soup.find('div', {'id': 'erwachsenenturniere'})
    for item in filter.find_all('tr', {'class' : 'tabrow'}):
        if 'tr_ht1' not in item.attrs['class']:
            entry = ET.SubElement(root, "entry")

            descriptionStr = ""


            # class
            ClassificationValue      = item.find('td', {'class': 'td_klasse'}).contents[0]
            ClassificationField      = ET.SubElement(entry, "classification")
            ClassificationField.text = ClassificationValue

            # title
            title = item.find('td', {'class': 'td_titel'})
            link_url = ""
            try:
                link      = title.find('a')
                link_name = link.contents[0]
                link_url  = link['href']
            except Exception as e:
                link_name = title.contents[0]

            name      = ET.SubElement(entry, "title")
            name.text = link_name

            link      = ET.SubElement(entry, "link")
            link.text = link_url


            # ort td_ort
            ort        = item.find('td', {'class': 'td_ort'}).contents[0]
            place      = ET.SubElement(entry, "place")
            place.text = ort


            # Zeitraum
            zeitraum = item.find('td', {'class': 'td_datum'}).contents[0]
            if "/" in zeitraum:
                startDateValue, endDateValue = zeitraum.split("/")
                if len(startDateValue) < 4:
                    startDateValue += endDateValue[3:]
            else:
                startDateValue = zeitraum
                endDateValue   = startDateValue

            startDateField      = ET.SubElement(entry, "startdate")
            startDateField.text = startDateValue
            endDateField        = ET.SubElement(entry, "enddate")
            endDateField.text   = endDateValue

            # Meldedatum
            try:
                deadlineValue   = item.find('td', {'class': 'td_meldung'}).contents[0]
            except Exception as e:
                deadlineValue   = startDateValue
                descriptionStr += "Fehler beim Meldeschluss"

            deadlineField      = ET.SubElement(entry, "deadline")
            deadlineField.text = deadlineValue


            #description
            descriptionStr       += "<br>Automatic import {0}".format(str(date.today()))
            descriptionField      = ET.SubElement(entry, "description")
            descriptionField.text = descriptionStr



            # Art: NBV
            art = item.find('td', {'class': 'td_art'}).contents[0]
            tournamentTypeField = ET.SubElement(entry, "tournamentType")
            tournamentTypeField.text = tournamentTypeSwitcher (art)

            #print("Titel: {:s}".format(link_name))
            #print("URL: {:s}".format(link_url))
            #print("Ort: {:s}".format(ort))
            #print("Start: {:s}".format(start))
            #print("Ende: {:s}".format(ende))
            #print("Meldeschluss: {:s}".format(meldeschluss))
            #print("Art: {:s}".format(art))
            #print("")

    #print(ET.tostring(root, encoding='utf8', xml_declaration=True)) # method='xml'))
    #print(ET.tostring(root, encoding='utf8', xml_declaration=True, method='xml'))

    tree = ET.ElementTree(root)
    f = BytesIO()
    tree.write(f, encoding='utf-8', xml_declaration=True)
    print(str(f.getvalue(), "utf-8"))
