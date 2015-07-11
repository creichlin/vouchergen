
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

  def updateSettings(self, **kwargs):
    """ update settings over webinterface, takes defaults from webinterface """
    
    data = {};
    for name, value in self.getSettings().items():
      if name in kwargs:
        data[name] = kwargs[name]
      else:
        data[name] = value

    self.__post("config.php", data)
    
  def getSettings(self):
    """ get all settings from webinterface and returns them in a dict """
    soup = self.__getAsSoup("config.php")
    data = {}
    
    for input in soup.select("input"):
      data[input["name"]] = input["value"]

    return data

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
  
  def __post(self, url, data, **kwargs):
    return self.session.post(self.__buildUrl(url, **kwargs), data = data).text
    
