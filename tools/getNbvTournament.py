#!/usr/bin/env python3

import requests, html2text, sys, os
from bs4 import BeautifulSoup
import xml.etree.cElementTree as ET

from io import BytesIO

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


            # class
            klasse = item.find('td', {'class': 'td_klasse'}).contents[0]

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

            # Meldedatum
            meldeschluss  = item.find('td', {'class': 'td_meldung'}).contents[0]
            deadline      = ET.SubElement(entry, "deadline")
            deadline.text = meldeschluss

            # Zeitraum
            zeitraum = item.find('td', {'class': 'td_datum'}).contents[0]
            if "/" in zeitraum:
                start, ende = zeitraum.split("/")
                if len(start) < 4:
                    start += ende[3:]
            else:
                start = zeitraum
                ende = start

            startdate = ET.SubElement(entry, "startdate")
            startdate.text = start
            enddate = ET.SubElement(entry, "enddate")
            enddate.text = ende

            #description
            description = ET.SubElement(entry, "description")
            description.text = "automatic import"



            # Art: NBV
            art = item.find('td', {'class': 'td_art'}).contents[0]
            tournamentType = ET.SubElement(entry, "tournamentType")
            tournamentType.text = tournamentTypeSwitcher ("FUN")

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
