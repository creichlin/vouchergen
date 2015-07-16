
import textwrap
import requests;
from bs4 import BeautifulSoup


class WebClient():

  def __init__(self, url, username, password):
    self.url = url
    self.username = username
    self.password = password

    self.session = None

    self.__login()
    
  def importTickets(self, db, tickets):
    file = "#\n" * 7
    
    for t in tickets:
      file += '" ' + t + '"\n'
    
    data = {
            'select_upload': db,
            'submit_upload': 'upload'
            }
    
    self.__post("index.php", data, files = {'datei': ('file.csv', file)})
    
  def printPDF(self, table, number):
    data = {
            'submit_print': '',
            'number': '%s' % (number, ),
            'select_print': table
            }
    self.__post('print.php', data)

  def updateSettings(self, **kwargs):
    """ update settings over webinterface, takes defaults from webinterface """
    
    data = {'submit': 'submit'}
    for name, value in self.getSettings().items():
      if name in kwargs:
        data[name] = kwargs[name]
      else:
        data[name] = value
        
    self.__post("config.php", data)
    
  def sendSms(self, number):
    """ send an sms in the sms test screen """
    return self.__postAsSoup('sms.php', {'send': '', 'config': '0', 'nummer': number})
    
  def isLocked(self, number):
    """ check if phine number is locked """
    soup = self.__postAsSoup('sms.php', {'test': '', 'config': '0', 'nummer': number})
    return not not soup.find("div", {'message-id': 'number-is-not-allowed'})
    
    
  def getSendtSms(self):  
    """ fetch sendt sms from the sms test provider screen """
    soup = self.__getAsSoup('testSmsProvider.php')
    entries = []
    for entry in soup.select('.message'):
      entries.append(MessageEntry(entry.select('.number')[0].get_text()))
    
    return entries
    
  def getSettings(self):
    """ get all settings from webinterface and returns them in a dict """
    soup = self.__getAsSoup("config.php")
    data = {}
    
    for input in soup.select("input"):
      data[input["name"]] = input["value"]

    for input in soup.select("textarea"):
      data[input["name"]] = input.get_text()

    return data
  
  def getTablesFromStats(self):
    soup = self.__getAsSoup("statistik.php")
    tables = []
    for row in soup.select('table > tr')[1:]:
      tables.append(row['table-id'])
    return tables
  
  def getStats(self, table):
    soup = self.__getAsSoup("statistik.php")
    selector = 'tr[table-id=%s]' % (table, )
    row = soup.find("tr", {'table-id': table}).findAll("td")
    
    return {'total' : int(row[1].get_text()),
            'unused': int(row[2].get_text()),
            'used': int(row[3].get_text())}
  
  def disconnect(self):
    self.__getAsText("logout.php")
    self.session.close()

  def __login(self):
    """ logging into the web interface """
    self.session = requests.Session();

    self.__post("login.php", data = {
      'passwort': self.password,
      'username': self.username
    })
    
  def __buildUrl(self, url, **kwargs):
    params = {'url': self.url}
    params.update(kwargs)
    return ("{url}" + url).format(**params)

  def __getAsText(self, url, **kwargs):
    response = self.session.get(self.__buildUrl(url, **kwargs))
    return response.text

    
  def __getAsSoup(self, url, **kwargs):
    response = self.__getAsText(url, **kwargs)
    return BeautifulSoup(response)
  
  def __post(self, url, data, files = {}, **kwargs):
    return self.session.post(self.__buildUrl(url, **kwargs), data = data, files = files).text
    
  def __postAsSoup(self, url, data, files = {}, **kwargs):
    response = self.__post(url, data, files, **kwargs)
    return BeautifulSoup(response)
    



class MessageEntry():
  def __init__(self, number):
    self.number = number

