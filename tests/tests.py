import webclient
import unittest
import mysql.connector

class TestBase(unittest.TestCase):

  def setUp(self):
    self.wc = webclient.WebClient('http://vogen.local/', 'admin', 'vogen');
    
    self.connection = mysql.connector.connect(host = 'localhost', user = 'voucher', password = 'voucher', database = 'voucher')

    cursor = self.connection.cursor(prepared = True)
    cursor.execute("show tables")
    
    tables = [row[0].decode('utf-8') for row in cursor]
    
    for tn in tables:
      cursor.execute('drop table `%s`' % (tn, )) 
      
    
    cursor.close()
    self.connection.commit()
    
  def tearDown(self):
    self.wc.disconnect()


class TestDatabases(TestBase):
  
  def testCreate(self):
    self.wc.updateSettings(dbtables = "aaa|AAA")
    tables = self.wc.getTablesFromStats()
    self.assertEquals(len(tables), 1)
    self.assertEquals(tables[0], 'aaa')
    
  def testCreateMultiple(self):
    self.wc.updateSettings(dbtables = "aaa|AAA\nbbb|BBB\nccc|CCC")
    tables = self.wc.getTablesFromStats()
    self.assertEquals(len(tables), 3)
    self.assertEquals(tables[0], 'aaa')
    self.assertEquals(tables[1], 'bbb')
    self.assertEquals(tables[2], 'ccc')

class TestConfig(TestBase):

  def testSimpleConfigs(self):
    self.wc.updateSettings(vou_header = 'HEADER', vou_text = 'TEXT', vou_label = 'LABEL')
    settings = self.wc.getSettings()
    
    self.assertEqual(settings['vou_header'], 'HEADER')
    self.assertEqual(settings['vou_text'], 'TEXT')
    self.assertEqual(settings['vou_label'], 'LABEL')
    
    self.wc.updateSettings(vou_header = 'FOO', vou_text = 'Bar', vou_label = 'foooooBar')
    settings = self.wc.getSettings()
    
    self.assertEqual(settings['vou_header'], 'FOO')
    self.assertEqual(settings['vou_text'], 'Bar')
    self.assertEqual(settings['vou_label'], 'foooooBar')
    
    
    
if __name__ == '__main__':
  unittest.main()