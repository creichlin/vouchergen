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
    self.assertEqual(len(tables), 1)
    self.assertEqual(tables[0], 'aaa')
    
  def testCreateMultiple(self):
    self.wc.updateSettings(dbtables = "aaa|AAA\nbbb|BBB\nccc|CCC")
    tables = self.wc.getTablesFromStats()
    self.assertEqual(len(tables), 3)
    self.assertEqual(tables[0], 'aaa')
    self.assertEqual(tables[1], 'bbb')
    self.assertEqual(tables[2], 'ccc')
    
  def testImport(self):
    self.wc.importTickets('default', ['aaaaaa', 'bbbbbb', 'cccccc', 'dddddd'])
    stats = self.wc.getStats('default')
    self.assertEqual(stats['total'], 4)
    self.assertEqual(stats['used'], 0)
    self.assertEqual(stats['unused'], 4)
    
  def testPrint(self):  
    self.wc.importTickets('default', ['aaaaaa', 'bbbbbb', 'cccccc', 'dddddd'])
    self.wc.printPDF('default', 2)
    stats = self.wc.getStats('default')
    self.assertEqual(stats['total'], 4)
    self.assertEqual(stats['used'], 2)
    self.assertEqual(stats['unused'], 2)
    
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